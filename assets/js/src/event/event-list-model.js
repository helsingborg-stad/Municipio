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
