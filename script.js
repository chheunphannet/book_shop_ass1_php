const sidebar = document.querySelector(".sidebar");
const sidebarToggle = document.querySelector(".open-aside");
const mainLogo = document.querySelector(".container-brandlogo-main");
const viewMore = document.querySelector(".view-more-container");

sidebarToggle.addEventListener("click", () => {
  sidebar.classList.toggle("collapsed");
  mainLogo.classList.toggle("visible");
});

// viewMore.addEventListener("click", () => {
//   let offset = 12;
//   fetch("home.php?offset=" + offset);
//   console.log("work");
// });
