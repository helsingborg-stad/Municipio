#!/usr/bin/env python3
"""Export every named Material Symbol in the bundled WOFF2 fonts as SVG.

Run `npm run build:icons` after installing requirements-icons.txt. The SVGs
are generated assets; they are not loaded by the theme until a renderer uses
them. Use --names for a small build while developing.
"""

from __future__ import annotations

import argparse
import hashlib
import json
import re
import shutil
import sys
import tempfile
from pathlib import Path
from xml.etree import ElementTree

try:
    from fontTools.pens.svgPathPen import SVGPathPen
    from fontTools.ttLib import TTFont
    from fontTools.varLib.instancer import instantiateVariableFont
except ImportError as exc:
    raise SystemExit(
        "Missing Python dependencies. Run: python3 -m pip install -r requirements-icons.txt"
    ) from exc


ROOT = Path(__file__).resolve().parent.parent
FONT_ROOT = ROOT / "node_modules" / "material-symbols"
DEFAULT_OUTPUT = ROOT / "assets" / "dist" / "icons" / "material-symbols"
WEIGHTS = (200, 400, 600)
STYLES = ("outlined", "rounded", "sharp")
OPTICAL_SIZE = 32
NAME_PATTERN = re.compile(r"[a-z0-9_]+\Z")
SVG_NS = "http://www.w3.org/2000/svg"


def font_path(style: str) -> Path:
    return FONT_ROOT / f"material-symbols-{style}.woff2"


def icon_names(font: TTFont) -> dict[str, str]:
    """Map ligature text to its output glyph, including font aliases."""
    cmap = font.getBestCmap()
    glyph_to_character = {
        glyph: chr(codepoint)
        for codepoint, glyph in cmap.items()
        if chr(codepoint) in "abcdefghijklmnopqrstuvwxyz0123456789_"
    }
    names: dict[str, str] = {}

    for lookup in font["GSUB"].table.LookupList.Lookup:
        for subtable in lookup.SubTable:
            if lookup.LookupType == 7:  # Extension substitution
                subtable = subtable.ExtSubTable
            for first, ligatures in getattr(subtable, "ligatures", {}).items():
                for ligature in ligatures:
                    characters = [glyph_to_character.get(glyph) for glyph in (first, *ligature.Component)]
                    if all(characters):
                        name = "".join(characters)
                        if NAME_PATTERN.fullmatch(name):
                            if name in names and names[name] != ligature.LigGlyph:
                                raise ValueError(f"Ambiguous ligature: {name}")
                            names[name] = ligature.LigGlyph
    if not names:
        raise ValueError("No Material Symbol ligatures found in font")
    return names


def picker_names() -> set[str]:
    """Names in the installed material-symbols package, used by the ACF picker."""
    declaration = ROOT / "node_modules" / "material-symbols" / "index.d.ts"
    if not declaration.is_file():
        raise FileNotFoundError(f"Picker catalogue missing: {declaration}")
    return set(re.findall(r'^\s+"([a-z0-9_]+)"\s*,?$', declaration.read_text(), re.MULTILINE))


def svg_for_glyph(font: TTFont, glyph_name: str) -> str:
    glyph_set = font.getGlyphSet()
    pen = SVGPathPen(glyph_set)
    glyph_set[glyph_name].draw(pen)
    path_data = pen.getCommands()
    if not path_data:
        raise ValueError(f"Empty glyph: {glyph_name}")

    units = font["head"].unitsPerEm
    width = font["hmtx"].metrics[glyph_name][0]
    svg = ElementTree.Element(
        "svg",
        {
            "xmlns": SVG_NS,
            "viewBox": f"0 0 {width} {units}",
            "fill": "currentColor",
            "aria-hidden": "true",
        },
    )
    ElementTree.SubElement(
        svg,
        "path",
        {
            "transform": f"translate(0 {units}) scale(1 -1)",
            "d": path_data,
        },
    )
    return ElementTree.tostring(svg, encoding="unicode") + "\n"


def export(output: Path, requested_names: set[str] | None) -> int:
    fonts: dict[str, tuple[TTFont, dict[str, str], str]] = {}
    for style in STYLES:
        path = font_path(style)
        if not path.is_file():
            raise FileNotFoundError(f"Font missing: {path}. Run npm install first.")
        font = TTFont(path)
        names = icon_names(font)
        digest = hashlib.sha256(path.read_bytes()).hexdigest()
        fonts[style] = (font, names, digest)

    all_names = set(next(iter(fonts.values()))[1])
    for style, (_, names, _) in fonts.items():
        if set(names) != all_names:
            missing = sorted(all_names - set(names))
            extra = sorted(set(names) - all_names)
            raise ValueError(f"Catalogue mismatch in {style}: missing={missing[:10]}, extra={extra[:10]}")

    missing_picker_names = picker_names() - all_names
    if missing_picker_names:
        raise ValueError(f"Picker icons missing from fonts: {sorted(missing_picker_names)}")
    if requested_names is not None:
        unknown = requested_names - all_names
        if unknown:
            raise ValueError(f"Unknown icon names: {sorted(unknown)}")
        names_to_export = sorted(requested_names)
    else:
        names_to_export = sorted(all_names)

    output.parent.mkdir(parents=True, exist_ok=True)
    staging = Path(tempfile.mkdtemp(prefix="material-symbols-", dir=output.parent))
    try:
        for style, (font, names, _) in fonts.items():
            axes = {axis.axisTag for axis in font["fvar"].axes} if "fvar" in font else set()
            if axes != {"FILL", "GRAD", "opsz", "wght"}:
                raise ValueError(f"Unexpected axes in {style}: {axes}; review the generator")
            for weight in WEIGHTS:
                # The separate @material-symbols/font-* packages are fixed at
                # 48 px optical size. A 32 px optical size more closely matches
                # the font's apparent stroke weight across 16-32 px UI icons.
                base_instance = instantiateVariableFont(
                    font, {"GRAD": 0, "opsz": OPTICAL_SIZE, "wght": weight}, inplace=False
                )
                for filled in (0, 1):
                    instance = base_instance if filled == 0 else instantiateVariableFont(
                        base_instance, {"FILL": 1}, inplace=False
                    )
                    directory = staging / style / str(weight) / str(filled)
                    directory.mkdir(parents=True)
                    for name in names_to_export:
                        (directory / f"{name}.svg").write_text(
                            svg_for_glyph(instance, names[name]), encoding="utf-8"
                        )

        manifest = {
            "format": 2,
            "iconCount": len(names_to_export),
            "variants": {"styles": list(STYLES), "weights": list(WEIGHTS), "filled": [0, 1], "opticalSize": OPTICAL_SIZE},
            "fonts": {
                style: digest
                for style, (_, _, digest) in fonts.items()
            },
            "icons": names_to_export,
        }
        (staging / "manifest.json").write_text(json.dumps(manifest, indent=2) + "\n", encoding="utf-8")

        # Replace a previous generated directory only after a complete build.
        previous = None
        if output.exists():
            if not (output / "manifest.json").is_file():
                raise ValueError(f"Refusing to replace a directory without a generated manifest: {output}")
            previous = output.with_name(output.name + ".previous")
            if previous.exists():
                raise ValueError(f"Previous build backup exists: {previous}")
            output.rename(previous)
        try:
            staging.rename(output)
        except OSError:
            if previous is not None:
                previous.rename(output)
            raise
        if previous is not None:
            shutil.rmtree(previous)
    finally:
        if staging.exists():
            shutil.rmtree(staging)

    return len(names_to_export)


def main() -> None:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--output", type=Path, default=DEFAULT_OUTPUT, help="Generated SVG directory")
    parser.add_argument("--names", nargs="+", help="Export only these names (for development)")
    args = parser.parse_args()
    names = set(args.names) if args.names else None
    if names and any(not NAME_PATTERN.fullmatch(name) for name in names):
        parser.error("Icon names may contain only lowercase letters, digits, and underscores")
    try:
        count = export(args.output.resolve(), names)
    except (FileNotFoundError, ValueError, KeyError) as exc:
        raise SystemExit(str(exc)) from exc
    print(f"Exported {count} icons in 18 variants to {args.output}")


if __name__ == "__main__":
    main()
