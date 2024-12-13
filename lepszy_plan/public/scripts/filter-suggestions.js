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
    const container1 = document.createElement("div");
    container1.classList.add("suggestion-container");
    const container2 = document.createElement("div");
    container2.classList.add("suggestion-container");
    const container3 = document.createElement("div");
    container3.classList.add("suggestion-container");
    const container4 = document.createElement("div");
    container4.classList.add("suggestion-container");
    const container5 = document.createElement("div");
    container5.classList.add("suggestion-container");
    const container6 = document.createElement("div");
    container6.classList.add("suggestion-container");

    const teacherFilter = document.getElementById("filter_teacher");
    const groupFilter = document.getElementById("filter_group");
    const roomFilter = document.getElementById("filter_room");
    const departmentFilter = document.getElementById("filter_department");
    const subjectFilter = document.getElementById("filter_subject");
    const classTypeFilter = document.getElementById("filter_type");

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
    handleFilterInput(groupFilter, container5, '/api/class-group');
    handleFilterInput(classTypeFilter, container6, '/api/class-type');
});