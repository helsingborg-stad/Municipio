var Helsingborg;

// Gallery settings
var gallery_image_per_row = 2;
var gallery_use_masonry = false;

jQuery(document).ready(function ($) {

    $('html').removeClass('no-js');

    $('.nav-mobilemenu, .navbar-mainmenu').find('a:hidden').attr('disabled', 'disabled').addClass('auto-disabled');
    $(window).on('resize', function (e) {
        $('.nav-mobilemenu, .navbar-mainmenu').find('a.auto-disabled').removeAttr('disabled').removeClass('auto-disabled');
        $('.nav-mobilemenu, .navbar-mainmenu').find('a:hidden').attr('disabled', 'disabled').addClass('auto-disabled');
    });

    /**
     * Initializes Foundation JS with necessary plugins:
     * Equalizer
     */
    $(document).foundation({
        equalizer: {
            equalize_on_stack: true
        },
        orbit: {
            slide_number_text: 'av',
            navigation_arrows: false
        }
    });

    /**
     * Append navigation buttons to orbit
     */
    $(document).on("ready.fndtn.orbit", function(e) {
        $('.orbit-container').append('<div class="orbit-navigation"><button class="orbit-prev" aria-label="Visa föregående bild"><i class="fa fa-chevron-circle-left"></i> Föregående</button><button class="orbit-next" aria-label="Visa nästa bild">Nästa <i class="fa fa-chevron-circle-right"></i></button></div>');
    });

    /**
     * Get disturbances
     */
    jQuery.post(ajaxurl, { action: 'big_notification' }, function(response) {
        if (response) {
            response = JSON.parse(response);
            $.each(response, function (index, item) {
                var message = '<a href="' + item.link + '">' + item.title + '</a>';
                Helsingborg.Prompt.Alert.show(item.class, message);
            });
        }
    });

    /**
     * Table list
     */
    if ($('.table-list').length > 0) {
        $('.table-list').delegate('tbody tr.table-item','click', function(){
            if(!$(this).is('.active')) {
                $('.table-item').removeClass('active');
                $('.table-content').removeClass('open');
                $(this).addClass('active');
                $(this).next('.table-content').addClass('open');
            } else if($(this).hasClass('active')) {
                $(this).toggleClass('active');
                $(this).next('.table-content').removeClass('open');
            }
        });
    }

    if (typeof is_front_page !== 'undefined') {
        var mobile_menu_offset = $('.nav-mainmenu-container').offset().top;
        if ($('body').find('#wpadminbar').length) mobile_menu_offset = mobile_menu_offset - 32;

        $(window).on('scroll', function (e) {
            if ($(window).scrollTop() >= mobile_menu_offset) {
                $('.nav-mainmenu-container, body').addClass('nav-fixed');
                if ($('body').find('#wpadminbar').length) $('.nav-mainmenu-container.nav-fixed').css('top', '32px');
            } else {
                if ($('body').find('#wpadminbar').length) $('.nav-mainmenu-container.nav-fixed').css('top', '0');
                $('.nav-mainmenu-container, body').removeClass('nav-fixed');
            }
        });
    }
   
    $('.mobile-menu-wrapper').find('input, button').attr('tabindex', '-1');

    $('[data-tooltip*="click"]').on('click', function (e) {
        if ($(e.target).is('[data-tooltip-toggle]')) {
            e.preventDefault();
            $(this).find('.tooltip').toggle().find('textarea:first').focus();
        }
    });

    $('[class^="sidebar"] .widget_text').append('<div class="stripe"><div></div><div></div><div></div><div></div><div></div></div>');

});