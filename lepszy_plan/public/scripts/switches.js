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

    const addFilterCommaKeyListener = (filter) => {
        filter.addEventListener("keydown", (event) => {
            if (event.key === ",") {
                if (!filter.value.includes(",")) {
                    filter.value += ", ";
                }
                event.preventDefault();
            }
        });
    }

    addFilterCommaKeyListener(teacherFilter);
    addFilterCommaKeyListener(studentFilter);

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

            let teacherNames;
            let studentIndexes;

            if (teacherName.includes(',')) {
                teacherNames = teacherName.split(',').map(name => name.trim());

            }
            else {
                teacherNames = [teacherName];
            }

            if (studentIndex.includes(',')) {
                studentIndexes = studentIndex.split(',').map(sIdx => sIdx.trim());
            }
            else {
                studentIndexes = [studentIndex];
            }

            if (teacherNames.length > 1 && studentIndex !== '' ||
                studentIndexes.length > 1 && teacherName !== '') {
                //console.error("Maksymalnie plany 2 osób (<wykładowca, wykładowca>, <wykładowca, student>, <student, student>)");
                alert("Maksymalnie plany 2 osób: \n- <wykładowca, wykładowca> \n- <wykładowca, student> \n- <student, student>");
                return;
            }

            const currentParams = new URLSearchParams();

            if (teacherName) {
                for (let i = 0; i < teacherNames.length; i++) {
                    currentParams.set(`teacher${i+1}`, teacherNames[i]);
                }
            }
            //else currentParams.delete('teacher');

            if (studentIndex) {
                for (let i = 0; i < studentIndexes.length; i++) {
                    currentParams.set(`student${i+1}`, studentIndexes[i]);
                }
            }
            //else currentParams.delete('student');

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

            updateSearchHistory('teacher', teacherName);
            updateSearchHistory('student', studentIndex);
            updateSearchHistory('group', groupNumber);
            updateSearchHistory('room', roomNumber);
            updateSearchHistory('department', departmentName);
            updateSearchHistory('subject', subjectName);
            updateSearchHistory('class_type', classTypeName);

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
    initializeSuggestionBoxes();

});

function initializeSuggestionBoxes() {
    const filters = [
        { id: "filter_teacher", key: "teacher" },
        { id: "filter_student", key: "student" },
        { id: "filter_group", key: "group" },
        { id: "filter_room", key: "room" },
        { id: "filter_department", key: "department" },
        { id: "filter_subject", key: "subject" },
        { id: "filter_type", key: "class_type" }
    ];

    const addSuggestionBox = (filterElement, fieldKey) => {
        const suggestionBox = document.createElement("div");
        suggestionBox.className = "suggestion-box";
        suggestionBox.style.position = "absolute";
        suggestionBox.style.border = "1px solid #ccc";
        suggestionBox.style.backgroundColor = "#fff";
        suggestionBox.style.zIndex = "1000";
        suggestionBox.style.width = `${filterElement.offsetWidth}px`;
        suggestionBox.style.maxHeight = "150px";
        suggestionBox.style.overflowY = "auto";
        suggestionBox.style.display = "none";

        document.body.appendChild(suggestionBox);

        const positionSuggestionBox = () => {
            const rect = filterElement.getBoundingClientRect();
            suggestionBox.style.top = `${rect.bottom + window.scrollY}px`;
            suggestionBox.style.left = `${rect.left + window.scrollX}px`;
        };

        positionSuggestionBox();

        filterElement.addEventListener("focus", () => {
            const historyKey = `searchHistory_${fieldKey}`;
            const history = JSON.parse(localStorage.getItem(historyKey)) || [];

            if (history.length === 0) {
                suggestionBox.style.display = "none";
                return;
            }

            suggestionBox.innerHTML = "";
            history.forEach(item => {
                const suggestionItem = document.createElement("div");
                suggestionItem.textContent = item;
                suggestionItem.style.padding = "5px";
                suggestionItem.style.cursor = "pointer";
                suggestionItem.addEventListener("click", () => {
                    filterElement.value = item;
                    suggestionBox.style.display = "none";
                });
                suggestionBox.appendChild(suggestionItem);
            });

            suggestionBox.style.display = "block";
        });

        filterElement.addEventListener("input", () => {
            suggestionBox.style.display = "none";
        });

        document.addEventListener("click", (event) => {
            if (!suggestionBox.contains(event.target) && event.target !== filterElement) {
                suggestionBox.style.display = "none";
            }
        });

        window.addEventListener("resize", positionSuggestionBox);
    };

    filters.forEach(({ id, key }) => {
        const filterElement = document.getElementById(id);
        if (filterElement) {
            addSuggestionBox(filterElement, key);
        }
    });
}


function updateSearchHistory(fieldKey, value) {
    if (!value) return; // Nie zapisujemy pustych wartości

    // Pobierz aktualną historię z localStorage
    const historyKey = `searchHistory_${fieldKey}`;
    let history = JSON.parse(localStorage.getItem(historyKey)) || [];

    // Usuń duplikaty
    history = history.filter(item => item !== value);

    // Dodaj nową wartość na początek
    history.unshift(value);

    // Ogranicz historię do 5 elementów
    if (history.length > 5) {
        history.pop();
    }

    // Zapisz historię z powrotem do localStorage
    localStorage.setItem(historyKey, JSON.stringify(history));
}
