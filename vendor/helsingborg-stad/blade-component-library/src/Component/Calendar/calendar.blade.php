<{{$componentElement}} id="{{ $id }}" class="{{ $class }}" {!! $attribute !!}>   
    <div id="calendarContainer"></div>

    @modal(
            [
                'heading'=> " ",
                'isPanel' => false,
                'id' => 'examplemodalid',
                'overlay' => 'dark',
                'animation' => 'scale-up',
            ]

        )
        
        <div class="{{$baseClass}}__event-list" js-toggle-item="1" js-toggle-class="">

            <div class="grid">

                <div class="grid-sm-6 grid-md-6">
                    <div class="booked">
                        <h2>Booked</h2>
                        <div class="booked__list"></div>
                    </div>        
                </div>
            
                <div class="grid-sm-6 grid-md-6">
                    <div class="available">
                        <h2>Available</h2>
                        <div class="available__list"></div>
                        @button([
                            'type' => 'filled',
                            'color' => $color,
                            'text' => 'book',
                            'classList' => ['postEventButton']
                        ])
                        @endbutton
                    </div>      
                </div>
            </div>
            
        </div>

    @endmodal
    <div id="organizerContainer" class="u-display--none"></div>
</{{$componentElement}}>

