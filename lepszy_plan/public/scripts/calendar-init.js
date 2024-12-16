let calendar;
document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');

    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek', // Domyślny widok: roczny
        initialDate: '2024-10-01',
        headerToolbar: headerToolbar,
        buttonText: buttonText,
        //height: '1000', // Automatyczna wysokość
        locale: 'pl',
        multiMonthMaxColumns: 2,// Język polsk
        firstDay: 1,
        events: events,
        views: views,
        slotMinTime: '07:00:00',     // Minimalna godzina w widokach dziennym/tygodniowym
        slotMaxTime: '21:00:00',      // Maksymalna godzina w widokach dziennym/tygodniowym
        allDaySlot: false,
        eventMouseEnter: function (info) {
            // Tworzymy tooltip
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip'; // Dodajemy klasę CSS
            tooltip.innerHTML = `
        <strong>${info.event.title}</strong><br>
        <b>Grupa:</b> ${info.event.extendedProps.group}<br>
        <b>Sala:</b> ${info.event.extendedProps.room}<br>
        <b>Wydział:</b> ${info.event.extendedProps.department}<br>
        <b>Godzina:</b> ${info.event.start.toLocaleTimeString()} - ${info.event.end.toLocaleTimeString()}
    `;
            document.body.appendChild(tooltip);

            // Pozycjonowanie tooltipa
            tooltip.style.position = 'absolute';
            tooltip.style.left = `${info.jsEvent.pageX + 10}px`;
            tooltip.style.top = `${info.jsEvent.pageY + 10}px`;
            tooltip.style.zIndex = 1000;

            // Przechowujemy tooltip w elemencie
            info.el.tooltip = tooltip;

            // Aktualizacja pozycji podczas ruchu myszki
            info.el.addEventListener('mousemove', function (e) {
                tooltip.style.left = `${e.pageX + 10}px`;
                tooltip.style.top = `${e.pageY + 10}px`;
            });
        },
        eventMouseLeave: function (info) {
            // Pobieramy tooltip
            const tooltip = info.el.tooltip;

            // Usuwamy tooltip, jeśli istnieje
            if (tooltip) {
                tooltip.remove();
                info.el.tooltip = null;
            }
        },

    });


    calendar.render();
});

// const customButtons = {
//     customTermView:{
//         text: 'Semestr',
//         click: function() {
//             const currentStartDate = new Date(info.start);
//             const currentMonth = currentStartDate.getMonth(); // Miesiąc zaczyna się od 0 (styczeń = 0, grudzień = 11)
//
//             // Zmień widok w zależności od aktualnego miesiąca
//             if (currentMonth >= 9 || currentMonth <= 1) { // Październik, Listopad, Grudzień, Styczeń
//                 calendar.changeView('multiMonthFiveMonth'); // Widok na 5 miesięcy
//             } else if (currentMonth >= 2 && currentMonth <= 8) { // Marzec - Wrzesień
//                 calendar.changeView('multiMonthSevenMonth'); // Widok na 7 miesięcy
//             }
//         }
//     }
// }


const headerToolbar = {
    left: 'prev,next today',   // Nawigacja: poprzedni/następny i "dzisiaj"
    center: 'title',          // Tytuł
    right: 'multiMonthSixMonth,dayGridMonth,timeGridWeek,timeGridDay' // Wszystkie widoki
}

const buttonText = {
    multiMonthSixMonth: 'Semestr',
    today:    'Dzisiaj',
    month:    'Miesiąc',
    week:     'Tydzień',
    day:      'Dzień',
}
const views = {
    multiMonthSixMonth: {
        type: 'multiMonth',
        duration: { months: 6 }
    },
    multiMonthFiveMonth: {
        type: 'multiMonth',
        duration: { months: 5 }
    },
    multiMonthSevenMonth: {
        type: 'multiMonth',
        duration: { months: 5 }
    },
}

events = []

