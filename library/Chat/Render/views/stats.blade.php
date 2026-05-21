<div class="chat-stats">
    <svg width="100%" height="100%" viewBox="0 0 42 42" class="donut">
        @if ($messages === 0)
        <circle
            class="donut-segment"
            cx="21" cy="21" r="15.91549430918954"
            fill="transparent"
            stroke="#c3c4c7"
            stroke-dasharray="100 0"
            stroke-dashoffset="0">
        </circle>
        @else
        <circle
            class="donut-segment"
            cx="21" cy="21" r="15.91549430918954"
            fill="transparent"
            stroke="#4ab866"
            stroke-dasharray="{{ $likedPercent }} {{ 100 - $likedPercent }}"
            stroke-dashoffset="{{ $likedOffset }}">
            <title>{{ $lang['likedMessages'] }}: {{ round($likedPercent) }}%</title>
        </circle>
        <circle
            class="donut-segment"
            cx="21" cy="21" r="15.91549430918954"
            fill="transparent"
            stroke="#cc1818"
            stroke-dasharray="{{ $dislikedPercent }} {{ 100 - $dislikedPercent }}"
            stroke-dashoffset="{{ $dislikedOffset }}">
            <title>{{ $lang['dislikedMessages'] }}: {{ round($dislikedPercent) }}%</title>
        </circle>
        <circle
            class="donut-segment"
            cx="21" cy="21" r="15.91549430918954"
            fill="transparent"
            stroke="#c3c4c7"
            stroke-dasharray="{{ $neutralPercent }} {{ 100 - $neutralPercent }}"
            stroke-dashoffset="{{ $neutralOffset }}">
            <title>{{ $lang['neutralMessages'] }}: {{ round($neutralPercent) }}%</title>
        </circle>
        @endif
    </svg>

    <p class="chat-stats-total">{{ $lang['totalMessages'] }}: <strong>{{ $messages }}</strong></p>

    <ul class="chat-stats-list">
        <li>
            <span class="chat-stats-dot" style="background:#4ab866"></span>
            <span class="chat-stats-label">{{ $lang['likedMessages'] }}</span>
            <span class="chat-stats-value">{{ $liked }}</span>
            <span class="chat-stats-percent">{{ round($likedPercent) }}%</span>
        </li>
        <li>
            <span class="chat-stats-dot" style="background:#cc1818"></span>
            <span class="chat-stats-label">{{ $lang['dislikedMessages'] }}</span>
            <span class="chat-stats-value">{{ $disliked }}</span>
            <span class="chat-stats-percent">{{ round($dislikedPercent) }}%</span>
        </li>
        <li>
            <span class="chat-stats-dot" style="background:#c3c4c7"></span>
            <span class="chat-stats-label">{{ $lang['neutralMessages'] }}</span>
            <span class="chat-stats-value">{{ $neutral }}</span>
            <span class="chat-stats-percent">{{ round($neutralPercent) }}%</span>
        </li>
    </ul>
</div>

<style>
.chat-stats {
    padding: 4px 0;
}
.donut {
    display: block;
    max-width: calc(var(--base, 8px) * 20);
    max-height: calc(var(--base, 8px) * 20);
    margin: var(--base, 8px) auto calc(var(--base, 8px) * 2);
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
.chat-stats-total {
    margin: 0 0 calc(var(--base, 8px) * 1.5);
    font-size: 13px;
    color: #555;
    text-align: center;
}
.chat-stats-list {
    list-style: none;
    margin: 0;
    padding: 0;
}
.chat-stats-list li {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 0;
    border-top: 1px solid #f0f0f0;
    font-size: 13px;
}
.chat-stats-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
}
.chat-stats-label {
    flex: 1;
    color: #444;
}
.chat-stats-value {
    font-weight: 600;
    color: #1d2327;
}
.chat-stats-percent {
    color: #888;
    min-width: 36px;
    text-align: right;
}
</style>
