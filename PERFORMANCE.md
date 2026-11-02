# Helsingborg frontend performance — 2026-10-30

**Latest iteration:** [rendering-work changes and verification](/Users/seno1000/www/public/dev/performance-checks/ITERATION2.md).
Style-recalculation time is 16% lower and layout time 12% lower in the dedicated
diagnostic; current FCP/LCP/TBT measurements show no significant additional gain.
The older report below is historical, not the latest measurement.

**Final delivery:** [complete changes and current measurements](/Users/seno1000/www/public/dev/performance-checks/RESULTS.md).
The report below is retained as experiment history. The final five-run median
is 22 ms TBT, 191 ms FCP/LCP after subtracting TTFB per run, and zero CLS.
CSS remains unchanged. The additional header reflow experiment was reverted;
only demonstrated payload/dependency reductions are retained.

## Retained changes

- Preserve separate CSS loading: the final HAR has the **same 62 CSS URLs and
  744,210 CSS bytes**. No CSS bundling is retained.
- The map and event plugins own their runtime loading. Map/event modules declare
  their requirements; event pages, shortcodes, and event HTML blocks load the
  event runtime. Late-rendered event content prints its scripts and localization
  if the footer has already been captured. No theme markup processor removes
  plugin scripts. Custom integrations can enqueue `event-integration` directly
  or use the plugin's `EventManagerIntegration/enqueueFrontendScript` filter.
- Remove jQuery and WordPress API Fetch dependencies from the native-JavaScript
  shell and posts list. REST settings are localized on the shell. Other
  components retain their own declared dependencies.
- In **styleguide-new**, stop forcing development output during production
  builds. Initialize distant sliders within 600 px of the viewport; visible
  sliders remain eager, with eager fallback without IntersectionObserver.
- Keep the earlier lazy navigation-preloader request. **Automatic REST nonce
  refresh has been restored**, including its DOMContentLoaded path. Scripts
  consume the shared nonce directly and do not implement refresh/retry, so
  removing this request was not safe for cached pages.

## Repeated measurements

The table below records the earlier optimization experiment before nonce refresh
was restored and ownership moved into the plugins. It is retained as historical
evidence, not a fresh measurement of the current implementation. A verification
run for the corrected implementation is retained at
`/private/tmp/helsingborg-vitals-plugin-owned/index.html`.

Three cold-browser runs per version, Sitespeed 42.7.0 / Chrome 156, images
disabled, 4× CPU slowdown, native local networking. Exclude TTFB by subtracting
it **within each run**, then taking the median. These are laboratory results,
not production field measurements.

| Metric | Baseline | Optimized | Difference |
| --- | ---: | ---: | ---: |
| Median TBT | 44 ms | 19 ms | **57% lower** |
| JavaScript content | 953,396 B | 452,368 B | **53% less** |
| JavaScript transfer including headers | 280,372 B | 146,804 B | **48% less** |
| Median FCP minus TTFB | 196 ms | 192 ms | 2% lower; not significant |
| Median LCP minus TTFB | 196 ms | 192 ms | 2% lower; not significant |
| CLS | 0 | 0 | Preserved |
| Total requests | 103 | 100 | 3 fewer |
| CSS requests | 62 | 62 | Identical URLs |

Per-run FCP/LCP minus TTFB: baseline **238 / 196 / 192 ms**; optimized
**195 / 192 / 190 ms**. TBT: baseline **47 / 44 / 39 ms**; optimized
**19 / 16 / 22 ms**. CLS is zero in every run.

The earlier claim that the page had reached a rendering floor was unsupported
by the single unthrottled run. These repeated measurements demonstrate
substantial JavaScript and blocking-time reductions, but do **not** establish
a substantial FCP/LCP improvement.

### Command and retained reports

```sh
sitespeed.io http://localhost:8080/helsingborg -b chrome -n 3 --headless \
  --chrome.args='--blink-settings=imagesEnabled=false' \
  --chrome.CPUThrottlingRate 4 --chrome.collectConsoleLog true \
  --pageLoadStrategy eager \
  --pageCompleteCheck='return performance.now() > 10000 && document.readyState !== "loading"' \
  --outputFolder /private/tmp/helsingborg-vitals-optimized
```

Reports include HAR and screenshots:

- Baseline: `/private/tmp/helsingborg-vitals-before/index.html`
- Retained result: `/private/tmp/helsingborg-vitals-optimized/index.html`
- Plugin/runtime experiment and separate CPU profile:
  `/private/tmp/helsingborg-vitals-final/index.html`
- Rejected header-offset scheduling experiment:
  `/private/tmp/helsingborg-vitals-paint/index.html`. Its small paint difference
  was insufficient to justify changing initialization timing; it was reverted.

## Rebuild and delivery

The slider and production-mode fixes are tracked in
`/Users/seno1000/www/public/styleguide-new`, not ignored vendor source.
Run `npm run build` there, then from the Municipio theme:

```sh
MUNICIPIO_STYLEGUIDE_RUNTIME_DIST=/Users/seno1000/www/public/styleguide-new/assets/dist \
  npm run build:municipio
```

This optional override copies JavaScript artifacts and merges only JavaScript
manifest entries, preserving installed CSS and CSS manifest entries. Ordinary
theme builds use the installed Composer styleguide package. Deliver the
styleguide fixes through its package release or the explicit runtime-build
override so they survive deployment.

The plugin source changes are in the installed copies of
`modularity-open-street-map` and `api-event-manager-integration`. Because those
directories are ignored by this install repository, reviewable patches are also
saved under `/Users/seno1000/www/public/dev/performance-checks/plugin-patches/`.
Apply them to the corresponding plugin repositories for package delivery.

Plugin integration checks (no stored-content changes):

```sh
wp --path=wp --url=http://localhost:8080/helsingborg eval-file performance-checks/plugin-assets.php
```

The checks cover absent/present feature requirements, event shortcodes, single
event pages, and late block rendering with localization and deduplication.
Nonce startup tests verify both loading and already-ready document states.

### Ownership/nonce correction verification

- Full Municipio PHP suite: 1906 tests / 3399 assertions passed after removal of
  the theme processor and its tests.
- Nonce startup regression tests: 2 passed.
- Native WordPress plugin enqueue/late-rendering checks: 15 passed.
- Theme production build completed with the styleguide runtime override.
- Fresh image-disabled Sitespeed verification: 101 requests, automatic nonce
  refresh returns HTTP 200, no map/event runtime requests on the front page,
  TBT 25 ms and CLS 0. This single run verifies the corrected behavior; it is not
  a replacement for the earlier three-run performance comparison.

## Verification

- Baseline and optimized desktop screenshots have identical SHA-256:
  `40c7fb68681857d9941b6159d21e928999cec616b1b38ea96ef4f21b6484fb70`.
- Real Chrome checks at 1366 px and 390 px: slider initially unmounted,
  mounts on scroll, next-slide control works, height unchanged
  (566.39 px desktop / 213.28 px mobile).
- Slider scheduling tests: 3 passed (visible, distant, unsupported observer).
- Municipio PHP suite: 1909 tests / 3406 assertions passed.
- Header/hash JavaScript checks: 11 passed.
- Production builds completed in both repositories.

The scroll check exposes an existing site-custom inline script that accesses
`logotypeAreaContainer.classList` when its legacy selector returns null.
It is outside the changed code. Local font/CSP failures also prevent
conclusions about production font-loading and CLS behavior.
