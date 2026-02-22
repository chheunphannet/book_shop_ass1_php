<?php
  session_start();

  $basket_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;

  echo (int) $basket_count;
