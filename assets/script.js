const toggleBtn = document.getElementById("theme-toggle");
const body = document.body;

if (localStorage.getItem("theme") === "dark") {
    body.classList.add("dark");
    document.getElementById('theme-toggle').textContent = 'تغییر تم ☀️';
}

toggleBtn.addEventListener("click", () => {
    body.classList.toggle("dark");
    if (body.classList.contains("dark")) {
        localStorage.setItem("theme", "dark");
        document.getElementById('theme-toggle').textContent = 'تغییر تم ☀️';
    } else {
        localStorage.setItem("theme", "light");
        document.getElementById('theme-toggle').textContent = 'تغییر تم 🌑';
    }
});