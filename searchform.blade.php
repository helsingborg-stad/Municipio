@extends('templates.scaffolding')

@section('content')

<?php
    global $searchFormNode;
    $searchFormNode = ($searchFormNode) ? $searchFormNode+1 : 1;
?>
<form class="search" method="get" action="/">
    @if (is_front_page())
        <label class="label label-lg label-theme" for="searchkeyword-{{ $searchFormNode }}">Sök på Helsingborg.se</label>
    @else
        <label for="searchkeyword-{{ $searchFormNode }}" class="sr-only">Sök på Helsingborg.se</label>
    @endif

    <div class="input-group input-group-lg">
        <input id="searchkeyword-{{ $searchFormNode }}" autocomplete="off" class="form-control form-control-lg" type="search" name="s" placeholder="Vad letar du efter?" value="<?php echo (isset($_GET['s']) && strlen($_GET['s']) > 0) ? urldecode(stripslashes($_GET['s'])) : ''; ?>">
        <span class="input-group-addon-btn">
            <input type="submit" class="btn btn-primary btn-lg" value="Sök">
        </span>
    </div>
</form>

@stop
