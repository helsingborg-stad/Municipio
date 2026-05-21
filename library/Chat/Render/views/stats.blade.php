@element([])
<svg width="100%" height="100%" viewBox="0 0 42 42" class="donut">
  <circle
    class="donut-segment"
    cx="21" cy="21" r="15.91549430918954"
    fill="transparent"
    stroke="#22c55e"
    stroke-dasharray="{{ $likedPercent }} {{ 100 - $likedPercent }}"
    stroke-dashoffset="{{ $likedOffset }}">
  </circle>
  <circle
    class="donut-segment"
    cx="21" cy="21" r="15.91549430918954"
    fill="transparent"
    stroke="#ef4444"
    stroke-dasharray="{{ $dislikedPercent }} {{ 100 - $dislikedPercent }}"
    stroke-dashoffset="{{ $dislikedOffset }}">
  </circle>
  <circle
    class="donut-segment"
    cx="21" cy="21" r="15.91549430918954"
    fill="transparent"
    stroke="#d2d3d4"
    stroke-dasharray="{{ $neutralPercent }} {{ 100 - $neutralPercent }}"
    stroke-dashoffset="{{ $neutralOffset }}">
  </circle>
</svg>
    @element([])
        @typography([
            'element' => 'h2',
            'variant' => 'h6',
        ])
            {{ $lang['totalMessages'] }}: {{ $messages }}
        @endtypography
    @endelement
    @element([])
        @typography([
            'element' => 'h2',
            'variant' => 'h6',
        ])
            {{ $lang['totalMessages'] }}: {{ $messages }}
        @endtypography
    @endelement
    @element([])
        @typography([
            'element' => 'h2',
            'variant' => 'h6',
        ])
            {{ $lang['totalMessages'] }}: {{ $messages }}
        @endtypography
    @endelement
@endelement
<style>
.donut {
    max-width: calc(var(--base, 8px) * 20);
    max-height: calc(var(--base, 8px) * 20);
    position: relative;
    left: 50%;
    transform: translateX(-50%);
    margin-top: var(--base, 8px);
    margin-bottom: var(--base, 8px);
}
.donut-segment {
    transform: rotate(-90deg);
    transform-origin: center;
    stroke-width: 9;
    pointer-events: stroke;
    transition: stroke-width 0.2s ease;
}

.donut-segment:hover {
    stroke-width: 10;
}
</style>