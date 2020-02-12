<!-- testimonials.blade.php -->
@if($testimonials)
    <{{ $componentElement }} id="{{ $id }}" class="grid {{ $class }}" {!! $attribute !!}>
                @foreach($testimonials as $testimonial)
                    <div class="{{ $gridClasses }} {{ $baseClass}}__image-top">

                        @if ($testimonial['imageTop'])
                            @if ($testimonial['image'])
                                @if ($testimonial['avatar'])
                                    @avatar([
                                        'image' => $testimonial['image'],
                                        'name' => $testimonial['name'],
                                        'size' => 'lg'
                                    ])
                                    @endavatar
                                @else
                                    @image([
                                        'src'=> $testimonial['image'],
                                        'alt' => $testimonial['name']
                                    ])
                                    @endimage
                                @endif
                            @endif

                            <div class="{{ $baseClass}}__header">
                                @if ($testimonial['name'])
                                    @typography([
                                            "variant" => $testimonial['nameElement'],
                                            "element" => "h2"
                                        ])
                                        {{$testimonial['name']}}
                                    @endtypography
                                @endif
                            </div>

                        @else
                            <div class="{{ $baseClass}}__header {{ $baseClass}}__image-bottom">
                                @if ($testimonial['name'])
                                    @typography([
                                        "variant" => $testimonial['nameElement'],
                                        "element" => "h2"
                                    ])
                                        {{$testimonial['name']}}
                                    @endtypography

                                @endif
                                @if ($testimonial['title'])
                                    @typography([
                                        "variant" => $testimonial['titleElement'],
                                        "element" => "h4"
                                    ])
                                        {{$testimonial['title']}}
                                    @endtypography

                                @endif
                            </div>
                            @if ($testimonial['image'])
                                @if ($testimonial['avatar'])
                                    @avatar([
                                        'image' => $testimonial['image'],
                                        'name' => $testimonial['name'],
                                        'size' => 'lg'
                                    ])
                                    @endavatar
                                @else
                                    @image([
                                        'src'=> $testimonial['image'],
                                        'alt' => $testimonial['name']
                                    ])
                                    @endimage
                                @endif
                            @endif
                        @endif
                        <div class="{{ $baseClass }}__quote c-testimonials__quote-color-{{$testimonial['quoteColor']}}">
                            @icon(['icon' => 'format_quote', 'size' => 'lg'])
                            @endicon
                            @typography([
                                "variant" => "p",
                                "element" => "p"
                            ])
                                {{$testimonial['testimonial']}}
                            @endtypography

                            @if ($testimonial['imageTop'])
                                @if ($testimonial['title'])
                                    @typography([
                                        "variant" => $testimonial['titleElement'],
                                        "element" => "h4"
                                    ])
                                        {{$testimonial['title']}}
                                    @endtypography
                                @endif
                            @endif
                        </div>

                    </div>
                @endforeach
    </{{ $componentElement }}>
@endif
