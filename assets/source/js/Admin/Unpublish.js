Municipio = Municipio || {};
Municipio.Admin = Municipio.Admin || {};

Municipio.Admin.Unpublish = (function ($) {

    function Unpublish() {
        this.initDatepicker();
        this.handleEvents();

        if ($('#unpublish-active').val() == 'true') {
            $('#unpublish-timestamp b').text(
                $('#unpublish-mm').find('option:selected').attr('data-text') + ' ' + $('#unpublish-jj').val() + ', ' +  $('#unpublish-aa').val() + ' @ ' + $('#unpublish-hh').val() + ':' + $('#unpublish-mn').val()
            );
        }
    }

    Unpublish.prototype.initDatepicker = function () {
        $('#unpublish-aa, #unpublish-mm, #unpublish-jj').hide();

        var timestamp_wrap_text = $('.unpublish-pub-section .timestamp-wrap').html();
        timestamp_wrap_text = timestamp_wrap_text.replace(/(,|@)/g, '');
        $('.unpublish-pub-section .timestamp-wrap').html(timestamp_wrap_text);

        $('#unpublish-hh').before('<span class="municipio-admin-datepicker-time dashicons dashicons-clock"></span>')

        $('#unpublish-timestamp-datepicker').datepicker({
            firstDay: 1,
            dateFormat: "yy-mm-dd",
            onSelect: function (selectedDate) {
                selectedDate = selectedDate.split('-');

                $('#unpublish-aa').val(selectedDate[0]);
                $('#unpublish-mm').val(selectedDate[1]);
                $('#unpublish-jj').val(selectedDate[2]);
            }
        }).find('.ui-datepicker-current-day').removeClass('ui-datepicker-current-day');
    };

    Unpublish.prototype.handleEvents = function () {
        $('.edit-unpublish-timestamp').on('click', function (e) {
            e.preventDefault();
            $('.edit-unpublish-timestamp').hide();
            $('#unpublish-timestampdiv').slideDown();
        });

        $('.cancel-unpublish-timestamp').on('click', function (e){
            e.preventDefault();
            $('.edit-unpublish-timestamp').show();
            $('#unpublish-timestampdiv').slideUp();
            $('#unpublish-active').val('false');
            $('#unpublish-timestamp b').text('');
        });

        $('.save-unpublish-timestamp').on('click', function (e){
            e.preventDefault();

            if ($('#unpublish-jj').val() == '') {
                alert('You need to pick a date');
                return false;
            }

            if ($('#unpublish-hh').val() == '') {
                $('#unpublish-hh').val('00');
            }

            if ($('#unpublish-mn').val() == '') {
                $('#unpublish-mn').val('00');
            }

            $('.edit-unpublish-timestamp').show();
            $('#unpublish-timestampdiv').slideUp();
            $('#unpublish-active').val('true');
            $('#unpublish-timestamp b').text(
                $('#unpublish-mm').find('option:selected').attr('data-text') + ' ' + $('#unpublish-jj').val() + ', ' +  $('#unpublish-aa').val() + ' @ ' + $('#unpublish-hh').val() + ':' + $('#unpublish-mn').val()
            );
        });
    };

    return new Unpublish();

})(jQuery);
