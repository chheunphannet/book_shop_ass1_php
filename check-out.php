<?php
  require __DIR__ . '/service.php';
  session_start();

  $service = new DatabaseService();
  $staff_name = 'System Guest';
  $sale_date = date('Y-m-d H:i:s');

  if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo 'Order_failed';
    exit;
  }

  try {
    $total_amount = 0.0;

    foreach ($_SESSION['cart'] as $book_id => $qty) {
      $price = $service->getCurrentPrice((int)$book_id);
      $total_amount += $price * (int)$qty;
    }

    if ($total_amount <= 0) {
      echo 'Order_failed';
      exit;
    }

    $sale_id = $service->insertSale($sale_date, $staff_name, $total_amount);

    foreach ($_SESSION['cart'] as $book_id => $qty) {
      $book_id = (int)$book_id;
      $qty = (int)$qty;
      $price = $service->getCurrentPrice($book_id);
      $service->insetSaleDetail($book_id, $sale_id, $qty, $price, $price * $qty);
      $service->updateStock($book_id, $qty);
    }

    unset($_SESSION['cart'], $_SESSION['total']);
    echo 'Order_success';
  } catch (Throwable $e) {
    echo 'Order_failed';
  }
