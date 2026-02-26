<?php
    require_once __DIR__ . "/../service.php";
    $service = new DatabaseService();
    $limit = 10;
    $offset = 0;
    $inventory = $service->getInventory($limit, $offset);
    $category = $service->getAllCategory();
    $latestOrdersRaw = $service->getLatestOrders(5);
    $popularBooksRaw = $service->getMostPopularBooks(5);
    $dashboardStats = $service->getDashboardStats();

    $latestOrders = array_map(static function (array $order): array {
        $timestamp = strtotime((string) ($order['sale_date'] ?? ''));
        $saleDate = $timestamp ? date('Y-m-d', $timestamp) : (string) ($order['sale_date'] ?? '');

        return [
            'date' => $saleDate,
            'person' => (string) ($order['staff_name'] ?? ''),
            'total' => '$' . number_format((float) ($order['total_amount'] ?? 0), 2),
            'status' => 'Completed',
        ];
    }, $latestOrdersRaw);
$popularBooks = array_map(static function (array $book): array {
    return [
        'title' => (string) ($book['title'] ?? ''),
        'sold' => (int) ($book['sold_qty'] ?? 0),
    ];
}, $popularBooksRaw);
$stats = [
    [
        'label' => 'Total Revenue',
        'value' => '$' . number_format((float) ($dashboardStats['revenue'] ?? 0), 2),
        'class' => 'revenue',
    ],
    [
        'label' => 'Total Sales',
        'value' => number_format((int) ($dashboardStats['total_sales'] ?? 0)),
        'class' => 'sales',
    ],
    [
        'label' => 'Low Stock',
        'value' => number_format((int) ($dashboardStats['low_stock'] ?? 0)),
        'class' => 'low-stock',
    ],
    [
        'label' => 'Books Available',
        'value' => number_format((int) ($dashboardStats['books_available'] ?? 0)),
        'class' => 'books',
    ],
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="admin-layout">
        <aside class="sidebar">
            <div class="brand">BookShop Admin</div>
            <nav class="sidebar-nav">
                <a href="admin.php" class="nav-link active">Dashboard Home</a>
                <a href="#inventory-management" class="nav-link">Inventory</a>
                <a href="#category-management" class="nav-link">Categories</a>
                <a href="orders.php" class="nav-link">Order History</a>
            </nav>
        </aside>

        <div class="content-area">
            <header class="topbar">
                <h1>Dashboard Overview</h1>
                <div class="topbar-right">
                    <div class="admin-profile">
                        <span class="avatar">AD</span>
                        <div class="admin-info">
                            <strong>Admin Name</strong>
                            <small>Super Admin</small>
                        </div>
                    </div>
                    <button type="button" class="logout-btn"><a href="/book_shop_ass1_php/home.php">Logout</a></button>
                </div>
            </header>

            <main class="main-content">
                <section class="stats-grid">
                    <?php foreach ($stats as $card): ?>
                        <article class="stat-card <?php echo htmlspecialchars($card['class']); ?>">
                            <h2><?php echo htmlspecialchars($card['label']); ?></h2>
                            <p><?php echo htmlspecialchars($card['value']); ?></p>
                        </article>
                    <?php endforeach; ?>
                </section>

                <section class="panel-grid">
                    <article class="panel">
                        <div class="panel-header">
                            <h3>Latest 5 Orders</h3>
                            <a href="orders.php" class="panel-link">View all</a>
                        </div>
                        <div class="table-wrap">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Customer / Staff</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($latestOrders)): ?>
                                        <?php foreach ($latestOrders as $order): ?>
                                            <?php $orderStatusClass = str_replace(' ', '-', strtolower($order['status'])); ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($order['date']); ?></td>
                                                <td><?php echo htmlspecialchars($order['person']); ?></td>
                                                <td><?php echo htmlspecialchars($order['total']); ?></td>
                                                <td>
                                                    <span class="badge order <?php echo htmlspecialchars($orderStatusClass); ?>">
                                                        <?php echo htmlspecialchars($order['status']); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4">No orders yet.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </article>

                    <article class="panel">
                        <div class="panel-header">
                            <h3>Most Popular Books</h3>
                        </div>
                        <ul class="popular-list">
                            <?php if (!empty($popularBooks)): ?>
                                <?php foreach ($popularBooks as $index => $book): ?>
                                    <li>
                                        <span class="rank">#<?php echo $index + 1; ?></span>
                                        <div class="popular-meta">
                                            <strong><?php echo htmlspecialchars($book['title']); ?></strong>
                                            <small>Based on sales volume</small>
                                        </div>
                                        <span class="sold"><?php echo (int) $book['sold']; ?> sold</span>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li>
                                    <span class="rank">-</span>
                                    <div class="popular-meta">
                                        <strong>No books yet</strong>
                                        <small>Based on sales volume</small>
                                    </div>
                                    <span class="sold">0 sold</span>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </article>
                </section>

                <section id="inventory-management" class="panel">
                    <div class="panel-header inventory-header">
                        <div>
                            <h3>Inventory Management</h3>
                            <p>Search and manage books with quick actions.</p>
                        </div>
                        <div class="inventory-tools">
                            <input type="search" id="inventorySearch" placeholder="Search by title">
                            <button type="button" class="add-btn" id="inventoryAddBtn">+ Add Book</button>
                        </div>
                    </div>

                    <div class="table-wrap">
                        <table class="data-table" id="inventoryTable">
                            <thead>
                                <tr>
                                    <th>Book Cover</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Pages</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="inventoryTbody">
                                <?php foreach ($inventory as $book): ?>
                                    <?php $stockStatusClass = str_replace(' ', '-', strtolower($book['status'])); ?>
                                    <?php $searchTitle = strtolower($book['title']); ?>
                                    <tr data-title="<?php echo htmlspecialchars($searchTitle); ?>">
                                        <td>
                                            <img
                                                    class="book-cover display-value display-value-<?php echo (int) $book['book_id']; ?>"
                                                    src="<?php echo htmlspecialchars($book['book_cover_base64']); ?>"
                                                    alt="<?php echo htmlspecialchars($book['title']); ?> cover"
                                                >
                                                <div class="input-file input-container input-container-<?php echo (int) $book['book_id']; ?>">
                                                <input type="file" accept="image/*" name="book_cover">
                                                </div>
                                        </td>
                                        <td>
                                            <span class="display-value field-title display-value-<?php echo (int) $book['book_id']; ?>">
                                                <?php echo htmlspecialchars($book['title']); ?>
                                            </span>
                                            <div class="input-container input-container-<?php echo htmlspecialchars($book['book_id']); ?>">
                                                <input type="text" id="edit-input-<?php echo htmlspecialchars($book['book_id']); ?>" name="title" value="<?php echo htmlspecialchars($book['title']); ?>">
                                            </div>
                                        </td>
                                        <td>
                                            <span class="display-value field-category display-value-<?php echo (int) $book['book_id']; ?>">
                                                <?php echo htmlspecialchars($book['name']); ?>
                                            </span>
                                            <select class="dropdown-cat input-container input-container-<?php echo htmlspecialchars($book['book_id']); ?>" name="category" id="edit-input-<?php echo htmlspecialchars($book['book_id'])?>">
                                                    <?php if (empty($category)): ?>
                                                        <option value="">No category available</option>
                                                    <?php else: ?>
                                                        <?php foreach ($category as $cat): ?>
                                                            <option value="<?php echo htmlspecialchars($cat['name']); ?>" <?php echo $cat['name'] === $book['name'] ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($cat['name']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <span class="display-value field-pages display-value-<?php echo (int) $book['book_id']; ?>">
                                                <?php echo $book['page_number'] !== null ? (int) $book['page_number'] : '-'; ?>
                                            </span>
                                            <div class="input-container input-container-<?php echo htmlspecialchars($book['book_id']); ?>">
                                                <input type="number" name="page_number" value="<?php echo $book['page_number'] !== null ? htmlspecialchars((string) $book['page_number']) : ''; ?>" min="0">
                                            </div>
                                        </td>
                                        <td>
                                            <span class="display-value field-price display-value-<?php echo (int) $book['book_id']; ?>">
                                                $<?php echo number_format((float) $book['unit_price'], 2); ?>
                                            </span>
                                            <div class="input-container input-container-<?php echo htmlspecialchars($book['book_id']); ?>" >
                                                <input type="number" name="unit_price" value="<?php echo htmlspecialchars($book['unit_price']); ?>">
                                            </div>
                                        </td>
                                        <td>
                                            <span class="display-value field-stock display-value-<?php echo (int) $book['book_id']; ?>">
                                                <?php echo (int) $book['stock_quantity']; ?>
                                            </span>
                                            <div class="input-container input-container-<?php echo htmlspecialchars($book['book_id']); ?>">
                                                <input type="number" id="edit-input-<?php echo htmlspecialchars($book['book_id']); ?>" name="stock_quantity" value="<?php echo htmlspecialchars($book['stock_quantity']); ?>">
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge stock display-value field-status display-value-<?php echo (int) $book['book_id']; ?> <?php echo htmlspecialchars($stockStatusClass); ?>">
                                                <?php echo htmlspecialchars($book['status']); ?>
                                            </span>
                                        </td>
                                        <td class="action-cell">
                                            <div class="action-buttons">
                                                <button type="button" class="icon-btn edit" id="btn-edit-<?php echo $book['book_id']; ?>"  onclick="onEditClick(<?php echo (int)$book['book_id']; ?>)">
                                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                                        <path d="M3 17.25V21h3.75L17.8 9.94l-3.75-3.75L3 17.25zm18-11.5a1 1 0 0 0 0-1.41l-1.34-1.34a1 1 0 0 0-1.41 0L16.13 4.1l3.75 3.75L21 5.75z"></path>
                                                    </svg>
                                                </button>
                                                <button type="button" class="icon-btn delete" id="btn-delete-<?php echo $book['book_id']; ?>" onclick="onDeleteClick(<?php echo (int)$book['book_id']; ?>)">
                                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                                        <path d="M6 7h12l-1 14H7L6 7zm3-3h6l1 2H8l1-2z"></path>
                                                    </svg>
                                                </button>
                                                 <button type="button" class="icon-btn confirm" id="btn-confirm-<?php echo $book['book_id']; ?>"  onclick="onConfirm(<?php echo (int)$book['book_id']; ?>)">
                                                    confirm
                                                </button>
                                                <button type="button" class="icon-btn cancel" id="btn-cancel-<?php echo $book['book_id']; ?>" onclick="onCancel(<?php echo (int)$book['book_id']; ?>)">
                                                    cancel
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                        <button id="next-btn" onclick="newBooks()">Next...</button>
                </section>

                <section id="category-management" class="panel">
                    <div class="panel-header inventory-header">
                        <div>
                            <h3>Category Management</h3>
                            <p>Add, edit, and delete categories.</p>
                        </div>
                        <div class="inventory-tools">
                            <input type="search" id="categorySearch" placeholder="Search by category name">
                            <button type="button" class="add-btn" id="categoryAddBtn">+ Add Category</button>
                        </div>
                    </div>

                    <div class="table-wrap">
                        <table class="data-table" id="categoryTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Category Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="categoryTbody">
                                <?php foreach ($category as $cat): ?>
                                    <?php $searchCategoryName = strtolower($cat['name']); ?>
                                    <tr data-name="<?php echo htmlspecialchars($searchCategoryName); ?>">
                                        <td class="category-id"><?php echo (int) $cat['category_id']; ?></td>
                                        <td>
                                            <span class="display-value category-display-value category-display-value-<?php echo (int) $cat['category_id']; ?>">
                                                <?php echo htmlspecialchars($cat['name']); ?>
                                            </span>
                                            <div class="input-container category-input-container-<?php echo (int) $cat['category_id']; ?>">
                                                <input type="text" name="category_name" value="<?php echo htmlspecialchars($cat['name']); ?>">
                                            </div>
                                        </td>
                                        <td class="action-cell">
                                            <div class="action-buttons">
                                                <button type="button" class="icon-btn edit" id="category-btn-edit-<?php echo (int) $cat['category_id']; ?>" onclick="onCategoryEditClick(<?php echo (int) $cat['category_id']; ?>)">
                                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                                        <path d="M3 17.25V21h3.75L17.8 9.94l-3.75-3.75L3 17.25zm18-11.5a1 1 0 0 0 0-1.41l-1.34-1.34a1 1 0 0 0-1.41 0L16.13 4.1l3.75 3.75L21 5.75z"></path>
                                                    </svg>
                                                </button>
                                                <button type="button" class="icon-btn delete" id="category-btn-delete-<?php echo (int) $cat['category_id']; ?>" onclick="onCategoryDeleteClick(<?php echo (int) $cat['category_id']; ?>)">
                                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                                        <path d="M6 7h12l-1 14H7L6 7zm3-3h6l1 2H8l1-2z"></path>
                                                    </svg>
                                                </button>
                                                <button type="button" class="icon-btn confirm" id="category-btn-confirm-<?php echo (int) $cat['category_id']; ?>" onclick="onCategoryConfirm(<?php echo (int) $cat['category_id']; ?>)">
                                                    confirm
                                                </button>
                                                <button type="button" class="icon-btn cancel" id="category-btn-cancel-<?php echo (int) $cat['category_id']; ?>" onclick="onCategoryCancel(<?php echo (int) $cat['category_id']; ?>)">
                                                    cancel
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>
        </div>
    </div>

    <script src="admin.js"></script>
</body>
</html>
