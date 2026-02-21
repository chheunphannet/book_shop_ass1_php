<?php
  require __DIR__ ."/service.php";

  $service = new DatabaseService();

  $limit = (int) $_GET['limit'] ?? 12;
  $offset = (int) $_GET['offset'] ?? $limit;

  $category = $service -> getCategory($limit, $offset) ?? [];
  
  $current_length = count($category);
  $category_length = $service -> getLengthCategory();
  $isDisplay_show_more = true;

  $left_length = $category_length % $limit;

    if($current_length == $left_length) {
        $isDisplay_show_more = false;
    }else if ($offset == $category_length){
        $isDisplay_show_more = false;
    }

  
  if (!empty($category)) {
    foreach ($category as $cat) {
      ?>
      <li class="catagory-list" onclick="loadBooksByCategory(<?= htmlspecialchars($cat['category_id']) ?>)">
          <span class="material-symbols-outlined list-icon">keyboard_double_arrow_right</span>
          <p class="category-text"><?= htmlspecialchars($cat['name']) ?></p>
      </li>
      <?php
      if(!$isDisplay_show_more){
          ?>
            <style>
              .view-more-container{
                display: none;
              }
            </style>
          <?php
      }
    }
  }else{
    echo "no_more";
  }
  
