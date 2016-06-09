Municipio = Municipio || {};
Municipio.Admin = Municipio.Admin || {};

Municipio.Admin.PublishDate = (function ($) {

    function PublishDate() {
        this.initDatepicker();
    }

    PublishDate.prototype.initDatepicker = function () {
        $('#aa, #mm, #jj').hide();
        var timestamp_wrap_text = $('.timestamp-wrap').html();
        timestamp_wrap_text = timestamp_wrap_text.replace(/(,|@)/g, '');
        $('.timestamp-wrap').html(timestamp_wrap_text);

        $('#hh').before('<span class="municipio-admin-datepicker-time dashicons dashicons-clock"></span>')

        $('#timestampdiv').prepend('<div id="timestamp-datepicker" class="municipio-admin-datepicker"></div>');
        $('#timestamp-datepicker').datepicker({
            firstDay: 1,
            dateFormat: "yy-mm-dd",
            onSelect: function (selectedDate) {
                selectedDate = selectedDate.split('-');

                $('#aa').val(selectedDate[0]);
                $('#mm').val(selectedDate[1]);
                $('#jj').val(selectedDate[2]);
            }
        });

        var initialDate = $('#aa').val() + '-' + $('#mm').val() + '-' + $('#jj').val();
        $('#timestamp-datepicker').datepicker('setDate', initialDate);
    };

    return new PublishDate();

})(jQuery);
