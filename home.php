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
       <aside class="sidebar">
            <div class="container-brandlogo">
                <span class="material-symbols-outlined icon-lg">book_2</span>
                <span class="logo-name">Bookrak</span>
            </div>
            
            <div class="header-aside">
                <h3 class="header-aside-content"></h3>
            </div>
            <ul class="list-container" type="none">
                <li class="catagory-list">
                    <img src="" alt="">
                    <p class="category-text">hello</p>
                </li>
            </ul>
       </aside>

        <div class="open-aside">
            <span class="material-symbols-outlined">chevron_left</span>
       </div>  
       <div class="main-content">
            <nav class="nav-container">
                <div class="container-brandlogo">
                <span class="material-symbols-outlined icon-lg">book_2</span>
                <span class="logo-name">Bookrak</span>
            </div>
                <form class="search-container" action="" method="post">
                    <span class="material-symbols-outlined">search</span>
                    <input type="text" name="search" class="search-input">
                </form>
                <ul class="list-container"  type="none">
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