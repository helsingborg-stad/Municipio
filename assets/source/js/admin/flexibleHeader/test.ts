const buildingOpeningHours = {
1: "08.00–20.00",
2: "08.00–20.00",
3: "08.00–20.00",
4: "08.00–20.00",
5: "08.00–19.00",
6: "11.00–17.00",
7: "11.00–17.00"
};

const exhibitionOpeningHours = {
1: "Stängt",
2: "11.00–17.00",
3: "11.00–17.00",
4: "11.00–20.00",
5: "11.00–17.00",
6: "11.00–17.00",
7: "11.00–17.00"
};

document.addEventListener('DOMContentLoaded', function () {
    const dayOfWeek = (new Date()).getDay();
    const exhibitionOpenElement = document.querySelectorAll('data-js-exhibition-opening-hours');

    exhibitionOpenElement.forEach(element => {
        element.innerHTML = exhibitionOpeningHours[dayOfWeek];
    });

    const buildingOpenElement = document.querySelectorAll('data-js-building-opening-hours');

    buildingOpenElement.forEach(element => {
        element.innerHTML = buildingOpeningHours[dayOfWeek];
    });
});