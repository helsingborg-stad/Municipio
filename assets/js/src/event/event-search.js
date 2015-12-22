var _eventPageModel = null;

/**
 * Handles tab from end date filed to open the "event types" dropdown
 */
$(document).on('keydown.tabcontroller', '#datetimepickerend', function (e) {
    if (e.keyCode == 9) {
        $('.zselect').trigger('click');
    }
});

/**
 * When tabbing in the last checkbo in "event type" dropdown, close the dropdown
 */
$(document).on('keydown.tabcontroller-item', '.zselect ul li:nth-last-child(2) input', function (e) {
    if (e.keyCode == 9) {
        $('.zselect ul').hide();
    }
});

$(document).ready(function($) {
    var events = {};
    var eventTypes = {};

    ko.bindingHandlers.trimText = {
        init: function (element, valueAccessor, allBindingsAccessor, viewModel) {
            var trimmedText = ko.computed(function () {
                var untrimmedText = ko.utils.unwrapObservable(valueAccessor());
                var minLength = 5;
                var maxLength = 250;
                var text = untrimmedText.length > maxLength ? untrimmedText.substring(0, maxLength - 1) + '...' : untrimmedText;
                var text = text.replace(/&nbsp;/gi, ' ');
                var text = text.trim();
                return text;
            });

            ko.applyBindingsToNode(element, {
                text: trimmedText
            }, viewModel);

            return {
                controlsDescendantBindings: true
            }
        }
    };

    /**
     * Initialize knockout model
     */
    _eventPageModel = new EventPageModel(events, eventTypes);
    ko.applyBindings(_eventPageModel);

    /**
     * Load events
     */
    var requestParams = {
        action: 'load_events',
        ids: adminIDs
    }

    $.post(ajaxurl, requestParams, function(response) {
        _eventPageModel.events(ExtractModels(_eventPageModel, JSON.parse(response), EventModel));

        $('#events-loading-indicator').remove();
    });

    /**
     * Load event types
     */
    var requestParams = {
        action: 'load_event_types'
    }

    $.post(ajaxurl, requestParams, function(response) {
        _eventPageModel.eventTypes(ExtractModels(_eventPageModel, JSON.parse(response), TypeModel));

        $("select#municipality_multiselect").zmultiselect({
            live: "#selectedTypes",
            filter: true,
            filterPlaceholder: 'Filtrera...',
            filterResult: true,
            filterResultText: "Visar",
            selectedText: ['Valt','av'],
            selectAll: true,
            selectAllText: ['Markera alla','Avmarkera alla'],
            placeholder: 'Välj evenemangstyp(er)…'
        });
    });

    /**
     * Initialize datepickers
     */
    var currentDate = new Date();
    currentDate.setDate(currentDate.getDate());

    $('#datetimepickerstart').datetimepicker({
        minDate: currentDate,
        weeks: true,
        lang: 'se',
        timepicker: false,
        format: 'Y-m-d',
        formatDate: 'Y-m-d',
        scrollMonth: false,
        scrollTime: false,
        scrollInput: false,
        onShow: function(ct) {
            this.setOptions({
                maxDate: $('#datetimepickerend').val() ? $('#datetimepickerend').val() : false
            })
        }
    });

    $('#datetimepickerend').datetimepicker({
        weeks: true,
        lang: 'se',
        timepicker: false,
        format: 'Y-m-d',
        formatDate: 'Y-m-d',
        scrollMonth: false,
        scrollTime: false,
        scrollInput: false,
        onShow:function(ct) {
            this.setOptions({
                minDate: $('#datetimepickerstart').val() ? $('#datetimepickerstart').val() : false
            })
        }
    });

    /**
     * Open modal
     */
    $(document).on('click', '[data-reveal="eventModal"]', function (event) {
        event.preventDefault();

        var image = $('.modal-image');
        var title = $('.modal-title');
        var link = $('.modal-link');
        var date = $('.modal-date');
        var description = $('.modal-description');
        var time_list = $('#time-modal');
        var organizer_list = $('#organizer-modal');

        var isc = $('.modal-ics a');

        document.getElementById('event-times').style.display = 'block';
        $('.event-times-loading').show();
        $('.event-times-item').remove();
        document.getElementById('event-organizers').style.display = 'none';

        var events = _eventPageModel.events();
        var result;
        for (var i = 0; i < events.length; i++) {
            if (events[i].EventID === this.id) {
                result = events[i];
                break;
            }
        }

        var dates_data = { action: 'load_event_dates', id: this.id, location: result.Location };
        $.post(ajaxurl, dates_data, function(response) {
            html = "";
            var dates = JSON.parse(response);

            for (var i=0;i<dates.length;i++) {
                html += '<li class="event-times-item">';
                html += '<span class="event-date"><i class="fa fa-clock-o"></i> ' + dates[i].Date;
                if (dates[i].Time) html += ' kl. ' + dates[i].Time;
                html += '</span><span class="event-location">' + dates_data.location + '</span>';
                html += '</li>';
            }

            $('#time-modal').prepend(html);
            $('.event-times-loading').hide();

            if (dates.length == 0) {
                document.getElementById('event-times').style.display = 'none';
            }
        });

        var organizers_data = { action: 'load_event_organizers', id: this.id };

        $.post(ajaxurl, organizers_data, function(response) {
            var organizers = JSON.parse(response); html = '';

            for (var i=0;i<organizers.length;i++) {
                html += '<li><span>' + organizers[i].Name + '</span></li>';
            }

            $('#organizer-modal').html(html);
            if (organizers.length > 0) {
                document.getElementById('event-organizers').style.display = 'block';
            }
        });

        if (result.ImagePath) {
            $(image).attr("src", result.ImagePath);
        } else {
            $(image).attr("src", defaultImagePath);
        }
        $(title).html(result.Name);

        if (result.Link) {
            $(link).html('<a class="link-item" href="' + result.Link + '" target="blank">' + result.Link + '</a>').show();
        } else {
            $(link).hide();
        }

        $(date).html(result.Date);
        $(description).html(nl2br(result.Description));
        $(isc).attr('href', '?ics=' + result.EventID);
    });
});

function nl2br (str, is_xhtml) {
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}