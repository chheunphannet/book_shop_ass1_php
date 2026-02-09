<?php
    $category = [
        "Fiction",
        "Non-Fiction",
        "Science Fiction",
        "Fantasy",
        "Mystery",
        "Biography",
        "History",
        "Children's Books",
        "Romance",
        "Thriller",
        "Horror",
        "Poetry",
        "Self-Help",
        "Graphic Novels",
        "Young Adult",
        "Cookbooks",
        "Travel",
        "Business",
    ];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soft Mesh Background</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"/>
    <link rel="stylesheet" href="./home.css">
</head>
<body>

    <div class="background-blob blob-1"></div>
    <div class="background-blob blob-2"></div>
    <div class="background-blob blob-3"></div>
    <div class="background-blob blob-4"></div>

    <div class="glass-container">
       <aside class="sidebar collapsed">
            <div class="container-brandlogo">
                <span class="material-symbols-outlined icon-lg">book_2</span>
                <span class="logo-name">Bookrak</span>
            </div>
            
            <div class="header-aside">
                <span class="material-symbols-outlined category-icon">category</span>
                <h3 class="header-aside-content">Book category</h3>
            </div>

            <ul class="list-container-slide" type="none">
                <?php 
                $count = 0;
                foreach ($category as $cat):
                 if ($count++ >= 12) break; ?>
                    <li class="catagory-list">
                        <span class="material-symbols-outlined list-icon">keyboard_double_arrow_right</span>
                        <p class="category-text"><?php echo $cat; ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>

       </aside>

        <div class="open-aside">
            <span class="material-symbols-outlined">chevron_left</span>
       </div>  

       <div class="main-content">
            <nav class="nav-container">
                <div class="container-brandlogo-main">
                    <span class="material-symbols-outlined icon-lg">book_2</span>
                    <span class="logo-name">Bookrak</span>
                </div>
                <form class="search-container" action="" method="post">
                    <span class="material-symbols-outlined">search</span>
                    <input type="text" name="search" class="search-input">
                </form>
                <ul class="list-container-main"  type="none">
                    <li>Shop</li>
                    <li>Blog</li>
                    <li>About Us</li>
                    <li class="basket">
                        <span>Basket</span> 
                        <span class="material-symbols-outlined">shopping_cart</span>
                        <span class="basket-count">0</span>
                    </li>
                </ul>

            </nav>
       </div>
    </div>

    <script src="./script.js"></script>
</body>
</html>
