@if($elapsed == true)
<time data-tooltip="{{$date}}" data-tooltip-right datetime="{{$date}}">{{municipio_human_datediff($date)}} {{$lang['ago']}}</time>
@else
<time data-tooltip="{{municipio_human_datediff($date)}} {{$lang['ago']}}" data-tooltip-right datetime="{{$date}}">{{$date}}</time>
@endif
