function AlarmModel(data) {
  if (!data) {
    data = {};
  }

  var self = this;
  self.IDnr = data.IDnr;
  self.CaseID = data.CaseID;
  self.SentTime = data.SentTime;
  self.PresGrp = data.PresGrp;
  self.HtText = data.HtText;
  self.Address = data.Address;
  self.AddressDescription = data.AddressDescription;
  self.Name = data.Name;
  self.Zone = data.Zone;
  self.Position = data.Position;
  self.Comment = data.Comment;
  self.MoreInfo = data.MoreInfo;
  self.Place = data.Place;
  self.BigDisturbance = data.BigDisturbance;
  self.SmallDisturbance = data.SmallDisturbance;
  self.ChangeDate = data.ChangeDate;
  self.Station = data.Station;
  self.Cities = data.Cities;
}

function TypeModel(data) {
  if (!data) {
    data = {};
  }

  var self = this;
  self.ID = data.ID;
  self.Name = data.EventTypesName;
}

function AlarmPageModel(alarms) {
  var self = this;
  self.alarms = ko.observableArray(ExtractModels(self, alarms, AlarmModel));

  self.change = function() {
    jQuery('#selectedTypes').trigger('change');
  }

  var filters = [{
    Type: "text",
    Name: "Händelse",
    Value: ko.observable(""),
    EventValue: function(alarm) {
      return alarm.HtText + alarm.Comment + alarm.Address;
    }
  }, {
    Type: "text",
    Name: "Plats",
    Value: ko.observable(""),
    EventValue: function(alarm) {
      return (alarm.Place != null) ? alarm.Place : "";
    }
  }, {
    Type: "calendar",
    Name: "Startdatum",
    CalendarID: "datetimepickerstart",
    Value: ko.observable(""),
    EventValue: function(alarm) {
      return (alarm.SentTime != null) ? alarm.SentTime.substr(0,10) : "";
    }
  }, {
    Type: "calendar",
    Name: "Slutdatum",
    CalendarID: "datetimepickerend",
    Value: ko.observable(""),
    EventValue: function(alarm) {
      return (alarm.SentTime != null) ? alarm.SentTime.substr(0,10) : "";
    }
  }];

  self.filter = new FilterModel(filters, self.alarms);
  self.pager = new PagerModel(self.filter.filteredAlarms);
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
                var alarmDate = new Date(filterValue);
                var selectedDate = new Date(eventValue);

                if (filter.Name.indexOf("Start") > -1) {
                  return alarmDate > selectedDate;
                } else {
                  return alarmDate < selectedDate;
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

  self.filteredAlarms = ko.computed(function() {
    var events = self.events();
    var filters = self.activeFilters();
    if (filters.length == 0) {
      return events;
    }

    var filteredAlarms = [];
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
        filteredAlarms.push(event);
      }
    }

    return filteredAlarms;
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