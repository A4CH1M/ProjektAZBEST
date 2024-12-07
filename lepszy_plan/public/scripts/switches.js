document.addEventListener("DOMContentLoaded", () => {
    const checkbox = document.getElementById("filter-logic-switch");
    const label = document.getElementById("filter-logic-text");

    checkbox.addEventListener("change", () => {
        label.textContent = checkbox.checked ? "OR" : "AND";
    });

    const darkModeSwitch = document.getElementById('dark-mode-switch');

    darkModeSwitch.addEventListener('change', function () {
        document.body.classList.toggle('dark-mode', this.checked);
    });
});
