<?php
  require __DIR__ ."/service.php";

  $service = new DatabaseService();

  $category_id = (int)($_GET["category_id"] ?? 0);

  $books = [];
  if($category_id != 0) {
    $books = $service->getBooksByCategoryId($category_id);
  }

  if (!empty($books)) {
    foreach ($books as $book) {
        ?>
        <div class="book-cards">
            <div class="book-cover-card" style="background-image: url('<?= $book['book_cover_base64'] ?>')"></div>
            <div class="book-context">
                <div>
                    <h3><?= htmlspecialchars($book['title']) ?></h3>
                    <h4><?= htmlspecialchars($book['name']) ?></h4>
                    <p>Stock: <?= htmlspecialchars($book['stock_quantity']) ?></p>
                    <p>Pages: <?= htmlspecialchars($book['page_number']) ?></p>
                </div>
                <div class="price-addtocard">
                    <h5>$<?= htmlspecialchars($book['unit_price']) ?></h5>
                    <div class="unit-container">
                        <button type="button" onclick="changeQty(<?= (int)$book['book_id'] ?>, -1)">-</button>
                        <input type="number" id="qty_display_<?= $book['book_id'] ?>" class="qty_display" value="1" min="1" readonly>
                        <button type="button" onclick="changeQty(<?= (int)$book['book_id'] ?>, 1)">+</button>
                    </div>
                    <button>Add To Card</button>
                </div>   
            </div>
        </div>
        <?php
    }
} else {
    echo "not_found";
}
  
