const limit = 10;
let offset = limit;
let newBookCounter = 0;
const insertedBookIdByTempId = new Map();
let newCategoryCounter = 0;
const insertedCategoryIdByTempId = new Map();

function applyInventoryTitleFilter() {
  const searchInput = document.getElementById("inventorySearch");
  const query = (searchInput?.value || "").trim().toLowerCase();

  document.querySelectorAll("#inventoryTbody tr").forEach((row) => {
    const titleFromData = (row.dataset.title || "").toLowerCase();
    const titleFromText = (
      row.querySelector(".field-title")?.textContent || ""
    ).trim().toLowerCase();
    const title = titleFromData || titleFromText;
    const matched = query === "" || title.includes(query);
    row.style.display = matched ? "" : "none";
  });
}

function applyCategoryNameFilter() {
  const searchInput = document.getElementById("categorySearch");
  const query = (searchInput?.value || "").trim().toLowerCase();

  document.querySelectorAll("#categoryTbody tr").forEach((row) => {
    const nameFromData = (row.dataset.name || "").toLowerCase();
    const nameFromText = (
      row.querySelector('[class*="category-display-value-"]')?.textContent || ""
    ).trim().toLowerCase();
    const name = nameFromData || nameFromText;
    const matched = query === "" || name.includes(query);
    row.style.display = matched ? "" : "none";
  });
}

function isTemporaryBookId(book_id) {
  return String(book_id).startsWith("new-");
}

function getPersistedBookId(book_id) {
  return insertedBookIdByTempId.get(String(book_id)) ?? null;
}

function isTemporaryCategoryId(category_id) {
  return String(category_id).startsWith("new-cat-");
}

function getPersistedCategoryId(category_id) {
  return insertedCategoryIdByTempId.get(String(category_id)) ?? null;
}

function escapeHtml(value) {
  return String(value)
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#39;");
}

function getCategoryOptionsHtml() {
  const options = [];
  const seen = new Set();

  document.querySelectorAll('#inventoryTbody select[name="category"] option').forEach((opt) => {
    const value = (opt.value || "").trim();
    if (!value) {
      return;
    }

    const key = value.toLowerCase();
    if (!seen.has(key)) {
      seen.add(key);
      options.push(value);
    }
  });

  if (options.length === 0) {
    return '<option value="">Select category</option>';
  }

  return options
    .map((value) => {
      const safeValue = escapeHtml(value);
      return `<option value="${safeValue}">${safeValue}</option>`;
    })
    .join("");
}

function createNewBookRow(book_id) {
  const row = document.createElement("tr");
  const categoryOptionsHtml = getCategoryOptionsHtml();

  row.dataset.title = "";
  row.dataset.category = "";
  row.innerHTML = `
    <td>
      <img
        class="book-cover display-value display-value-${book_id}"
        src="../placeholder-image-vertical.png"
        alt="New book cover"
      >
      <div class="input-file input-container input-container-${book_id}">
        <input type="file" accept="image/*" name="book_cover">
      </div>
    </td>
    <td>
      <span class="display-value field-title display-value-${book_id}"></span>
      <div class="input-container input-container-${book_id}">
        <input type="text" name="title" value="">
      </div>
    </td>
    <td>
      <span class="display-value field-category display-value-${book_id}"></span>
      <select class="dropdown-cat input-container input-container-${book_id}" name="category">
        ${categoryOptionsHtml}
      </select>
    </td>
    <td>
      <span class="display-value field-pages display-value-${book_id}">-</span>
      <div class="input-container input-container-${book_id}">
        <input type="number" name="page_number" value="" min="0">
      </div>
    </td>
    <td>
      <span class="display-value field-price display-value-${book_id}">$0.00</span>
      <div class="input-container input-container-${book_id}">
        <input type="number" name="unit_price" value="0" step="0.01" min="0">
      </div>
    </td>
    <td>
      <span class="display-value field-stock display-value-${book_id}">0</span>
      <div class="input-container input-container-${book_id}">
        <input type="number" name="stock_quantity" value="0" min="0">
      </div>
    </td>
    <td>
      <span class="badge stock display-value field-status display-value-${book_id} empty">Empty</span>
    </td>
    <td class="action-cell">
      <div class="action-buttons">
        <button type="button" class="icon-btn edit" id="btn-edit-${book_id}" aria-label="Edit new book">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M3 17.25V21h3.75L17.8 9.94l-3.75-3.75L3 17.25zm18-11.5a1 1 0 0 0 0-1.41l-1.34-1.34a1 1 0 0 0-1.41 0L16.13 4.1l3.75 3.75L21 5.75z"></path>
          </svg>
        </button>
        <button type="button" class="icon-btn delete" id="btn-delete-${book_id}" aria-label="Delete new book">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M6 7h12l-1 14H7L6 7zm3-3h6l1 2H8l1-2z"></path>
          </svg>
        </button>
        <button type="button" class="icon-btn confirm" id="btn-confirm-${book_id}">
          confirm
        </button>
        <button type="button" class="icon-btn cancel" id="btn-cancel-${book_id}">
          cancel
        </button>
      </div>
    </td>
  `;

  row
    .querySelector(`#btn-edit-${book_id}`)
    .addEventListener("click", () => onEditClick(book_id));
  row
    .querySelector(`#btn-delete-${book_id}`)
    .addEventListener("click", () => onDeleteClick(book_id));
  row
    .querySelector(`#btn-confirm-${book_id}`)
    .addEventListener("click", () => onConfirm(book_id));
  row
    .querySelector(`#btn-cancel-${book_id}`)
    .addEventListener("click", () => onCancel(book_id));

  return row;
}

function onAddBookClick() {
  const inventoryTbody = document.getElementById("inventoryTbody");
  if (!inventoryTbody) {
    return;
  }

  newBookCounter += 1;
  const book_id = `new-${Date.now()}-${newBookCounter}`;
  const newRow = createNewBookRow(book_id);
  inventoryTbody.prepend(newRow);
  onEditClick(book_id);

  const titleInput = newRow.querySelector('input[name="title"]');
  if (titleInput) {
    titleInput.focus();
  }
}

function newBooks() {
  const inventoryTbody = document.getElementById("inventoryTbody");
  const nextBtn = document.getElementById("next-btn");

  fetch(`next_books.php?limit=${limit}&offset=${offset}`)
    .then((res) => res.text())
    .then((html) => {
      if (html.trim() !== "no_more") {
        inventoryTbody.insertAdjacentHTML("beforeend", html);
        offset += limit;
        applyInventoryTitleFilter();
      } else {
        nextBtn.style.display = "none";
      }
    })
    .catch(() => {
      if (nextBtn) {
        nextBtn.disabled = true;
        nextBtn.textContent = "Load failed";
      }
    });
}

function onEditClick(book_id) {
  document
    .querySelectorAll(`.input-container-${book_id}`)
    .forEach((el) => el.classList.add("show"));
  document
    .querySelectorAll(`.display-value-${book_id}`)
    .forEach((el) => el.classList.add("hidden"));
  document.getElementById(`btn-edit-${book_id}`).style.display = "none";
  document.getElementById(`btn-delete-${book_id}`).style.display = "none";
  document.getElementById(`btn-confirm-${book_id}`).style.display =
    "inline-grid";
  document.getElementById(`btn-cancel-${book_id}`).style.display =
    "inline-grid";
}

function onCancel(book_id) {
  document
    .querySelectorAll(`.input-container-${book_id}`)
    .forEach((el) => el.classList.remove("show"));
  document
    .querySelectorAll(`.display-value-${book_id}`)
    .forEach((el) => el.classList.remove("hidden"));
  document.getElementById(`btn-edit-${book_id}`).style.display = "inline-grid";
  document.getElementById(`btn-delete-${book_id}`).style.display =
    "inline-grid";
  document.getElementById(`btn-confirm-${book_id}`).style.display = "none";
  document.getElementById(`btn-cancel-${book_id}`).style.display = "none";
}

function getRowData(book_id) {
  const row = document.getElementById(`btn-confirm-${book_id}`).closest("tr");

  return {
    row,
    title: row.querySelector('input[name="title"]')?.value.trim() || "",
    category: row.querySelector('select[name="category"]')?.value || "",
    page_number: row.querySelector('input[name="page_number"]')?.value.trim() || "",
    unit_price: row.querySelector('input[name="unit_price"]')?.value || "0",
    stock_quantity:
      row.querySelector('input[name="stock_quantity"]')?.value || "0",
    book_cover:
      row.querySelector('input[name="book_cover"]')?.files?.[0] || null,
  };
}

function getInventoryStatus(stockQuantity) {
  if (stockQuantity === 0) {
    return { label: "Empty", className: "empty" };
  }

  if (stockQuantity <= 10) {
    return { label: "Low", className: "low" };
  }

  return { label: "In Stock", className: "in-stock" };
}

function applyRowUiUpdate(book_id, data) {
  const pagesRaw = String(data.page_number ?? "").trim();
  const pagesValue = Number.parseInt(pagesRaw, 10);
  const safePages =
    pagesRaw !== "" && Number.isInteger(pagesValue) && pagesValue >= 0
      ? String(pagesValue)
      : "-";
  const price = Number.parseFloat(data.unit_price);
  const safePrice = Number.isFinite(price) ? price : 0;
  const qty = Number.parseInt(data.stock_quantity, 10);
  const safeQty = Number.isInteger(qty) && qty >= 0 ? qty : 0;

  const coverEl = data.row.querySelector(`img.display-value-${book_id}`);
  const titleEl = data.row.querySelector(`.field-title.display-value-${book_id}`);
  const categoryEl = data.row.querySelector(
    `.field-category.display-value-${book_id}`,
  );
  const pagesEl = data.row.querySelector(`.field-pages.display-value-${book_id}`);
  const priceEl = data.row.querySelector(`.field-price.display-value-${book_id}`);
  const stockEl = data.row.querySelector(`.field-stock.display-value-${book_id}`);
  const statusEl = data.row.querySelector(`.field-status.display-value-${book_id}`);

  if (titleEl) {
    titleEl.textContent = data.title;
  }

  if (categoryEl) {
    categoryEl.textContent = data.category;
  }

  if (pagesEl) {
    pagesEl.textContent = safePages;
  }

  if (priceEl) {
    priceEl.textContent = `$${safePrice.toFixed(2)}`;
  }

  if (stockEl) {
    stockEl.textContent = String(safeQty);
  }

  if (statusEl) {
    const status = getInventoryStatus(safeQty);
    statusEl.textContent = status.label;
    statusEl.classList.remove("in-stock", "low", "empty");
    statusEl.classList.add(status.className);
  }

  if (coverEl && data.book_cover) {
    if (coverEl.dataset.previewUrl) {
      URL.revokeObjectURL(coverEl.dataset.previewUrl);
    }
    const previewUrl = URL.createObjectURL(data.book_cover);
    coverEl.src = previewUrl;
    coverEl.dataset.previewUrl = previewUrl;
  }

  data.row.dataset.title = data.title.toLowerCase();
  data.row.dataset.category = data.category.toLowerCase();
}

function onConfirm(book_id) {
  const data = getRowData(book_id);

  if (!data.title || !data.category) {
    alert("Title and category are required.");
    return;
  }

  const fd = new FormData();
  fd.append("book_id", book_id);
  fd.append("title", data.title);
  fd.append("category", data.category);
  fd.append("page_number", data.page_number);
  fd.append("unit_price", data.unit_price);
  fd.append("stock_quantity", data.stock_quantity);
  if (data.book_cover) {
    fd.append("book_cover", data.book_cover);
  }

  const persistedBookId = getPersistedBookId(book_id);
  const isUnsavedTempRow = isTemporaryBookId(book_id) && persistedBookId === null;
  const endpoint = isUnsavedTempRow ? "add_book.php" : "update_book.php";

  if (!isUnsavedTempRow) {
    fd.set("book_id", String(persistedBookId ?? book_id));
  }

  fetch(endpoint, { method: "POST", body: fd })
    .then((res) => res.json())
    .then((payload) => {
      if (!payload.ok) {
        throw new Error(payload.message || "Save failed.");
      }

      if (isUnsavedTempRow && payload.book_id) {
        insertedBookIdByTempId.set(String(book_id), Number(payload.book_id));
        data.row.dataset.persistedBookId = String(payload.book_id);
      }

      applyRowUiUpdate(book_id, data);
      onCancel(book_id);
    })
    .catch((err) => {
      alert(err.message || "Save failed.");
    });
}

function onDeleteClick(book_id) {
  const button = document.getElementById(`btn-delete-${book_id}`);
  const row = button ? button.closest("tr") : null;

  if (!row) {
    return;
  }

  if (!window.confirm("Delete this book?")) {
    return;
  }

  const persistedBookId = getPersistedBookId(book_id);
  if (isTemporaryBookId(book_id) && persistedBookId === null) {
    row.remove();
    return;
  }

  button.disabled = true;

  const fd = new FormData();
  fd.append("book_id", String(persistedBookId ?? book_id));

  fetch("delete_book.php", { method: "POST", body: fd })
    .then((res) => res.json())
    .then((payload) => {
      if (!payload.ok) {
        throw new Error(payload.message || "Delete failed.");
      }

      if (isTemporaryBookId(book_id)) {
        insertedBookIdByTempId.delete(String(book_id));
      }
      row.remove();
    })
    .catch((err) => {
      alert(err.message || "Delete failed.");
      button.disabled = false;
    });
}

function createNewCategoryRow(category_id) {
  const row = document.createElement("tr");
  row.dataset.name = "";
  row.innerHTML = `
    <td class="category-id">-</td>
    <td>
      <span class="display-value category-display-value category-display-value-${category_id}"></span>
      <div class="input-container category-input-container-${category_id}">
        <input type="text" name="category_name" value="">
      </div>
    </td>
    <td class="action-cell">
      <div class="action-buttons">
        <button type="button" class="icon-btn edit" id="category-btn-edit-${category_id}">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M3 17.25V21h3.75L17.8 9.94l-3.75-3.75L3 17.25zm18-11.5a1 1 0 0 0 0-1.41l-1.34-1.34a1 1 0 0 0-1.41 0L16.13 4.1l3.75 3.75L21 5.75z"></path>
          </svg>
        </button>
        <button type="button" class="icon-btn delete" id="category-btn-delete-${category_id}">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M6 7h12l-1 14H7L6 7zm3-3h6l1 2H8l1-2z"></path>
          </svg>
        </button>
        <button type="button" class="icon-btn confirm" id="category-btn-confirm-${category_id}">
          confirm
        </button>
        <button type="button" class="icon-btn cancel" id="category-btn-cancel-${category_id}">
          cancel
        </button>
      </div>
    </td>
  `;

  row
    .querySelector(`#category-btn-edit-${category_id}`)
    .addEventListener("click", () => onCategoryEditClick(category_id));
  row
    .querySelector(`#category-btn-delete-${category_id}`)
    .addEventListener("click", () => onCategoryDeleteClick(category_id));
  row
    .querySelector(`#category-btn-confirm-${category_id}`)
    .addEventListener("click", () => onCategoryConfirm(category_id));
  row
    .querySelector(`#category-btn-cancel-${category_id}`)
    .addEventListener("click", () => onCategoryCancel(category_id));

  return row;
}

function onCategoryAddClick() {
  const categoryTbody = document.getElementById("categoryTbody");
  if (!categoryTbody) {
    return;
  }

  newCategoryCounter += 1;
  const category_id = `new-cat-${Date.now()}-${newCategoryCounter}`;
  const newRow = createNewCategoryRow(category_id);
  categoryTbody.prepend(newRow);
  onCategoryEditClick(category_id);

  const nameInput = newRow.querySelector('input[name="category_name"]');
  if (nameInput) {
    nameInput.focus();
  }
}

function onCategoryEditClick(category_id) {
  document
    .querySelectorAll(`.category-input-container-${category_id}`)
    .forEach((el) => el.classList.add("show"));
  document
    .querySelectorAll(`.category-display-value-${category_id}`)
    .forEach((el) => el.classList.add("hidden"));

  document.getElementById(`category-btn-edit-${category_id}`).style.display = "none";
  document.getElementById(`category-btn-delete-${category_id}`).style.display = "none";
  document.getElementById(`category-btn-confirm-${category_id}`).style.display =
    "inline-grid";
  document.getElementById(`category-btn-cancel-${category_id}`).style.display =
    "inline-grid";
}

function onCategoryCancel(category_id) {
  document
    .querySelectorAll(`.category-input-container-${category_id}`)
    .forEach((el) => el.classList.remove("show"));
  document
    .querySelectorAll(`.category-display-value-${category_id}`)
    .forEach((el) => el.classList.remove("hidden"));

  document.getElementById(`category-btn-edit-${category_id}`).style.display =
    "inline-grid";
  document.getElementById(`category-btn-delete-${category_id}`).style.display =
    "inline-grid";
  document.getElementById(`category-btn-confirm-${category_id}`).style.display = "none";
  document.getElementById(`category-btn-cancel-${category_id}`).style.display = "none";
}

function getCategoryRowData(category_id) {
  const row = document.getElementById(`category-btn-confirm-${category_id}`).closest("tr");
  const name = row.querySelector('input[name="category_name"]')?.value.trim() || "";
  return { row, name };
}

function applyCategoryRowUiUpdate(category_id, data) {
  const displayEl = data.row.querySelector(`.category-display-value-${category_id}`);
  if (displayEl) {
    displayEl.textContent = data.name;
  }
  data.row.dataset.name = data.name.toLowerCase();
}

function onCategoryConfirm(category_id) {
  const data = getCategoryRowData(category_id);
  if (!data.name) {
    alert("Category name is required.");
    return;
  }

  const persistedCategoryId = getPersistedCategoryId(category_id);
  const isUnsavedTempCategory =
    isTemporaryCategoryId(category_id) && persistedCategoryId === null;

  const endpoint = isUnsavedTempCategory
    ? "add_category.php"
    : "update_category.php";

  const fd = new FormData();
  fd.append("name", data.name);
  if (!isUnsavedTempCategory) {
    fd.append("category_id", String(persistedCategoryId ?? category_id));
  }

  fetch(endpoint, { method: "POST", body: fd })
    .then((res) => res.json())
    .then((payload) => {
      if (!payload.ok) {
        throw new Error(payload.message || "Save failed.");
      }

      if (isUnsavedTempCategory && payload.category_id) {
        const realId = Number(payload.category_id);
        insertedCategoryIdByTempId.set(String(category_id), realId);
        data.row.dataset.persistedCategoryId = String(realId);
        const idCell = data.row.querySelector(".category-id");
        if (idCell) {
          idCell.textContent = String(realId);
        }
      }

      applyCategoryRowUiUpdate(category_id, data);
      onCategoryCancel(category_id);
      applyCategoryNameFilter();
    })
    .catch((err) => {
      alert(err.message || "Save failed.");
    });
}

function onCategoryDeleteClick(category_id) {
  const button = document.getElementById(`category-btn-delete-${category_id}`);
  const row = button ? button.closest("tr") : null;
  if (!row) {
    return;
  }

  if (!window.confirm("Delete this category?")) {
    return;
  }

  const persistedCategoryId = getPersistedCategoryId(category_id);
  if (isTemporaryCategoryId(category_id) && persistedCategoryId === null) {
    row.remove();
    return;
  }

  button.disabled = true;

  const fd = new FormData();
  fd.append("category_id", String(persistedCategoryId ?? category_id));

  fetch("delete_category.php", { method: "POST", body: fd })
    .then((res) => res.json())
    .then((payload) => {
      if (!payload.ok) {
        throw new Error(payload.message || "Delete failed.");
      }

      if (isTemporaryCategoryId(category_id)) {
        insertedCategoryIdByTempId.delete(String(category_id));
      }
      row.remove();
    })
    .catch((err) => {
      alert(err.message || "Delete failed.");
      button.disabled = false;
    });
}

const inventoryAddBtn = document.getElementById("inventoryAddBtn");
if (inventoryAddBtn) {
  inventoryAddBtn.addEventListener("click", onAddBookClick);
}

const inventorySearchInput = document.getElementById("inventorySearch");
if (inventorySearchInput) {
  inventorySearchInput.addEventListener("input", applyInventoryTitleFilter);
}

const categoryAddBtn = document.getElementById("categoryAddBtn");
if (categoryAddBtn) {
  categoryAddBtn.addEventListener("click", onCategoryAddClick);
}

const categorySearchInput = document.getElementById("categorySearch");
if (categorySearchInput) {
  categorySearchInput.addEventListener("input", applyCategoryNameFilter);
}
