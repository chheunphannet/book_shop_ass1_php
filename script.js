const sidebar = document.querySelector(".sidebar");
const sidebarToggle = document.querySelector(".open-aside");
const mainLogo = document.querySelector(".container-brandlogo-main");
const searchForm = document.querySelector(".search-container");
const searchInput = document.querySelector(".search-input");

sidebarToggle.addEventListener("click", () => {
  sidebar.classList.toggle("collapsed");
  mainLogo.classList.toggle("visible");
});

if (searchForm) {
  searchForm.addEventListener("submit", (e) => {
    e.preventDefault();
    searchTitle();
  });
}

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

function searchTitle() {
  const btn = document.getElementById("search-btn");

  const query = searchInput ? searchInput.value.trim() : "";
  if (!query) {
    return;
  }

  fetch(`search-books-title.php?search=${encodeURIComponent(query)}`)
    .then((res) => res.text())
    .then((html) => {
      const container = document.getElementById("book-cards");
      container.innerHTML = "";
      if (html.trim() === "not_found") {
        container.innerHTML = "<h3>No books found</h3>";
      } else {
        document.querySelector(".suggest_container").style.display = "none";
        document.getElementById("you-may-like").style.display = "none";
        document.getElementById("all-books").style.display = "none";
        document.getElementById("load-more-btn").style.display = "none";
        document.querySelector(".suggest_container").style.display = "none";
        document.querySelector(".book-cover-grid").style.display = "none";
        container.insertAdjacentHTML("beforeend", html);
        container.style.marginTop = "20px";
      }
    });
}
