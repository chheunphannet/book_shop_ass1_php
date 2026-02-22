<?php
  require __DIR__ . '/service.php';
  $service = new DatabaseService();

  $offset = isset($_GET['offset']) ? (int)($_GET['offset']) : 0;
  $limit = 50;

  $books = $service->getBooks($limit, $offset);

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
                    <h5>$<?php echo htmlspecialchars($book['unit_price']); ?></h5>
                    <div class="unit-container">
                        <button type="button" onclick="changeQty(<?php echo (int)$book['book_id']; ?>, -1, <?php echo (int)$book['stock_quantity']; ?>)">-</button>
                        <input type="number" id="qty_display_<?php echo $book['book_id'] ?>" class="qty_display" value="1" min="1" readonly>
                        <button type="button" onclick="changeQty(<?php echo (int)$book['book_id']; ?>, 1, <?php echo (int)$book['stock_quantity']; ?>)">+</button>
                    </div>
                    <button id="add-to-cart" onclick="addToCart(<?php echo (int)$book['book_id'] ?>, <?php echo (int)$book['stock_quantity']; ?>)">Add To Card</button>
                </div>     
            </div>
        </div>
        <?php
    }
} else {
    echo "no_more";
}
