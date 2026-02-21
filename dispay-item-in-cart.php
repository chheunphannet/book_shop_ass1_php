<?php
  require __DIR__ . '/service.php';
  session_start();
  $service = new DatabaseService();

  $books = [];

  if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);

    $books = $service -> getBooksInIds($ids);
  }
  
  if (!empty($books)){
    $total = 0;
    foreach ($books as $book){
      $total += ((float)$book['unit_price']) * ((int)$_SESSION['cart'][$book['book_id']]);
    ?>
      <div class="cart-item-card">
        <div class="cart-item-cover" style="background-image: url('<?= $book['book_cover_base64'] ?>');"></div>

        <div class="cart-item-info">
          <h4 class="cart-item-title"><?= $book['title'] ?></h4>
          <p class="cart-item-meta">Qty: <?= $_SESSION['cart'][$book['book_id']] ?> x $<?= $book['unit_price'] ?></p>
        </div>
      </div>
    <?php
    }
    ?>
      <div data-cart-total="<?= $total ?>" hidden></div>
    <?php
  }else{
    echo 'no_item';
  }
