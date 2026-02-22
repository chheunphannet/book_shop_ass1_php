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
    foreach ($books as $book){
    ?>
      <div class="cart-item-card">
        <div>
            <div class="cart-item-cover" style="background-image: url('<?= $book['book_cover_base64'] ?>');"></div>
          
            <div class="cart-item-info">
              <h4 class="cart-item-title"><?= $book['title'] ?></h4>
              <p class="cart-item-meta">Qty: <?= $_SESSION['cart'][$book['book_id']] ?> x $<?= $book['unit_price'] ?></p>
            </div>   
          </div>  
        <span class="material-symbols-outlined" id="remove-from-cart" onclick="removeFromCart(<?= (int)$book['book_id'] ?>)">delete</span>
      </div>
    <?php
    }
  }else{
    echo 'no_item';
  }
