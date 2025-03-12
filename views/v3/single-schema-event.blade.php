@extends('templates.single')

@section('article.content')
    @image([ 'src'=> $post->imageContract, 'fullWidth' => true, ]) @endimage

    @if($locationLinkAttributes && $locationLinkText)
        @link($locationLinkAttributes)
            @icon(['icon' => 'location_on'])@endicon
            {!!$locationLinkText!!}
        @endlink
    @endif

    @typography()
        @icon(['icon' => 'schedule'])@endicon{!!$durationText!!}
    @endtypography

    @if(!empty($dateAndTimeForEventsInSameSeries))
        @typography()
            <i>Fler tider och datum finns</i>
        @endtypography
    @endif

    @typography(['variant' => 'h2'])
        Beskrivning
    @endtypography
    
    {!!$post->schemaObject['description'] ?? ''!!}

    @link(['href' => $icsDownloadLink])
        @icon(['icon' => 'event'])@endicon Lägg till i kalender
    @endlink
    
@stop

@section('sidebar.right-sidebar.before')

    @typography(['variant' => 'h3'])
        Om evenemanget
    @endtypography

    @typography(['variant' => 'h3'])
        @icon(['icon' => 'schedule'])@endicon Datum och tider
    @endtypography

    <strong>{!!$dateAndTime['local']!!}</strong>
    @typography(){!!$dateAndTime['time']!!}@endtypography

    @if(!empty($dateAndTimeForEventsInSameSeries))
        @accordion([])
            @accordion__item(['heading' => 'Visa fler datum och tider'])
                @foreach($dateAndTimeForEventsInSameSeries as $event)
                    <div>
                        <strong>{!!$event['local']!!}</strong>
                        @typography(){!!$event['time']!!}@endtypography
                    </div>
                @endforeach
            @endaccordion__item
        @endaccordion
    @endif

    @if(!empty($priceListItems))
        @typography(['variant' => 'h3'])
            @icon(['icon' => 'local_activity'])@endicon Priser
        @endtypography
    @endif

    @foreach ($priceListItems as $priceListItem)
        
        <div>
            <strong>{!! $priceListItem->getName() !!}</strong>
            <span>{!! $priceListItem->getPrice() !!}</span>
        </div>
    @endforeach

@stop