<?php 
  require __DIR__ . '/service.php';
  $service = new DatabaseService();

  $search_keyword = isset($_GET['search']) ? $_GET['search'] : '';
  $books = [];
  if ($search_keyword != '') {
    $books = $service->searchBooksByTitle($search_keyword);
  }

  if(!empty($books)) {
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
}else {
    echo "not_found";
}