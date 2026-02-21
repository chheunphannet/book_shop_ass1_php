const sidebar = document.querySelector(".sidebar");
const sidebarToggle = document.querySelector(".open-aside");
const mainLogo = document.querySelector(".container-brandlogo-main");
const searchForm = document.querySelector(".search-container");
const searchInput = document.querySelector(".search-input");
const basketTrigger = document.querySelector(".basket");
const cartDrawer = document.getElementById("cart-drawer");
const cartBackdrop = document.getElementById("cart-backdrop");
const cartClose = document.querySelector(".cart-close");

fetch(`dispay-item-in-cart.php`)
  .then((res) => res.text())
  .then((html) => {
    const cart_body = document.querySelector(".cart-body");
    if (html.trim() === "no_item") {
      cart_body.innerHTML = "<p class='cart-empty'>Your basket is empty.</p>";
    } else {
      cart_body.innerHTML = html;
      const totalEl = cart_body.querySelector("[data-cart-total]");
      const total = totalEl.dataset.cartTotal;
      document.querySelector(".total-price").textContent = `Total: $${total}`;
    }
  });

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

function setCartOpen(isOpen) {
  if (!cartDrawer || !cartBackdrop) {
    return;
  }
  cartDrawer.classList.toggle("open", isOpen);
  cartBackdrop.classList.toggle("visible", isOpen);
}

if (basketTrigger) {
  basketTrigger.addEventListener("click", () => {
    const isOpen = cartDrawer ? cartDrawer.classList.contains("open") : false;
    setCartOpen(!isOpen);
  });
}

if (cartClose) {
  cartClose.addEventListener("click", () => setCartOpen(false));
}

if (cartBackdrop) {
  cartBackdrop.addEventListener("click", () => setCartOpen(false));
}

let currentOffset = 20;
const pageSize = 20;
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
const container = document.getElementById("book-cards");
function searchTitle() {
  const btn = document.getElementById("search-btn");

  const query = searchInput ? searchInput.value.trim() : "";
  if (!query) {
    return;
  }

  fetch(`search-books-title.php?search=${encodeURIComponent(query)}`)
    .then((res) => res.text())
    .then((html) => {
      container.innerHTML = "";
      if (html.trim() === "not_found") {
        container.innerHTML = "<h3>No books found</h3>";
      } else {
        setDisplay("none");
        container.insertAdjacentHTML("beforeend", html);
        container.style.marginTop = "20px";
      }
    });
}

function loadBooksByCategory(category_id) {
  fetch(`load-books-category.php?category_id=${category_id}`)
    .then((res) => res.text())
    .then((html) => {
      container.innerHTML = "";
      if (html.trim() === "not_found") {
        container.innerHTML = "<h3>No books found</h3>";
      } else {
        setDisplay("none");
        container.insertAdjacentHTML("beforeend", html);
        container.style.marginTop = "20px";
      }
    });
}

const c_limit = 12;
let c_offset = c_limit;
const category_container = document.querySelector(".view-more-container");
function loadCategoryMore() {
  fetch(`load-category.php?limit=${c_limit}&offset=${c_offset}`)
    .then((res) => res.text())
    .then((html) => {
      if (html.trim() != "no_more") {
        document.querySelector(".list-container-slide").innerHTML = html;

        const newUrl =
          window.location.pathname + `?limit=${c_limit}&offset=${c_offset}`;
        history.pushState({ offset: c_offset }, "", newUrl);
        c_offset += c_limit;
      }
    });
}

function changeQty(bookId, delta, stock_quantity) {
  const display = document.getElementById(`qty_display_${bookId}`);
  if (!display) {
    return;
  }
  let currentVal = parseInt(display.value);

  let newVal = currentVal + delta;

  if (newVal < 1) {
    newVal = 1;
  } else if (newVal > stock_quantity) {
    newVal = newVal - 1;
  }

  display.value = newVal;
}

window.onpopstate = function (event) {
  if (event.state) {
    window.location.reload();
  }
};

function addToCart(book_id, real_qty) {
  const display = document.getElementById(`qty_display_${book_id}`);
  const qty = parseInt(display.value);

  fetch(
    `add-to-cart.php?book_id=${book_id}&qty=${qty}&real_qty=${real_qty}`,
  ).then((response) => {
    if (response.ok) {
      alert("Added to cart!");
      display.value = 1;
    }
  });

  fetch(`dispay-item-in-cart.php`)
    .then((res) => res.text())
    .then((html) => {
      const cart_body = document.querySelector(".cart-body");
      if (html.trim() === "no_item") {
        cart_body.innerHTML = "<p class='cart-empty'>Your basket is empty.</p>";
      } else {
        cart_body.innerHTML = html;
        const totalEl = cart_body.querySelector("[data-cart-total]");
        const total = totalEl ? totalEl.dataset.cartTotal : "0.00";
        document.querySelector(".total-price").textContent = `$${total}`;
      }
    });
}

function setDisplay(display) {
  document.querySelector(".suggest_container").style.display = display;
  document.getElementById("you-may-like").style.display = display;
  document.getElementById("all-books").style.display = display;
  document.getElementById("load-more-btn").style.display = display;
  document.querySelector(".suggest_container").style.display = display;
  document.querySelector(".book-cover-grid").style.display = display;
}
