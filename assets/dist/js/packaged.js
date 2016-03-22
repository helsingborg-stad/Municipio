function googleTranslateElementInit() {
    new google.translate.TranslateElement({
        pageLanguage: "sv",
        autoDisplay: false,
        gaTrack: HbgPrimeArgs.googleTranslate.gaTrack,
        gaId: HbgPrimeArgs.googleTranslate.gaUA
    }, "google-translate-element");
}


equalheight = function(container) {

    var currentTallest = 0,
        currentRowStart = 0,
        rowDivs = new Array(),
        $el,
        topPosition = 0;
    jQuery(container).each(function() {

        $el = $(this);
        $($el).height('auto')
        topPostion = $el.position().top;

        if (currentRowStart != topPostion) {
            for (currentDiv = 0; currentDiv < rowDivs.length; currentDiv++) {
                rowDivs[currentDiv].height(currentTallest);
            }
            rowDivs.length = 0; // empty the array
            currentRowStart = topPostion;
            currentTallest = $el.height();
            rowDivs.push($el);
        } else {
            rowDivs.push($el);
            currentTallest = (currentTallest < $el.height()) ? ($el.height()) : (currentTallest);
        }
        for (currentDiv = 0; currentDiv < rowDivs.length; currentDiv++) {
            rowDivs[currentDiv].height(currentTallest);
        }
    });
}

jQuery(window).load(function() {
    equalheight('.grid > div');
});

jQuery(window).resize(function() {
    equalheight('.grid > div');
});
