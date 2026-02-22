<?php
    require __DIR__ . '/service.php';
    session_start();
    $service = new DatabaseService();
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
            <a class="container-brandlogo" href="/book_shop_ass1_php">
                <span class="material-symbols-outlined icon-lg">book_2</span>
                <span class="logo-name">Bookrak</span>
            </a>
            
            <div class="header-aside">
                <span class="material-symbols-outlined category-icon">category</span>
                <h3 class="header-aside-content">Book category</h3>
            </div>

            <ul class="list-container-slide" type="none">
                <?php 
                $offset = 0;
                $limit = 12;
                $category = $service->getCategory($limit, $offset);
                foreach ($category as $cat):
                ?>
                    <li class="catagory-list" onclick="loadBooksByCategory(<?php echo $cat['category_id']; ?>)">
                        <span class="material-symbols-outlined list-icon">keyboard_double_arrow_right</span>
                        <p class="category-text"><?php echo $cat['name']; ?></p>
                    </li>
                <?php   
                    endforeach; 
                ?>
            </ul>
                
                <div class="view-more-container" onclick="loadCategoryMore()">
                    <span class="material-symbols-outlined more-icon">more_horiz</span>
                       <p class='view-more-content'>View more</p>
                </div>
                

       </aside>

        <div class="open-aside">
            <span class="material-symbols-outlined">chevron_left</span>
       </div>  

       <div class="main-content">
            <nav class="nav-container">
                <a class="container-brandlogo-main" href="/book_shop_ass1_php">
                    <span class="material-symbols-outlined icon-lg">book_2</span>
                    <span class="logo-name">Bookrak</span>
                </a>
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
                        <span class="basket-count"></span>
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
                                <h5>$<?php echo htmlspecialchars($book['unit_price']); ?></h5>
                                <div class="unit-container">
                                    <button type="button" onclick="changeQty(<?php echo (int)$book['book_id']; ?>, -1, <?php echo (int)$book['stock_quantity']; ?>)">-</button>
                                    <input type="number" id="qty_display_<?php echo $book['book_id'] ?>" class="qty_display" value="1" min="1" readonly>
                                    <button type="button" onclick="changeQty(<?php echo (int)$book['book_id']; ?>, 1, <?php echo (int)$book['stock_quantity']; ?>)">+</button>
                                </div>
                                <button id="add-to-cart" onclick="addToCart(<?php echo (int)$book['book_id'] ?>, <?php echo (int)$book['stock_quantity']; ?>)">Add To Card</button>
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
                            <div class="unit-container">
                                    <button type="button" onclick="changeQty(<?php echo (int)$book['book_id']; ?>, -1, <?php echo (int)$book['stock_quantity']; ?>)">-</button>
                                    <input type="number" id="qty_display_<?php echo $book['book_id'] ?>" class="qty_display" value="1" min="1" readonly>
                                    <button type="button" onclick="changeQty(<?php echo (int)$book['book_id']; ?>, 1, <?php echo (int)$book['stock_quantity']; ?>)">+</button>
                            </div>
                            <button id="add-to-cart" onclick="addToCart(<?php echo (int)$book['book_id'] ?>, <?php echo (int)$book['stock_quantity']; ?>)">Add To Card</button>
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
                        $books = $service->getBooks(20, 0);
                        foreach ($books as $book):
                    ?>
                        <div class="book-cards">
                            <div class="book-cover-card" style="background-image: url('<?php echo $book['book_cover_base64']; ?>')"></div>
                            <div class="book-context">
                                <div>
                                    <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                                    <h4><?php echo htmlspecialchars($book['name']); ?></h4>
                                    <p>Stock: <?php echo htmlspecialchars($book['stock_quantity']); ?></p>
                                    <p>Pages: <?php echo htmlspecialchars($book['page_number']); ?></p>
                                </div>
                                <div class="price-addtocard">
                                    <h5>$<?php echo htmlspecialchars($book['unit_price']); ?></h5>
                                    <div class="unit-container">
                                        <button type="button" onclick="changeQty(<?php echo (int)$book['book_id']; ?>, -1, <?php echo (int)$book['stock_quantity']; ?>)">-</button>
                                        <input type="number" id="qty_display_<?php echo $book['book_id'] ?>" class="qty_display" value="1" min="1" readonly>
                                        <button type="button" onclick="changeQty(<?php echo (int)$book['book_id']; ?>, 1, <?php echo (int)$book['stock_quantity']; ?>)">+</button>
                                    </div>
                                    <button id="add-to-cart" onclick="addToCart(<?php echo (int)$book['book_id'] ?>, <?php echo (int)$book['stock_quantity']; ?>)">Add To Card</button>
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

       <div id="cart-backdrop" class="cart-backdrop"></div>
       <aside id="cart-drawer" class="cart-drawer">
            <div class="cart-header">
                <div class="header-aside">
                    <span class="material-symbols-outlined category-icon">shopping_cart</span>
                    <h3 class="header-aside-content">Basket</h3>
                </div>
                <button type="button" class="cart-close" aria-label="Close basket">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="cart-body">
            </div>
            <div class="cart-footer">
                <p class="total-price"></p>
                <button type="button" class="checkout-btn" onclick="placeOrder()">Checkout</button>
            </div>
       </aside>

       <div id="order-modal" class="order-modal">
            <div class="order-modal-box">
                <span id="order-modal-icon" class="material-symbols-outlined order-modal-icon">check_circle</span>
                <h3 id="order-modal-title">Order successful</h3>
                <p id="order-modal-message">Your order has been placed successfully.</p>
                <button type="button" class="order-modal-btn" onclick="closeOrderModal()">OK</button>
            </div>
       </div>
    </div>

    <script src="./script.js"></script>
</body>
</html>

