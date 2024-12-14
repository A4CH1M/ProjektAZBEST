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
    const filterLogicSwitch = document.getElementById("filter-logic-switch");

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
            const filterLogic = filterLogicSwitch.checked ? 'OR' : 'AND';

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

            currentParams.set('filter_logic', filterLogic);

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

