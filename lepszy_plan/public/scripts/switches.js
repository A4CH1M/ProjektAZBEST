function handleFilterInput(filterElement, container, apiUrl) {
    filterElement.addEventListener("input", async () => {
        const text = filterElement.value;
        if (text.length > 1) {
            try {
                const response = await fetch(`${apiUrl}?filter=${text}`);
                const data = await response.json();

                container.innerHTML = "";

                data.forEach(item => {
                    const suggestionBox = document.createElement('div');
                    suggestionBox.textContent = item;
                    suggestionBox.classList.add("suggestion-item");

                    // Obsługa kliknięcia w sugestię
                    suggestionBox.addEventListener('click', () => {
                        filterElement.value = item;
                        container.innerHTML = "";
                    });

                    container.appendChild(suggestionBox);
                });
            } catch (error) {
                console.error("Błąd podczas pobierania danych:", error);
            }
        } else {
            container.innerHTML = "";
        }
    });
}
document.addEventListener("DOMContentLoaded", async() => {
    const checkbox = document.getElementById("filter-logic-switch");
    const label = document.getElementById("filter-logic-text");

    checkbox.addEventListener("change", () => {
        label.textContent = checkbox.checked ? "OR" : "AND";
    });

    const searchButton = document.getElementById("search-btn");

    const teacherFilter = document.getElementById("filter_teacher");
    const studentFilter = document.getElementById("filter_student");
    const groupFilter = document.getElementById("filter_group");
    const roomFilter = document.getElementById("filter_room");
    const departmentFilter = document.getElementById("filter_department");
    const subjectFilter = document.getElementById("filter_subject");
    const classTypeFilter = document.getElementById("filter_type");

    const container1 = document.createElement("div");
    container1.classList.add("suggestion-container");
    const container2 = document.createElement("div");
    container2.classList.add("suggestion-container");
    const container3 = document.createElement("div");
    container3.classList.add("suggestion-container");
    const container4 = document.createElement("div");
    container4.classList.add("suggestion-container");
    const container5 = document.createElement("div");
    container4.classList.add("suggestion-container");
    const container6 = document.createElement("div");
    container4.classList.add("suggestion-container");

    const filtersContainerTeacher = document.getElementById("filter-container-teacher");
    filtersContainerTeacher.appendChild(container1);
    const filtersContainerSubject = document.getElementById("filter-container-subject");
    filtersContainerSubject.appendChild(container2);
    const filtersContainerDepartment = document.getElementById("filter-container-department");
    filtersContainerDepartment.appendChild(container3);
    const filtersContainerRoom = document.getElementById("filter-container-room");
    filtersContainerRoom.appendChild(container4);
    const filtersContainerClassGroup = document.getElementById("filter-container-group");
    filtersContainerClassGroup.appendChild(container5);
    const filtersContainerClassType = document.getElementById("filter-container-type");
    filtersContainerClassType.appendChild(container6);

    handleFilterInput(teacherFilter, container1, '/api/teacher');
    handleFilterInput(subjectFilter, container2, '/api/subject');
    handleFilterInput(departmentFilter, container3, '/api/department');
    handleFilterInput(roomFilter, container4, '/api/room');
    handleFilterInput(groupFilter, container5, '/api/classGroup');
    handleFilterInput(classTypeFilter, container6, '/api/classType');

    document.addEventListener("keydown", (event) => {
        if (event.key === "Enter") {
            event.preventDefault();
            searchButton.click();
        }
    });
    const fetchAndApplyFilters = async (params) => {
        const apiUrl = `/api/class-period?${params.toString()}`;

        try {
            const response = await fetch(apiUrl);

            if (!response.ok) {
                throw new Error('Błąd podczas pobierania danych');
            }

            const classPeriods = await response.json();
            addEventsToCalendar(classPeriods);
        } catch (error) {
            console.error('Wystąpił błąd:', error);
        }
    };
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.toString()) {
        await fetchAndApplyFilters(urlParams);
    }
    searchButton.addEventListener("click", async () => {
        try {
            const teacherName = teacherFilter ? teacherFilter.value : '';
            const studentIndex = studentFilter ? studentFilter.value : '';
            const groupNumber = groupFilter ? groupFilter.value : '';
            const roomNumber = roomFilter ? roomFilter.value : '';
            const departmentName = departmentFilter ? departmentFilter.value : '';
            const subjectName = subjectFilter ? subjectFilter.value : '';
            const classTypeName = classTypeFilter ? classTypeFilter.value : '';

            if (teacherName === '' && studentIndex === '' && groupNumber === ''
                && roomNumber === '' && departmentName === '' && subjectName === ''
                && classTypeName === '') {
                return;
            }

            const currentParams = new URLSearchParams();

            if (teacherName) currentParams.set('teacher', teacherName);
            else currentParams.delete('teacher');

            if (studentIndex) currentParams.set('student', studentIndex);
            else currentParams.delete('student');

            if (groupNumber) currentParams.set('group', groupNumber);
            else currentParams.delete('group');

            if (roomNumber) currentParams.set('room', roomNumber);
            else currentParams.delete('room');

            if (departmentName) currentParams.set('department', departmentName);
            else currentParams.delete('department');

            if (subjectName) currentParams.set('subject', subjectName);
            else currentParams.delete('subject');

            if (classTypeName) currentParams.set('class_type', classTypeName);
            else currentParams.delete('class_type');

            const apiUrl = `/api/class-period?${currentParams.toString()}`;

            const response = await fetch(apiUrl);

            const newUrl = `${window.location.pathname}?${currentParams.toString()}`;
            window.history.replaceState(null, '', newUrl);


            if (!response.ok) {
                throw new Error('Błąd podczas pobierania danych');
            }

            const classPeriods = await response.json();

            addEventsToCalendar(classPeriods)

        } catch (error) {
            console.error('Wystąpił błąd:', error);
        }
    });
    const darkModeSwitch = document.getElementById('dark-mode-switch');

    darkModeSwitch.addEventListener('change', function () {
        document.body.classList.toggle('dark-mode', this.checked);

    });
});

