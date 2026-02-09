const sidebar = document.querySelector(".sidebar");
const sidebarToggle = document.querySelector(".open-aside");
const mainLogo = document.querySelector(".container-brandlogo-main");
sidebarToggle.addEventListener("click", () => {
  sidebar.classList.toggle("collapsed");
  mainLogo.classList.toggle("visible");
});
