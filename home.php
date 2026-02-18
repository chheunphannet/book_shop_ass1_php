<?php
    require __DIR__ . '/service.php';
    $limit = 12;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    if ($offset < 0) {
        $offset = 0;
    }
    $service = new DatabaseService();
    $category = $service->getCategory($limit, $offset);
    
    $current_length = count($category);
    $category_length = $service -> getLengthCategory();
    $isDisplay_show_more = true;

    $left_length = $category_length % $limit;

    if($current_length == $left_length) {
        $isDisplay_show_more = false;
    }else if ($offset == $category_length){
        $isDisplay_show_more = false;
    }

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
       <aside class="sidebar">
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
                foreach ($category as $cat):
                ?>
                    <li class="catagory-list">
                        <span class="material-symbols-outlined list-icon">keyboard_double_arrow_right</span>
                        <p class="category-text"><?php echo $cat['name']; ?></p>
                    </li>
                <?php 
                    endforeach; 
                ?>
            </ul>
            <?php
                if ($isDisplay_show_more) {
            ?>
            <div class="view-more-container">
                <span class="material-symbols-outlined more-icon">more_horiz</span>
                <?php 
                    $nextOffset = $offset + $limit;
                    echo "<a href='?offset=" . $nextOffset . "' class='view-more-content'>View more</a>";
                ?>
            </div>
            <?php 
                }
            ?>

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
                <form class="search-container" action="" method="get">
                    <span class="material-symbols-outlined">search</span>
                    <input type="text" name="search" class="search-input">
                    <button id="search-btn" type="submit" onclick="searchTitle()">
                        <span class="material-symbols-outlined">search</span>
                    </button>  
                </form>
                    
                <ul class="list-container-main" type="none">
                    <li>Admin Dashboard</li>
                    <li class="basket">
                        <span>Basket</span> 
                        <span class="material-symbols-outlined">shopping_cart</span>
                        <span class="basket-count">0</span>
                    </li>
                </ul>

            </nav>
            <div class="suggest_container">
                    
                    <?php
                        $books = $service->getRandomBook(1);
                        if (!empty($books)):
                            $book = $books[0];
                    ?>  
                        <div class="context">
                            <h1><?php echo $book['title'] ?></h1>
                            <h2><?php echo $book['name'] ?></h2>
                            <div class="price-addtocard">
                                <h5>$<?php echo $book['unit_price'] ?></h5>
                                <button>Add To Card</button>
                            </div>   
                        </div>
                        <div class="book-cover" style="background-image: url('<?php echo $book['book_cover_base64']; ?>');"></div>
                    <?php endif; ?>
                    
            </div>
            <h3 id="you-may-like">You may like</h3>
            <div class="book-cover-grid">
                <?php 
                    $book_in_box = $service->getRandomBook(5);
                    if (!empty($book_in_box)):
                        foreach ($book_in_box as $book):
                ?>
                    <div class="book-cover-card" style="background-image: url('<?php echo $book['book_cover_base64']; ?>')">
                        <div class="book-cover-details">
                            <h5>$<?php echo $book['unit_price'] ?></h5>
                            <button><a href="">Add To Card</a></button>
                        </div>
                    </div>
                    <?php 
                        endforeach; 
                    endif; 
                    ?>
            </div>
            <h3 id="all-books">All books</h3>
            <div class="book-card-container">
                <div id="book-cards">
                    <?php
                        $books = $service->getBooks(50, 0);
                        foreach ($books as $book):
                    ?>
                        <div class="book-cards">
                            <div class="book-cover-card" style="background-image: url('<?php echo $book['book_cover_base64']; ?>')"></div>
                            <div class="book-context">
                                <div>
                                    <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                                    <h4><?php echo htmlspecialchars($book['name']); ?></h4>
                                </div>
                                <div class="price-addtocard">
                                    <h5>$<?php echo htmlspecialchars($book['unit_price']); ?></h5>
                                    <button>Add To Card</button>
                                </div>   
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
               <button id="load-more-btn" onclick="loadMore()">
                    View More
               </button>
            </div>
       </div>
    </div>

    <script src="./script.js"></script>
</body>
</html>

