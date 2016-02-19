<header class="post-header">
    <h1>{{ the_title() }}</h1>

    <ul>
        <li class="post-author">
            <span class="post-author-image"><img src="https://randomuser.me/api/portraits/med/men/49.jpg" alt="Gary Meyer"></span>
            <span class="post-author-name">Gary Meyer</span>
        </li>
        <li class="post-date">
            {{ the_time(get_option('date_format')) }} {{ the_time(get_option('time_format')) }}
        </li>
        <li class="post-comments">
            <a href="{{ comments_link() }}">Kommentarer ({{ comments_number('0', '1', '%') }})</a>
        </li>
    </ul>
</header>
