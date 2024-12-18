function initializeSuggestionBoxesAndInputs() {
    const filters = [
        { id: "filter_teacher", key: "teacher", apiUrl: "/api/teacher" },
        { id: "filter_student", key: "student"},
        { id: "filter_group", key: "group", apiUrl: "/api/group" },
        { id: "filter_room", key: "room", apiUrl: "/api/room" },
        { id: "filter_department", key: "department", apiUrl: "/api/department" },
        { id: "filter_subject", key: "subject", apiUrl: "/api/subject" },
        { id: "filter_type", key: "type", apiUrl: "/api/class-type" }
    ];

    const addFilterFunctionality = (filterElement, fieldKey, apiUrl) => {
        const suggestionBox = document.createElement("div");
        suggestionBox.id = `suggestion-box-${fieldKey}`;
        suggestionBox.className = "suggestion-box";

        document.body.appendChild(suggestionBox);

        const positionSuggestionBox = () => {
            const rect = filterElement.getBoundingClientRect();
            suggestionBox.style.top = `${rect.bottom + window.scrollY}px`;
            suggestionBox.style.left = `${rect.left + window.scrollX}px`;
            suggestionBox.style.width = `${rect.width}px`; // Dopasowanie szerokości
        };

        positionSuggestionBox();

        filterElement.addEventListener("focus", () => {
            const historyKey = `searchHistory_${fieldKey}`;
            const history = JSON.parse(localStorage.getItem(historyKey)) || [];
            updateSuggestionBox(suggestionBox, history, filterElement, fieldKey);
        });

        if (filterElement.id !== 'filter_student') {
            filterElement.addEventListener("input", async () => {
                const text = filterElement.value;
                if (text.length > 1) {
                    try {
                        const response = await fetch(`${apiUrl}?filter=${text}`);
                        const apiData = await response.json();
                        updateSuggestionBox(suggestionBox, apiData, filterElement);
                    } catch (error) {
                        console.error("Error fetching API suggestions:", error);
                    }
                } else {
                    suggestionBox.style.display = "none";
                }
            });
        }

        document.addEventListener("click", (event) => {
            if (!suggestionBox.contains(event.target) && event.target !== filterElement) {
                suggestionBox.style.display = "none";
            }
        });

        window.addEventListener("resize", positionSuggestionBox);
    };

    const updateSuggestionBox = (suggestionBox, suggestions, filterElement, fieldKey = null) => {
        suggestionBox.innerHTML = "";
        suggestions.forEach((item) => {
            const suggestionItem = document.createElement("div");
            suggestionItem.textContent = item;
            suggestionItem.className = "suggestion-item";

            suggestionItem.addEventListener("click", () => {
                filterElement.value = item;
                suggestionBox.style.display = "none";
                if (fieldKey) updateSearchHistory(fieldKey, item);
            });

            suggestionBox.appendChild(suggestionItem);
        });

        suggestionBox.style.display = suggestions.length > 0 ? "block" : "none";
    };

    const updateSearchHistory = (fieldKey, value) => {
        if (!value) return;

        const historyKey = `searchHistory_${fieldKey}`;
        let history = JSON.parse(localStorage.getItem(historyKey)) || [];

        history = history.filter((item) => item !== value);

        history.unshift(value);

        if (history.length > 5) {
            history.pop();
        }

        localStorage.setItem(historyKey, JSON.stringify(history));
    };

    const addSearchButtonFunctionality = () => {
        const searchButton = document.getElementById("search-btn");
        if (!searchButton) return;

        searchButton.addEventListener("click", () => {
            filters.forEach(({ id, key }) => {
                const filterElement = document.getElementById(id);
                if (filterElement) {
                    const value = filterElement.value.trim();
                    if (value) {
                        updateSearchHistory(key, value);
                    }
                }
            });
        });
    };

    filters.forEach(({ id, key, apiUrl }) => {
        const filterElement = document.getElementById(id);
        if (filterElement) {
            addFilterFunctionality(filterElement, key, apiUrl);
        }
    });

    addSearchButtonFunctionality();
}

document.addEventListener("DOMContentLoaded", initializeSuggestionBoxesAndInputs);
