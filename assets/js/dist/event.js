function EventModel(data) {
    if (!data) {
        data = {};
    }

    var self = this;
    self.EventID = data.EventID;
    self.Date = data.Date;
    self.Name = data.Name;
    self.Description = data.Description;
    self.Link = data.Link;
    self.ImagePath = data.ImagePath;
    self.Location = data.Location;
    self.EventTypesName = data.EventTypesName;
}

function TypeModel(data) {
    if (!data) {
        data = {};
    }

    var self = this;
    self.ID = data.ID;
    self.Name = data.EventTypesName;
}

function EventPageModel(events, eventTypes) {
    var self = this;
    self.selectedEventTypes = ko.observable();
    self.events = ko.observableArray(ExtractModels(self, events, EventModel));
    self.eventTypes = ko.observableArray(ExtractModels(self, eventTypes, TypeModel));

    self.change = function() {
        jQuery('#selectedTypes').trigger('change');
    }

    var filters = [{
        Type: "text",
        Name: "Fritext",
        Value: ko.observable(""),
        EventValue: function(event) {
            return event.Name + event.Description;
        }
    }, {
        Type: "text",
        Name: "Plats",
        Value: ko.observable(""),
        EventValue: function(event) {
            return (event.Location != null) ? event.Location : "";
        }
    }, {
        Type: "calendar",
        Name: "Startdatum",
        CalendarID: "datetimepickerstart",
        Value: ko.observable(""),
        EventValue: function(event) {
            return (event.Date != null) ? event.Date : "";
        }
    }, {
        Type: "calendar",
        Name: "Slutdatum",
        CalendarID: "datetimepickerend",
        Value: ko.observable(""),
        EventValue: function(event) {
            return (event.Date != null) ? event.Date : "";
        }
    }, {
        Type: "select",
        Name: "Evenemangstyp",
        Options: self.eventTypes,
        CurrentOption: self.selectedEventTypes,
        EventValue: function(event) {
            return (event.EventTypesName != null) ? event.EventTypesName : "";
        }
    }];

    self.filter = new FilterModel(filters, self.events);
    self.pager = new PagerModel(self.filter.filteredEvents);
}

function PagerModel(events) {
    var self = this;

    self.events = GetObservableArray(events);
    self.currentPageIndex = ko.observable(self.events().length > 0 ? 0 : -1);
    self.currentPageSize = 7;

    self.eventCount = ko.computed(function() {
        return self.events().length;
    });

    self.maxPageIndex = ko.computed(function() {
        var maxpageindex = Math.ceil(self.events().length / self.currentPageSize) - 1;
        return maxpageindex;
    });

    self.pagerPages = function () {
        var total = self.maxPageIndex();
        var current = self.currentPageIndex();
        var range = 5;
        var start = 0;
        var end = 0;

        if (current < 2) {
            start = 1;
            end = range;
        }
        else if (current + range > total) {
            start = total - range;
            end = total;
        } else {
            start = current - 1;
            end = current + 3;
        }

        var pages = new Array();
        for (var i = start; i <= end; i++) {
            pages.push(i);
        }

        return pages;
    };

    self.currentPageEvents = ko.computed(function() {
        var newPageIndex = -1;
        var pageIndex = self.currentPageIndex();
        var maxPageIndex = self.maxPageIndex();

        if (pageIndex > maxPageIndex) {
            newPageIndex = maxPageIndex;
        } else if (pageIndex == -1) {
            if (maxPageIndex > -1) {
                newPageIndex = 0;
            } else {
                newPageIndex = -2;
            }
        } else {
            newPageIndex = pageIndex;
        }

        if (newPageIndex != pageIndex) {
            if (newPageIndex >= -1) {
                self.currentPageIndex(newPageIndex);
            }

            return [];
        }

        var pageSize = self.currentPageSize;
        var startIndex = pageIndex * pageSize;
        var endIndex = startIndex + pageSize;

        self.renderPagers();

        return self.events().slice(startIndex, endIndex);
    }).extend({
        throttle: 5
    });

    self.currentStatus = function(index) {
        return (self.currentPageIndex() == index) ? 'current' : '';
    };

    self.isHidden = function(index) {
        return (self.maxPageIndex() >= index) ? true : false;
    }

    self.moveFirst = function() {
        self.changePageIndex(0);
    };

    self.movePrevious = function() {
        self.changePageIndex(self.currentPageIndex() - 1);
    };

    self.moveNext = function() {
        self.changePageIndex(self.currentPageIndex() + 1);
    };

    self.moveLast = function() {
        self.changePageIndex(self.maxPageIndex());
    };

    self.changePageIndex = function(newIndex) {
        if (newIndex < 0 || newIndex == self.currentPageIndex() || newIndex >
            self.maxPageIndex()) {
            return;
        }
        self.currentPageIndex(newIndex);
    };

    self.onPageSizeChange = function() {
        self.currentPageIndex(0);
    };

    self.renderPagers = function() {
        self.pagerPages();
    };

    self.renderNoEvents = function() {
        var message = "<span data-bind=\"visible: pager.eventCount() == 0\">Hittade inga event.</span>";
        $("div.NoEvents").html(message);
    };

    //self.renderPagers();
    //self.renderNoEvents();
}

function FilterModel(filters, events) {
    var self = this;
    self.events = GetObservableArray(events);
    self.filters = ko.observableArray(filters);
    self.activeFilters = ko.computed(function() {

        var filters = self.filters();
        var activeFilters = [];

        for (var index = 0; index < filters.length; index++) {
            var filter = filters[index];

            if (filter.CurrentOption) {
                var filterOption = filter.CurrentOption();
                if (filterOption != null) {
                    var activeFilter = {
                        Filter: filter,
                        IsFiltered: function(filter, event) {
                            var filterOption = filter.CurrentOption();
                            if (!filterOption) {
                                return;
                            }

                            var eventValue = filter.EventValue(event);
                            return filterOption.indexOf(eventValue) == -1;
                        }
                    };
                    activeFilters.push(activeFilter);
                }
            } else if (filter.Value) {
                var filterValue = filter.Value();
                if (filterValue && filterValue != "" && filterValue != null) {
                    var activeFilter = {
                    Filter: filter,
                    IsFiltered: function(filter, event) {
                        var filterValue = filter.Value();
                        filterValue = filterValue.toUpperCase();

                        var eventValue = filter.EventValue(event);
                        eventValue = eventValue.toUpperCase();

                        if (filter.Type == "calendar") {
                            var eventDate = new Date(filterValue);
                            var selectedDate = new Date(eventValue);

                            if (filter.Name.indexOf("Start") > -1) {
                                return eventDate > selectedDate;
                            } else {
                                return eventDate < selectedDate;
                            }
                        } else {
                            return eventValue.indexOf(filterValue) == -1;
                        }
                    }
                    };
                    activeFilters.push(activeFilter);
                }
            }
        }

        return activeFilters;
    });

    self.filteredEvents = ko.computed(function() {
        var events = self.events();
        var filters = self.activeFilters();
        if (filters.length == 0) {
            return events;
        }

        var filteredEvents = [];
        for (var rIndex = 0; rIndex < events.length; rIndex++) {
            var isIncluded = true;
            var event = events[rIndex];

            for (var fIndex = 0; fIndex < filters.length; fIndex++) {
                var filter = filters[fIndex];
                var isFiltered = filter.IsFiltered(filter.Filter, event);
                if (isFiltered) {
                    isIncluded = false;
                    break;
                }
            }

            if (isIncluded) {
                filteredEvents.push(event);
            }
        }

        return filteredEvents;
    }).extend({
        throttle: 200
    });
}

function ExtractModels(parent, data, constructor) {
    var models = [];
    if (data == null) {
        return models;
    }

    for (var index = 0; index < data.length; index++) {
        var row = data[index];
        var model = new constructor(row, parent);
        models.push(model);
    }

    return models;
}

function GetObservableArray(array) {
    if (typeof(array) == 'function') {
        return array;
    }

    return ko.observableArray(array);
}

function CompareCaseInsensitive(left, right) {
    if (left == null) {
        return right == null;
    } else if (right == null) {
        return false;
    }

    return left.toUpperCase() <= right.toUpperCase();
}

function GetOption(name, value, filterValue) {
    var option = {
        Name: name,
        Value: value,
        FilterValue: filterValue
    };

    return option;
}

function SortArray(array, direction, comparison) {
    if (array == null) {
        return [];
    }

    for (var oIndex = 0; oIndex < array.length; oIndex++) {
        var oItem = array[oIndex];

        for (var iIndex = oIndex + 1; iIndex < array.length; iIndex++) {
            var iItem = array[iIndex];
            var isOrdered = comparison(oItem, iItem);

            if (isOrdered == direction) {
                array[iIndex] =
                oItem;
                array[oIndex] = iItem;
                oItem =
                iItem;
            }
        }
    }

    return array;
}

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