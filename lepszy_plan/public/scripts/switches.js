document.addEventListener("DOMContentLoaded", () => {
    const checkbox = document.getElementById("filter-logic-switch");
    const label = document.getElementById("filter-logic-text");

    checkbox.addEventListener("change", () => {
        label.textContent = checkbox.checked ? "OR" : "AND";
    });
});
