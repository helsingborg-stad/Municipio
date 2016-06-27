Intranet = Intranet || {};
Intranet.Stripe = (function ($) {

    var playLog = [];
    var magicCode = [0, 1, 2, 3, 4];

    var instruments = [
        {
            name: 'Piano',
            path: 'piano',
            icon: 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0NDMuOTgxIiBoZWlnaHQ9IjQ0My45ODEiIHZpZXdCb3g9IjAgMCA0NDMuOTgxIDQ0My45ODEiPjxwYXRoIGQ9Ik00NDMuOTggMTM0Ljk4bC0yNS4xNjYtMTQuMjc0Yy0yNS4xNjMtMTQuMjU2LTE4LjE3LTE3LjE4NC0yOS4zNjItMzkuNTU1LTExLjE3Ny0yMi4zNzItNDYuMTM4LTIyLjM3Mi00Ni4xMzgtMjIuMzcyTDQ1LjUyMiAxMjIuNjE1bDMxNS4yOCA1My43NzUgODMuMTgtNDEuNDF6bS04NS45ODIgNDguNDlMNDUuNTIyIDEyOC40MTR2NTguMjkyTDAgMjA0Ljg4djM5LjE0bDc2Ljc2IDExLjIxNiAxNy44NzQgOTUuOTIyYzEuMDYyIDUuNzAzIDIuNzggNS43MDMgMy44NSAwbDE2LjgyMi05MC4yOTUgNDYuOTYgNi44N3Y3MC40OGwtMjAuNDUtMS41MDJ2MTkuNTU0bDg1LjQ2NSA2LjI4NHYtMTkuNTMybC0xOS40MDItMS40NDN2LTY3LjE5Nmw2OC42OTMgMTAuMDQgMTcuOTg3IDk2LjUxOGMxLjA1OCA1LjY5NCAyLjc4NSA1LjY5NCAzLjg1IDBsMTYuOTMtOTAuODU2IDE4LjEyOCAyLjY0NS43NzMtMzcuNTE4IDIzLjc2LTEzLjk3VjE4My40N3pNMTk1LjMgMzQwLjY1NWwtMjAuNDQyLTEuNTAzdi02OS41OGwyMC40NDIgMi45ODh2NjguMDk1em0xMTcuOTYzLTc1LjEzMkwyNC45IDIyNC42MzNsNDQuNTY0LTE4LjM2IDYuNTA4Ljk2LTE5LjI3NCAxMy4wMjUgMTAuNDkgMi43ODcgMTMuMjU4LTE1LjE1NCAxOS41NTYgMi44NzctMTguMTUgMTIuMjcgMTAuNDg4IDIuNzkgMTIuNTUzLTE0LjM0NyAxOS4xNjIgMi44My0xNy4wNSAxMS41MTYgMTAuNDg4IDIuNzk3IDExLjg0Ni0xMy41NSA0Mi44IDYuMzE1LTE0Ljg0IDEwLjAyIDEwLjQ4NyAyLjc5OCAxMC40MzYtMTEuOTI0IDE3Ljk1MyAyLjY0LTEzLjcyIDkuMjg0IDEwLjQ4NiAyLjc5NyA5LjcyLTExLjEzIDQxLjYwMyA2LjEzMy0xMS41MSA3Ljc5IDEwLjQ4IDIuNzkgOC4zMjctOS41MDYgNDAuNzgzIDYuMDItOS4yOSA2LjI5IDEwLjQ4OCAyLjc4NiA2LjktNy44OSAzMi4yMiA0Ljc1Mi0xOS40IDIwLjQ4NXoiLz48cGF0aCBkPSJNMzY0LjY0MiAyNDYuNDhsLTI0LjQ3IDEzLjk2NnYzMy4yMTRsMTAzLjgxLTcwLjYxNVYxNDMuNTJsLTc5LjM0IDQwLjczN3oiLz48L3N2Zz4='
        },
        {
            name: 'Drums',
            path: 'drums',
            icon: 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA2MCA2MCI+PHBhdGggZD0iTTYwIDUuOThhMSAxIDAgMCAwLTEtMWMtNC4zNjQgMC02LjExMi4zMjgtNyAuODc4LS44ODgtLjU1LTIuNjM2LS44OC03LS44OGExIDEgMCAwIDAgMCAyYzUuMDM3IDAgNS44Ny40NzUgNS45ODYuNTg4LjAxNS4wNS4wMTUuMjEuMDE0LjMyN3Y2LjYyNEwzNy45MTUgMTcuOTdhMS43IDEuNyAwIDAgMC0xLjA0Ny43OTcgMS43MSAxLjcxIDAgMCAwLS4xNzQgMS4zMDZsLjk0NiAzLjUzMkExOC45NDQgMTguOTQ0IDAgMCAwIDMxIDIyLjAxM1YxNS45OGg2YTEgMSAwIDAgMCAwLTJIMjNhMSAxIDAgMCAwIDAgMmg2djYuMDMzYy0yLjI5LjEyMi00LjUzOC42NjMtNi42NCAxLjU5MmwuOTQ1LTMuNTMyYy4xMi0uNDQ0LjA1OC0uOTA3LS4xNzQtMS4zMDZhMS43IDEuNyAwIDAgMC0xLjA0NC0uNzk3TDkgMTQuNTE4di02LjU0YzAtLjAxMy0uMDA3LS4wMjQtLjAwOC0uMDM4IDAtLjAxMy4wMDgtLjAyNC4wMDgtLjAzOHYtLjAwOGMwLS4xMTggMC0uMjc4LS4wMDUtLjI5NS4xMzYtLjE0OC45NjgtLjYyIDYuMDA1LS42MmExIDEgMCAwIDAgMC0yYy00LjM2NCAwLTYuMTEyLjMyNy03IC44NzctLjg4OC0uNTUtMi42MzYtLjg4LTctLjg4YTEgMSAwIDAgMCAwIDJjNS4wMzcgMCA1Ljg3LjQ3NSA1Ljk4Ni41ODguMDE1LjA1LjAxNS4yMS4wMTQuMzI4djYuMDk2bC0xLjk4NS0uNTI0Yy0uODktLjIzOC0xLjg1NS4zMjMtMi4wOTUgMS4yMTVsLTEuNyA2LjM0N2MtLjEyLjQ0My0uMDU4LjkwNi4xNzIgMS4zMDMuMjMuMzk4LjYwMy42ODMgMS4wNDguOEw3IDI0LjMzNHYyNy4yMzNMLjI5MyA1OC4yNzNhMSAxIDAgMCAwIDEuNDE0IDEuNDEzTDcgNTQuMzk0djQuNTg2YTEgMSAwIDAgMCAyIDB2LTQuNTg2bDUuMjkzIDUuMjkzYS45OTcuOTk3IDAgMCAwIDEuNDE0IDAgMSAxIDAgMCAwIDAtMS40MTRMOSA1MS41NjVWMjQuODZsOC4xNTQgMi4xNUExOC43ODcgMTguNzg3IDAgMCAwIDExIDQwLjk4YzAgNi42IDMuMzg4IDEyLjQyIDguNTEyIDE1LjgyN2wtLjQ4MiAxLjkzYS45OTguOTk4IDAgMCAwIC45NyAxLjI0IDEgMSAwIDAgMCAuOTctLjc1N2wuMzQtMS4zNjVjMi42MDggMS4zNDggNS41NTggMi4xMjMgOC42OSAyLjEyM3M2LjA4Mi0uNzc1IDguNjktMi4xMjNsLjM0IDEuMzY1YTEgMSAwIDAgMCAxLjk0LS40ODRsLS40ODItMS45M0M0NS42MTIgNTMuNDAyIDQ5IDQ3LjU4MiA0OSA0MC45OGExOC43ODQgMTguNzg0IDAgMCAwLTYuMTU0LTEzLjk3TDUxIDI0Ljg2djI2LjcwNmwtNi43MDcgNi43MDdhMSAxIDAgMCAwIDEuNDE0IDEuNDE0TDUxIDU0LjM5NHY0LjU4NmExIDEgMCAwIDAgMiAwdi00LjU4Nmw1LjI5MyA1LjI5M2EuOTk3Ljk5NyAwIDAgMCAxLjQxNCAwIDEgMSAwIDAgMCAwLTEuNDE0TDUzIDUxLjU2NVYyNC4zMzNsNC41NjItMS4yMDNhMS43MTIgMS43MTIgMCAwIDAgMS4wNDYtLjhjLjIzLS4zOTYuMjktLjg2LjE3Mi0xLjMwM2wtMS43LTYuMzQ3Yy0uMjQtLjg5LTEuMjAzLTEuNDUyLTIuMDk1LTEuMjE0TDUzIDEzLjk5VjcuOThjMC0uMDE1LS4wMDctLjAyNi0uMDA4LS4wNCAwLS4wMTMuMDA4LS4wMjQuMDA4LS4wMzh2LS4wMWMwLS4xMTYgMC0uMjc2LS4wMDUtLjI5NC4xMzYtLjE0NS45NjgtLjYyIDYuMDA1LS42MmExIDEgMCAwIDAgMS0xem0tMTYgMzVjMCA3LjcyLTYuMjggMTQtMTQgMTRzLTE0LTYuMjgtMTQtMTQgNi4yOC0xNCAxNC0xNCAxNCA2LjI4IDE0IDE0em0xLTM3YzQuMzY0IDAgNi4xMTItLjMzIDctLjg4Ljg4OC41NSAyLjYzNi44OCA3IC44OGExIDEgMCAwIDAgMC0yYy01LjAzNyAwLTUuODctLjQ3NS01Ljk4Ni0uNTg4QTEuODU0IDEuODU0IDAgMCAxIDUzIDEuMDY1Vi45OGMwLS41NTMtLjQ0Ni0uOTYtLjk5OC0uOTZINTJjLS41NSAwLS45OTguNDg1LTEgMS4wMzZ2LjAxYzAgLjExNiAwIC4yNzYuMDA1LjI5NC0uMTM2LjE0Ni0uOTY4LjYyLTYuMDA1LjYyYTEgMSAwIDAgMCAwIDJ6bS00NCAwYzQuMzY0IDAgNi4xMTItLjMzIDctLjg4Ljg4OC41NSAyLjYzNi44OCA3IC44OGExIDEgMCAwIDAgMC0yYy01LjAzNyAwLTUuODctLjQ3NS01Ljk4Ni0uNTg4QTEuODQzIDEuODQzIDAgMCAxIDkgMS4wNjVWLjk4YzAtLjU1My0uNDQ2LS45Ni0uOTk4LS45NkM3LjQ4LjA2IDcuMDAyLjUwNSA3IDEuMDU3di4wMWMwIC4xMTYgMCAuMjc2LjAwNS4yOTMtLjEzNi4xNDYtLjk2OC42Mi02LjAwNS42MmExIDEgMCAwIDAgMCAyeiIvPjxwYXRoIGQ9Ik0zNC41IDM5Ljk4Yy0yLjQ4IDAtNC41IDIuMDE4LTQuNSA0LjVzMi4wMiA0LjUgNC41IDQuNSA0LjUtMi4wMiA0LjUtNC41LTIuMDItNC41LTQuNS00LjV6Ii8+PC9zdmc+'
        }
    ];

    var activeInstrument = null;
    var sounds = [];

    /**
     * Constructor
     * Should be named as the class itself
     */
    function Stripe() {
        if ($('.stripe').length > 0) {
            $.each(sounds, function (index, item) {
                new Audio(item);
            });

            $('.stripe').addClass('easter-egg').append('<ul class="stripe-instruments"></ul>');

            $.each(instruments, function (index, item) {
                $('.stripe .stripe-instruments').append('<li><button data-instrument-key="' + index + '"><img src="' + item.icon + '"><span>' + item.name + '</span></button></li>');
            });
        }

        $('.stripe div').on('click', function (e) {
            var soundIndex = $(e.target).closest('div').index();
            this.play(soundIndex);
            this.playLog(soundIndex);
        }.bind(this));

        $(document).on('click', '.stripe .stripe-instruments button',  function (e) {
            var instrumentKey = $(e.target).closest('button').attr('data-instrument-key');
            this.setInstrument(instrumentKey);
        }.bind(this));
    }

    Stripe.prototype.setInstrument = function(instrumentKey) {
        activeInstrument = instruments[instrumentKey].path;
        sounds = [
            municipioIntranet.themeUrl + '/assets/sound/' + activeInstrument + '/1.mp3',
            municipioIntranet.themeUrl + '/assets/sound/' + activeInstrument + '/2.mp3',
            municipioIntranet.themeUrl + '/assets/sound/' + activeInstrument + '/3.mp3',
            municipioIntranet.themeUrl + '/assets/sound/' + activeInstrument + '/4.mp3',
            municipioIntranet.themeUrl + '/assets/sound/' + activeInstrument + '/5.mp3'
        ];
    };

    /**
     * Play sound at index
     * @param  {integer} soundIndex Sound index
     * @return {void}
     */
    Stripe.prototype.play = function(soundIndex) {
        if (!(soundIndex in sounds)) {
            return;
        }

        var audio = new Audio(sounds[soundIndex]);

        audio.play();

        audio.addEventListener('ended', function () {
            this.remove();
        });
    };

    /**
     * Log strokes in the playLog
     * @param  {integer} soundIndex Sound index
     * @return {mixed}
     */
    Stripe.prototype.playLog = function(soundIndex) {
        playLog.push(soundIndex);
        var lastFour = playLog.slice(Math.max(playLog.length - magicCode.length, 0));

        if (lastFour.join('') != magicCode.join('')) {
            return;
        }

        $('.stripe .stripe-instruments').addClass('show');
    };

    return new Stripe();

})(jQuery);
