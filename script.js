const sidebar = document.querySelector(".sidebar");
const sidebarToggle = document.querySelector(".open-aside");
const mainLogo = document.querySelector(".container-brandlogo-main");
const searchForm = document.querySelector(".search-container");
const searchInput = document.querySelector(".search-input");
const basketTrigger = document.querySelector(".basket");
const cartDrawer = document.getElementById("cart-drawer");
const cartBackdrop = document.getElementById("cart-backdrop");
const cartClose = document.querySelector(".cart-close");
const basketCount = document.querySelector(".basket-count");
const orderModal = document.getElementById("order-modal");
const orderModalIcon = document.getElementById("order-modal-icon");
const orderModalTitle = document.getElementById("order-modal-title");
const orderModalMessage = document.getElementById("order-modal-message");
// const removeFromCart = document.getElementById("remove-from-cart");

function setTotalPrice(totalText) {
  const totalEl = document.querySelector(".total-price");
  if (totalEl) {
    totalEl.textContent = `Total: $${totalText}`;
  }
}

function getTotalPrice() {
  return fetch(`calculate_price.php`)
    .then((res) => res.text())
    .then((text) => {
      setTotalPrice(text.trim());
    });
}

getTotalPrice();

function removeFromCart(book_id) {
  fetch(`remove-from-cart.php?book_id=${book_id}`)
    .then(() => fetch(`dispay-item-in-cart.php`))
    .then((res) => res.text())
    .then((html) => {
      const cart_body = document.querySelector(".cart-body");
      if (html.trim() === "no_item") {
        cart_body.innerHTML = "<p class='cart-empty'>Your basket is empty.</p>";
        setTotalPrice("0.00");
      } else {
        cart_body.innerHTML = html;
        getTotalPrice();
      }
      return getCountItemInBasket();
    });
}

fetch(`dispay-item-in-cart.php`)
  .then((res) => res.text())
  .then((html) => {
    const cart_body = document.querySelector(".cart-body");
    if (html.trim() === "no_item") {
      cart_body.innerHTML = "<p class='cart-empty'>Your basket is empty.</p>";
      setTotalPrice("0.00");
    } else {
      cart_body.innerHTML = html;
      getTotalPrice();
    }
  });

function getCountItemInBasket() {
  return fetch(`basket-count.php`)
    .then((res) => res.text())
    .then((html) => {
      basketCount.innerText = html;
    });
}
getCountItemInBasket();

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

  fetch(`add-to-cart.php?book_id=${book_id}&qty=${qty}&real_qty=${real_qty}`)
    .then((response) => {
      if (response.ok) {
        display.value = 1;
      }
      return fetch(`dispay-item-in-cart.php`);
    })
    .then((res) => res.text())
    .then((html) => {
      const cart_body = document.querySelector(".cart-body");
      if (html.trim() === "no_item") {
        cart_body.innerHTML = "<p class='cart-empty'>Your basket is empty.</p>";
        setTotalPrice("0.00");
      } else {
        cart_body.innerHTML = html;
        getTotalPrice();
      }
      return getCountItemInBasket();
    });

  setCartOpen(true);
}

function placeOrder() {
  fetch("check-out.php")
    .then((res) => res.text())
    .then((msg) => {
      if (msg.trim() === "Order_success") {
        const cartBody = document.querySelector(".cart-body");
        cartBody.innerHTML = "<p class='cart-empty'>Your basket is empty.</p>";
        setTotalPrice("0.00");
        basketCount.innerText = "0";
        showOrderModal("success");
      } else {
        showOrderModal("fail");
      }
    });
}

function showOrderModal(type) {
  if (!orderModal) {
    return;
  }
  orderModal.classList.remove("success", "fail");
  orderModal.classList.add(type, "open");

  if (type === "success") {
    orderModalIcon.textContent = "check_circle";
    orderModalTitle.textContent = "Order Successful";
    orderModalMessage.textContent = "Your order has been placed.";
  } else {
    orderModalIcon.textContent = "error";
    orderModalTitle.textContent = "Order Failed";
    orderModalMessage.textContent = "Your cart is empty or checkout failed.";
  }
}

function closeOrderModal() {
  if (!orderModal) {
    return;
  }
  orderModal.classList.remove("open", "success", "fail");
}

function setDisplay(display) {
  document.querySelector(".suggest_container").style.display = display;
  document.getElementById("you-may-like").style.display = display;
  document.getElementById("all-books").style.display = display;
  document.getElementById("load-more-btn").style.display = display;
  document.querySelector(".suggest_container").style.display = display;
  document.querySelector(".book-cover-grid").style.display = display;
}
