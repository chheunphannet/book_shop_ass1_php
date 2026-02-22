<?php
session_start();

$book_id = $_GET['book_id'] ?? 0;

if(isset($_SESSION['cart'][$book_id])){
  unset($_SESSION['cart'][$book_id]);
}

?>