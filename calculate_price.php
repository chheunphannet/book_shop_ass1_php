<?php
  require __DIR__ . '/service.php';
  session_start();
  $service = new DatabaseService();

  $total = 0;

  if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    $books = $service->getBooksInIds($ids);

    foreach ($books as $book) {
      $qty = (int)$_SESSION['cart'][$book['book_id']];
      $total += ((float)$book['unit_price']) * $qty;
    }
  }

  echo number_format($total, 2, '.', '');
