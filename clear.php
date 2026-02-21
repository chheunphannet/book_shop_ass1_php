<?php
  session_start();
  unset($_SESSION['cart']); // This specifically deletes the cart drawer
  header("Location: home.php"); // Send you back to the shop
  exit();
?>