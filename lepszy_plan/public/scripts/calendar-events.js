function addEventsToCalendar(classPeriods) {
    const events = classPeriods.map(period => ({
        title: `${period.subject} (${period.class_type})`,
        start: period.start,
        end: period.end,
        extendedProps: {
            group: period.group,
            room: period.room,
            department: period.department,
        },
        backgroundColor: getClassTypeColor(period.class_type) // Ustawiamy kolor bloku
    }));


    if (calendar) {
        calendar.getEventSources().forEach(source => source.remove());
        calendar.addEventSource(events); // Dodajemy nowe eventy
    } else {
        console.error('Kalendarz nie został zainicjalizowany.');
    }
}


function getClassTypeColor(classType) {
    switch (classType.toLowerCase()) {
        case 'laboratorium': return '#e74c3c'; // Czerwony
        case 'wykład': return '#3498db';       // Niebieski
        case 'seminarium': return '#2ecc71';   // Zielony
        case 'audytoryjne': return '#f1c40f';  // Żółty
        case 'lektorat': return '#9b59b6';     // Fioletowy
        case 'projekt': return '#e67e22';      // Pomarańczowy
        default: return '#95a5a6';             // Szary (domyślny)
    }
}