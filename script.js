const sidebar = document.querySelector(".sidebar");
const sidebarToggle = document.querySelector(".open-aside");

sidebarToggle.addEventListener("click", () => {
  sidebar.classList.toggle("collapsed");
});
