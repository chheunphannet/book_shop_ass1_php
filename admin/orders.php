<?php
require_once __DIR__ . "/../service.php";

$service = new DatabaseService();
$ordersRaw = $service->getAllOrders();
$saleIds = array_map(static fn(array $order): int => (int) ($order['sale_id'] ?? 0), $ordersRaw);
$itemsBySaleId = $service->getOrderItemsBySaleIds($saleIds);

$orders = array_map(static function (array $order) use ($itemsBySaleId): array {
    $saleId = (int) ($order['sale_id'] ?? 0);
    $timestamp = strtotime((string) ($order['sale_date'] ?? ''));
    $saleDate = $timestamp ? date('Y-m-d H:i', $timestamp) : (string) ($order['sale_date'] ?? '');

    return [
        'sale_id' => $saleId,
        'date' => $saleDate,
        'person' => (string) ($order['staff_name'] ?? ''),
        'total' => '$' . number_format((float) ($order['total_amount'] ?? 0), 2),
        'status' => 'Completed',
        'items' => $itemsBySaleId[$saleId] ?? [],
    ];
}, $ordersRaw);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="admin-layout">
        <aside class="sidebar">
            <div class="brand">BookShop Admin</div>
            <nav class="sidebar-nav">
                <a href="admin.php" class="nav-link">Dashboard Home</a>
                <a href="admin.php" class="nav-link">Inventory</a>
                <a href="#" class="nav-link">Categories</a>
                <a href="orders.php" class="nav-link active">Order History</a>
            </nav>
        </aside>

        <div class="content-area">
            <header class="topbar">
                <h1>Order History</h1>
                <div class="topbar-right">
                    <button type="button" class="logout-btn"><a href="/book_shop_ass1_php/home.php">Logout</a></button>
                </div>
            </header>

            <main class="main-content">
                <section class="panel">
                    <div class="panel-header">
                        <h3>All Orders</h3>
                        <a href="admin.php" class="panel-link">Back to dashboard</a>
                    </div>
                    <div class="table-wrap">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Date</th>
                                    <th>Customer / Staff</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($orders)): ?>
                                    <?php foreach ($orders as $order): ?>
                                        <?php $orderStatusClass = str_replace(' ', '-', strtolower($order['status'])); ?>
                                        <tr>
                                            <td><?php echo (int) $order['sale_id']; ?></td>
                                            <td><?php echo htmlspecialchars($order['date']); ?></td>
                                            <td><?php echo htmlspecialchars($order['person']); ?></td>
                                            <td class="order-items-cell">
                                                <?php if (!empty($order['items'])): ?>
                                                    <details class="order-items-dropdown">
                                                        <summary>View items (<?php echo count($order['items']); ?>)</summary>
                                                        <ul class="order-items-list">
                                                            <?php foreach ($order['items'] as $item): ?>
                                                                <li>
                                                                    <span class="order-item-title"><?php echo htmlspecialchars($item['title']); ?></span>
                                                                    <span class="order-item-qty">x<?php echo (int) $item['qty']; ?></span>
                                                                </li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    </details>
                                                <?php else: ?>
                                                    <span class="order-item-empty">No items</span>
                                                <?php endif; ?>
                                            </td>
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
                                        <td colspan="6">No orders yet.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>
        </div>
    </div>
</body>
</html>
