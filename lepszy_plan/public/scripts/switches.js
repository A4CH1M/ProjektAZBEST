document.addEventListener("DOMContentLoaded", () => {
    const checkbox = document.getElementById("filter-logic-switch");
    const label = document.getElementById("filter-logic-text");

    checkbox.addEventListener("change", () => {
        label.textContent = checkbox.checked ? "OR" : "AND";
    });

    // Dodanie obsługi przycisku "search-btn"
    const searchButton = document.getElementById("search-btn");
    const teacherFilter = document.getElementById("filter_teacher");
    const studentFilter = document.getElementById("filter_student");
    const calendarBody = document.getElementById("calendar-body");
    searchButton.addEventListener("click", async() => {
        console.log("Przycisk 'Wyszukaj' został kliknięty.");
        try {
            // Pobierz wartość z pola filtra
            const teacherName = teacherFilter ? teacherFilter.value : '';
            const studentName = studentFilter ? studentFilter.value : '';

            // Wyślij żądanie do API z parametrem filtra
            //const response = await fetch('/api/teachers');
            //const response = await fetch(`/api/teachers?filter_teacher=${encodeURIComponent(teacherName)}`);
            //const response2 = await fetch(`/api/groupStudent?filter_student=${encodeURIComponent(studentName)}`);
            const response3 = await fetch(`/api/classPeroid?filter_student=${encodeURIComponent(studentName)}`);

            if (!response3.ok) {
                throw new Error('Błąd podczas pobierania danych');
            }

            //const teachers = await response.json();
            //const groups = await response2.json();
            const subjects = await response3.json();
            //console.log('Rekordy z kolumny full_name:', teachers);

            // Czyszczenie kalendarza
            calendarBody.innerHTML = '';

            // Dodawanie danych do kalendarza
            // groups.forEach((group) => {
            //     const row = document.createElement('tr');
            //     const cell = document.createElement('td');
            //     cell.textContent = group; // Zakładamy, że API zwraca nazwiska
            //     row.appendChild(cell);
            //     calendarBody.appendChild(row);
            // });

            subjects.forEach((subject) => {
                const row = document.createElement('tr');

                // Komórka z nazwą przedmiotu
                const nameCell = document.createElement('td');
                nameCell.textContent = subject.name;
                row.appendChild(nameCell);

                // Komórka z datą rozpoczęcia
                const startCell = document.createElement('td');
                startCell.textContent = subject.start;
                row.appendChild(startCell);

                // Komórka z datą zakonczenia
                const endCell = document.createElement('td');
                endCell.textContent = subject.end;
                row.appendChild(endCell);

                const teacherCell = document.createElement('td');
                endCell.textContent = subject.teacher;
                row.appendChild(endCell);

                const groupCell = document.createElement('td');
                groupCell.textContent = subject.group;
                row.appendChild(groupCell);

                const roomCell = document.createElement('td');
                roomCell.textContent = subject.room;
                row.appendChild(roomCell);

                const departmentCell = document.createElement('td');
                departmentCell.textContent = subject.department;
                row.appendChild(departmentCell);

                const classTypeCell = document.createElement('td');
                classTypeCell.textContent = subject.class_type;
                row.appendChild(classTypeCell);

                // Dodanie wiersza do tabeli
                calendarBody.appendChild(row);
            });

        } catch (error) {
            console.error('Wystąpił błąd:', error);
        }
    });
});
