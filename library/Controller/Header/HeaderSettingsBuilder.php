<?php

namespace Municipio\Controller\Header;

/**
 * Builds upper and lower header settings for the flexible header.
 */
class HeaderSettingsBuilder
{
    /**
     * Constructor.
     */
    public function __construct(
        private object $customizer,
        private HeaderClasses $headerClasses,
        private HeaderAttributes $headerAttributes,
    ) {
    }

    /**
     * Build upper and lower header settings.
     *
     * @param array<string, mixed> $upperItems Upper header items.
     * @param array<string, mixed> $lowerItems Lower header items.
     * @param array<string, mixed> $logoScrollShrink
     *
     * @return array{0: array<string, mixed>, 1: array<string, mixed>}
     */
    public function build(array $upperItems, array $lowerItems, array $logoScrollShrink = []): array
    {
        $upperHeader = [];
        $lowerHeader = [];

        if (!empty($this->customizer->headerSticky)) {
            $upperHeader['sticky'] = empty($lowerItems['modified']) ? true : false;
            $lowerHeader['sticky'] = empty($upperHeader['sticky']);
        }

        $lowerHeaderHasMegaMenu = $this->hasMegaMenu($lowerItems);
        $upperHeaderHasMegaMenu = $this->hasMegaMenu($upperItems);

        $lowerHeader['innerMegaMenu'] = $lowerHeaderHasMegaMenu && !empty($lowerHeader['sticky']);
        $upperHeader['innerMegaMenu'] = $upperHeaderHasMegaMenu && !empty($upperHeader['sticky']);

        $upperHeader['classList'] = $this->headerClasses->getHeaderClasses($upperItems, $logoScrollShrink);
        $lowerHeader['classList'] = $this->headerClasses->getHeaderClasses($lowerItems, $logoScrollShrink);
        $upperHeader['classList'][] = !empty($upperItems['modified']['center']) ? 'c-header--flexible-has-centered-content' : '';
        $lowerHeader['classList'][] = !empty($lowerItems['modified']['center']) ? 'c-header--flexible-has-centered-content' : '';
        $upperHeader['attributeList'] = $this->headerAttributes->getHeaderAttributes('upper', $logoScrollShrink);
        $lowerHeader['attributeList'] = $this->headerAttributes->getHeaderAttributes('lower', $logoScrollShrink);

        return [
            array_merge($this->defaultHeaderSettings(), $upperHeader),
            array_merge($this->defaultHeaderSettings(), $lowerHeader),
        ];
    }

    /**
     * Default settings.
     *
     * @return array<string, mixed>
     */
    private function defaultHeaderSettings(): array
    {
        return [
            'sticky' => false,
            'classList' => [],
            'attributeList' => [],
        ];
    }

    /**
     * Checks if the mega menu is present in the menu.
     *
     * @param array<string, mixed> $items Header items.
     */
    private function hasMegaMenu(array $items): bool
    {
        return isset($items['desktop']['mega-menu']) || isset($items['mobile']['mega-menu']);
    }
}