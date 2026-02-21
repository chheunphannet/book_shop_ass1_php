<?php
 session_start();

 $book_id = $_GET['book_id'];
 $qty = $_GET['qty'];
 $real_qty = $_GET['real_qty'];

 if(!isset($_SESSION['cart'])){
    $_SESSION['cart'] = [];
 }

 if(isset($_SESSION['cart'][$book_id])){
   $old_qty = $_SESSION['cart'][$book_id];

   if($old_qty + $qty > $real_qty){
   $_SESSION['cart'][$book_id] = $real_qty;
   }else{
   $_SESSION['cart'][$book_id] += $qty;
   }

 }else {
   $_SESSION['cart'][$book_id] = $qty;
}

