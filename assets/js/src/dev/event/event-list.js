Helsingborg = Helsingborg || {};
Helsingborg.Event = Helsingborg.Event || {};

Helsingborg.Event.List = (function ($) {

    var events = {};
    var options = {};

    function List() {
        $(function(){

            // Find and loop all event-lists on the page
            $('[data-event-list]').each(function (index, element) {
                this.init(element);
            }.bind(this));

        }.bind(this));
    }

    /**
     * Initialize event calendar on element
     * @param  {string} element The element
     * @return {void}
     */
    List.prototype.init = function(element) {
        this.options = this.getOptions(element);
        this.getEvents(element);
        this.handleClickEvent(element);
    };

    List.prototype.handleClickEvent = function(element) {
        $(document).on('click', '.event-item:not(.featured)', function(e) {
            e.preventDefault();
            var $modal = $('#eventModal');

            $('#event-times').show();
            $('#event-organizers').hide();
            $('.event-times-loading').show();
            $('.event-times-item').remove();

            // Find the clicked event
            var clickedEventID = $(e.target).closest('.event-item').attr('id');
            var clickedEvent;
            for (var i = 0; i < this.events.length; i++) {
                if (this.events[i].EventID === clickedEventID) {
                    clickedEvent = this.events[i];
                    break;
                }
            }

            // Get event times
            var dates_data = {
                action: 'load_event_dates',
                id: clickedEventID,
                location: clickedEvent.Location
            };

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

                if (dates.length === 0) {
                    $('#event-times').hide();
                }
            });

            // Organizers
            var organizers_data = {
                action: 'load_event_organizers',
                id: clickedEventID
            };

            $.post(ajaxurl, organizers_data, function(response) {
                var organizers = JSON.parse(response); html = '';

                for (var i=0;i<organizers.length;i++) {
                    html += '<li><span>' + organizers[i].Name + '</span></li>';
                }

                $('#organizer-modal').html(html);
                if (organizers.length > 0) {
                    $('event-organizers').show();
                } else {
                    $('event-organizers').hide();
                }
            });

            // Output information            
            if (clickedEvent.ImagePath !== "") {
                $('.modal-image').attr('src', clickedEvent.ImagePath);
            } else {
                $('.modal-image').attr('src', '/wp-content/themes/This-is-Helsingborg/assets/images/event-placeholder.jpg');
            }

            if (clickedEvent.Link) {
                $('.modal-link').html('<a class="link-item" href="' + clickedEvent.Link + '" target="blank">' + clickedEvent.Link + '</a>').show();
            } else {
                jQuery('.modal-link').empty();
            }

            $('.modal-title').html(clickedEvent.Name);
            $('.modal-date').html(clickedEvent.Date);
            $('.modal-description').html(this.nl2br(clickedEvent.Description));
            $('.modal-ics a').attr('href', '?ics=' + clickedEvent.EventID);

        }.bind(this));
    };

    List.prototype.nl2br = function(str) {
        return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + '<br>' + '$2');
    };

    /**
     * Get and append calendar events
     * @param  {string} element The base element
     * @return {void}           Outputs the events to the calendar and saves them to the "this.events" variable
     */
    List.prototype.getEvents = function(element) {
        var data = {
            action: 'update_event_calendar',
            amount: this.options.ammount,
            ids: this.options.administrationIds
        };

        $.post(ajaxurl, data, function(response) {
            var $element = $(element);
            var obj = JSON.parse(response);

            if (obj.events.length === 0) {
                $(element).closest('.widget').remove();
            }

            this.events = obj.events;

            // Remove loading icon
            $element.find('.event-loading').remove();

            // Append calendar items
            if ($element.find('.event-list li:first').hasClass('event-item-featured')) {
                $element.find('.event-item-featured').after(obj.list);
            } else {
                $element.find('.event-list').prepend(obj.list);
            }

        }.bind(this));
    };

    /**
     * Get the options set in hte data-event-list html attribute
     * @param  {string} element The element to check for options in
     * @return {object}         The options
     */
    List.prototype.getOptions = function(element) {
        var options = $(element).data('event-list');
        options = options.replace(/'/g, "\"");
        return JSON.parse(options);
    };

    return new List();

})(jQuery);