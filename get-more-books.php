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
                </div>
                <div class="price-addtocard">
                    <h5>$<?= htmlspecialchars($book['unit_price']) ?></h5>
                    <button>Add To Card</button>
                </div>   
            </div>
        </div>
        <?php
    }
} else {
    echo "no_more";
}
