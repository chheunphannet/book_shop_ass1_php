const sidebar = document.querySelector(".sidebar");
const sidebarToggle = document.querySelector(".open-aside");
const mainLogo = document.querySelector(".container-brandlogo-main");

sidebarToggle.addEventListener("click", () => {
  sidebar.classList.toggle("collapsed");
  mainLogo.classList.toggle("visible");
});

let currentOffset = 50;
const pageSize = 50;
function loadMore() {
  const btn = document.getElementById("load-more-btn");
  btn.innerText = "Loading...";
  fetch(`get-more-books.php?offset=${currentOffset}`)
    .then((res) => res.text())
    .then((html) => {
      if (html.trim() === "no_more") {
        btn.style.display = "none";
      } else {
        document
          .getElementById("book-cards")
          .insertAdjacentHTML("beforeend", html);
        currentOffset += pageSize; // Increase for next time
        btn.innerText = "View More";
      }
    });
}
