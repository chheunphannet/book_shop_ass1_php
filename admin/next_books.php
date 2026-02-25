<?php 
  require_once __DIR__ . "/../service.php";
  $service = new DatabaseService();

  $limit = isset($_GET["limit"]) ? (int) $_GET["limit"] : 10;
  $offset = isset($_GET["offset"]) ? (int) $_GET["offset"] : 0;

  $inventory = $service->getInventory($limit, $offset);
  $category = $service->getAllCategory();


if(!empty($inventory)){
  foreach ($inventory as $book){
    ?>
      <?php
      $stockStatusClass = str_replace(' ', '-', strtolower($book['status']));
      $searchTitle = strtolower($book['title']);
      $searchCategory = strtolower($book['name']);
      ?>
      <tr data-title="<?php echo htmlspecialchars($searchTitle); ?>" data-category="<?php echo htmlspecialchars($searchCategory); ?>">
        <td>
            <img
                class="book-cover display-value display-value-<?php echo (int) $book['book_id']; ?>"
                src="<?php echo htmlspecialchars($book['book_cover_base64']); ?>"
                alt="<?php echo htmlspecialchars($book['title']); ?> cover"
            >
            <div class="input-file input-container input-container-<?php echo (int) $book['book_id']; ?>">
              <input type="file" accept="image/*" name="book_cover">
            </div>
        </td>
        <td>
          <span class="display-value field-title display-value-<?php echo (int) $book['book_id']; ?>">
              <?php echo htmlspecialchars($book['title']); ?>
          </span>
          <div class="input-container input-container-<?php echo (int) $book['book_id']; ?>">
              <input type="text" name="title" value="<?php echo htmlspecialchars($book['title']); ?>">
          </div>
        </td>
        <td>
          <span class="display-value field-category display-value-<?php echo (int) $book['book_id']; ?>">
              <?php echo htmlspecialchars($book['name']); ?>
          </span>
          <select class="dropdown-cat input-container input-container-<?php echo (int) $book['book_id']; ?>" name="category">
            <?php foreach ($category as $cat): ?>
              <option value="<?php echo htmlspecialchars($cat['name']); ?>" <?php echo $cat['name'] === $book['name'] ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($cat['name']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </td>
        <td>
          <span class="display-value field-pages display-value-<?php echo (int) $book['book_id']; ?>">
              <?php echo $book['page_number'] !== null ? (int) $book['page_number'] : '-'; ?>
          </span>
          <div class="input-container input-container-<?php echo (int) $book['book_id']; ?>">
              <input type="number" name="page_number" value="<?php echo $book['page_number'] !== null ? htmlspecialchars((string) $book['page_number']) : ''; ?>" min="0">
          </div>
        </td>
        <td>
          <span class="display-value field-price display-value-<?php echo (int) $book['book_id']; ?>">
              $<?php echo number_format((float) $book['unit_price'], 2); ?>
          </span>
          <div class="input-container input-container-<?php echo (int) $book['book_id']; ?>">
              <input type="number" name="unit_price" value="<?php echo htmlspecialchars($book['unit_price']); ?>">
          </div>
        </td>
        <td>
          <span class="display-value field-stock display-value-<?php echo (int) $book['book_id']; ?>">
              <?php echo (int) $book['stock_quantity']; ?>
          </span>
          <div class="input-container input-container-<?php echo (int) $book['book_id']; ?>">
              <input type="number" name="stock_quantity" value="<?php echo htmlspecialchars($book['stock_quantity']); ?>">
          </div>
        </td>
        <td>
            <span class="badge stock display-value field-status display-value-<?php echo (int) $book['book_id']; ?> <?php echo htmlspecialchars($stockStatusClass); ?>">
                <?php echo htmlspecialchars($book['status']); ?>
            </span>
        </td>
        <td class="action-cell">
            <div class="action-buttons">
                <button type="button" class="icon-btn edit" id="btn-edit-<?php echo (int) $book['book_id']; ?>" onclick="onEditClick(<?php echo (int) $book['book_id']; ?>)" aria-label="Edit <?php echo htmlspecialchars($book['title']); ?>">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M3 17.25V21h3.75L17.8 9.94l-3.75-3.75L3 17.25zm18-11.5a1 1 0 0 0 0-1.41l-1.34-1.34a1 1 0 0 0-1.41 0L16.13 4.1l3.75 3.75L21 5.75z"></path>
                    </svg>
                </button>
                <button type="button" class="icon-btn delete" id="btn-delete-<?php echo (int) $book['book_id']; ?>" onclick="onDeleteClick(<?php echo (int) $book['book_id']; ?>)" aria-label="Delete <?php echo htmlspecialchars($book['title']); ?>">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M6 7h12l-1 14H7L6 7zm3-3h6l1 2H8l1-2z"></path>
                    </svg>
                </button>
                <button type="button" class="icon-btn confirm" id="btn-confirm-<?php echo (int) $book['book_id']; ?>" onclick="onConfirm(<?php echo (int) $book['book_id']; ?>)">
                    confirm
                </button>
                <button type="button" class="icon-btn cancel" id="btn-cancel-<?php echo (int) $book['book_id']; ?>" onclick="onCancel(<?php echo (int) $book['book_id']; ?>)">
                    cancel
                </button>
            </div>
        </td>
      </tr>
    <?php
  }
}else{
  echo "no_more";
}
