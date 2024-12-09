document.addEventListener("DOMContentLoaded", () => {
    const resetButton = document.getElementById("reset-filters-btn");

    resetButton.addEventListener("click", () => {
        const filters = document.querySelectorAll("#filters-container input[type='text']");

        filters.forEach(filter => {
            filter.value = "";
        });
    });
});