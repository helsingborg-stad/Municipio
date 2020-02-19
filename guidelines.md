1. Do not use functions in views. If you feel the need to do so, don't follow up on it. 
2. Do not target array items in views ($foo['bar']). If you have to, please use objects $foo->bar. Recommended method is letting a sub-view expand your array, or cleaining it in controller. @include('text', $array); 
3. 