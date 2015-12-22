var _alarmPageModel = null;

jQuery(document).ready(function() {
    var events = {};
    var eventTypes = {};

    $('#events-loading-indicator').show();
    //document.getElementById('alarm-pager-top').style.display = "none";
    //document.getElementById('event-pager-bottom').style.display = "none";
    //document.getElementById('no-event').style.display = "none";

    ko.bindingHandlers.trimText = {
        init: function(element, valueAccessor, allBindingsAccessor, viewModel) {
            var trimmedText = ko.computed(function() {
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

    _alarmPageModel = new AlarmPageModel(events);
    ko.applyBindings(_alarmPageModel);
    $(document).on('click', '.modal-link', function(event) {
        event.preventDefault();
        var alarms = _alarmPageModel.alarms();
        var result;

        for (var i = 0; i < alarms.length; i++) {
            if (alarms[i].IDnr === this.id) {
                result = alarms[i];
                break;
            }
        }

        moreInfoText = '-';
        if (result.MoreInfo != '') {
            moreInfoText = result.MoreInfo;
        }

        $('.modalDate').text(result.SentTime);
        $('.modalEvent, .main-title').text(result.HtText);
        $('.modalStation').text(result.Station);
        $('.modalID').text(result.IDnr);
        $('.modalState').text(result.PresGrp);
        $('.modalAddress').text(result.Address);
        $('.modalLocation').text(result.Place);
        $('.modalArea').text(result.Zone);
        $('.modalMoreInfo').text(moreInfoText);
    });

    var data = {
        action: 'load_alarms'
    };

    jQuery.post(ajaxurl, data, function(response) {
        _alarmPageModel.alarms(ExtractModels(_alarmPageModel, JSON.parse(response), AlarmModel));
        $('#events-loading-indicator').hide();
        //document.getElementById('alarm-pager-top').style.display = "block";
        //document.getElementById('event-pager-bottom').style.display = "block";
        //document.getElementById('no-event').style.display = "block";
    });

    jQuery(function() {
        var currentDate = new Date();
        currentDate.setDate(currentDate.getDate());

        jQuery('#datetimepickerstart').datetimepicker({
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
                    maxDate: jQuery('#datetimepickerend').val() ? jQuery('#datetimepickerend').val() : false
                })
            }
        });

        jQuery('#datetimepickerend').datetimepicker({
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
                    minDate: jQuery('#datetimepickerstart').val() ? jQuery('#datetimepickerstart').val() : false
                })
            }
        });
    });
});

function updateEvents(checkbox) {
    if (checkbox.checked) {
        var data = {
            action: 'load_events',
            ids: '0'
        };

        jQuery.post(ajaxurl, data, function(response) {
            _alarmPageModel.alarms(ExtractModels(_alarmPageModel, JSON.parse(response), EventModel));
        });
    } else {
        var data = {
            action: 'load_events',
            ids: adminIDs
        };

        jQuery.post(ajaxurl, data, function(response) {
            _alarmPageModel.alarms(ExtractModels(_alarmPageModel, JSON.parse(response), EventModel));
        });
    }
}