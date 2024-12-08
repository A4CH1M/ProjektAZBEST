document.addEventListener("DOMContentLoaded", () => {
    const checkbox = document.getElementById("filter-logic-switch");
    const label = document.getElementById("filter-logic-text");

    checkbox.addEventListener("change", () => {
        label.textContent = checkbox.checked ? "OR" : "AND";
    });

    const searchButton = document.getElementById("search-btn");

    const teacherFilter = document.getElementById("filter_teacher");
    const studentFilter = document.getElementById("filter_student");
    const groupFilter = document.getElementById("filter_grup");
    const roomFilter = document.getElementById("filter_room");
    const departmentFilter = document.getElementById("filter_faculty");
    const subjectFilter = document.getElementById("filter_course");
    const classTypeFilter = document.getElementById("filter_form");

    searchButton.addEventListener("click", async() => {
        console.log("Przycisk 'Wyszukaj' został kliknięty.");
        try {
            const teacherName = teacherFilter ? teacherFilter.value : '';
            const studentIndex = studentFilter ? studentFilter.value : '';
            const groupNumber = groupFilter ? groupFilter.value : '';
            const roomNumber = roomFilter ? roomFilter.value : '';
            const departmentName = departmentFilter ? departmentFilter.value : '';
            const subjectName = subjectFilter ? subjectFilter.value : '';
            const classTypeName = classTypeFilter ? classTypeFilter.value : '';
            
            //dodaj if'a: jeśli wszystkie są puste, nic nie wysyłaj

            url = '/api/classPeroid?';
            if (teacherName !== '')
                url += `teacher=${encodeURIComponent(teacherName)}&`;
            if (studentIndex !== '')
                url += `student=${encodeURIComponent(studentIndex)}&`;
            if (groupNumber !== '')
                url += `group=${encodeURIComponent(groupNumber)}&`;
            if (roomNumber !== '')
                url += `room=${encodeURIComponent(roomNumber)}&`;
            if (departmentName !== '')
                url += `department=${encodeURIComponent(departmentName)}&`;
            if (subjectName !== '')
                url += `subject=${encodeURIComponent(subjectName)}&`;
            if (classTypeName !== '')
                url += `class_type=${encodeURIComponent(classTypeName)}`;

            if(url[-1] === '&')
                url = url.slice(0, -1);

            const response = await fetch(url);

            if (!response.ok) {
                throw new Error('Błąd podczas pobierania danych');
            }
            
            console.log(response);
            // narazie tyle wystarczy, w tym miejscu powinno dać json'a z blokami zajęć dla podanych filtrów
            // skup się na samej logice filtrowania
        } catch (error) {
            console.error('Wystąpił błąd:', error);
        }
    });
});
