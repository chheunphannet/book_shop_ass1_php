<?php
require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

class DatabaseService
{
    private \mysqli $db;
    public function __construct(?string $envDir = null)
    {
        
        $envDir = $envDir ?? __DIR__;

        if (is_file($envDir . '/.env')) {
            $dotenv = Dotenv::createImmutable($envDir);
            $dotenv->safeLoad();
        }

        $dbHost = $_ENV['DB_HOST'] ?? '127.0.0.1';
        $dbPort = (int)($_ENV['DB_PORT'] ?? 3306);
        $dbName = $_ENV['DB_DATABASE'] ?? '';
        $dbUser = $_ENV['DB_USERNAME'] ?? '';
        $dbPass = $_ENV['DB_PASSWORD'] ?? '';

        $this->db = new \mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);

        if ($this->db->connect_error) {
            die('Database connection failed: ' . $this->db->connect_error);
        }
    }

private function getInventoryStatus(int $stock): string
{
    if ($stock === 0) {
        return 'Empty';
    }

    if ($stock <= 10) {
        return 'Low';
    }

    return 'In Stock';
}


    public function getConnection(): \mysqli
    {
        return $this->db;
    }

    public function getCurrentPrice($book_id){
        $stmt= $this->db->prepare('SELECT unit_price FROM books WHERE book_id = ? AND is_active = TRUE');
        $stmt->bind_param('i', $book_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return (float)$row['unit_price'];
    }

    public function updateStock($book_id, $qty){
        $stmt = $this->db->prepare('UPDATE books SET stock_quantity = stock_quantity - ? WHERE book_id = ?');
        $stmt ->bind_param('ii', $qty, $book_id);
        $stmt -> execute();
    }

    public function softDeleteBook(int $bookId): bool
    {
        $stmt = $this->db->prepare('UPDATE books SET is_active = FALSE WHERE book_id = ? AND is_active = TRUE');
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param('i', $bookId);
        $ok = $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();

        return $ok && $affected > 0;
    }

    public function createBookByCategoryName(
        string $title,
        string $categoryName,
        int $pageNumber,
        float $unitPrice,
        int $stockQty,
        ?string $bookCoverBinary = null
    ): ?int {
        $catStmt = $this->db->prepare('SELECT category_id FROM category WHERE name = ? LIMIT 1');
        if (!$catStmt) {
            return null;
        }

        $catStmt->bind_param('s', $categoryName);
        $catStmt->execute();
        $catResult = $catStmt->get_result();
        $catRow = $catResult ? $catResult->fetch_assoc() : null;
        $catStmt->close();

        if (!$catRow) {
            return null;
        }

        $categoryId = (int)$catRow['category_id'];

        if ($bookCoverBinary === null) {
            $placeholderPath = __DIR__ . '/placeholder-image-vertical.png';
            $bookCoverBinary = is_file($placeholderPath) ? file_get_contents($placeholderPath) : '';
            if ($bookCoverBinary === false) {
                $bookCoverBinary = '';
            }
        }

        $stmt = $this->db->prepare('INSERT INTO books (category_id, title, page_number, unit_price, stock_quantity, book_cover, is_active)
            VALUES (?, ?, ?, ?, ?, ?, TRUE)');

        if (!$stmt) {
            $stmt = $this->db->prepare('INSERT INTO books (category_id, title, page_number, unit_price, stock_quantity, book_cover)
                VALUES (?, ?, ?, ?, ?, ?)');
            if (!$stmt) {
                return null;
            }
        }

        $blob = null;
        $stmt->bind_param('isidib', $categoryId, $title, $pageNumber, $unitPrice, $stockQty, $blob);
        $stmt->send_long_data(5, $bookCoverBinary);

        $ok = $stmt->execute();
        $insertId = $ok ? (int)$stmt->insert_id : 0;
        $stmt->close();

        return $insertId > 0 ? $insertId : null;
    }

    public function updateBookByCategoryName(
        int $bookId,
        string $title,
        string $categoryName,
        int $pageNumber,
        float $unitPrice,
        int $stockQty,
        ?string $bookCoverBinary = null
    ): bool {
        $catStmt = $this->db->prepare('SELECT category_id FROM category WHERE name = ? LIMIT 1');
        if (!$catStmt) {
            return false;
        }

        $catStmt->bind_param('s', $categoryName);
        $catStmt->execute();
        $catResult = $catStmt->get_result();
        $catRow = $catResult ? $catResult->fetch_assoc() : null;
        $catStmt->close();

        if (!$catRow) {
            return false;
        }

        $categoryId = (int)$catRow['category_id'];

        if ($bookCoverBinary !== null) {
            $stmt = $this->db->prepare('UPDATE books
                SET category_id = ?, title = ?, page_number = ?, unit_price = ?, stock_quantity = ?, book_cover = ?
                WHERE book_id = ?');
            if (!$stmt) {
                return false;
            }

            $blob = null;
            $stmt->bind_param('isidibi', $categoryId, $title, $pageNumber, $unitPrice, $stockQty, $blob, $bookId);
            $stmt->send_long_data(5, $bookCoverBinary);
        } else {
            $stmt = $this->db->prepare('UPDATE books
                SET category_id = ?, title = ?, page_number = ?, unit_price = ?, stock_quantity = ?
                WHERE book_id = ?');
            if (!$stmt) {
                return false;
            }

            $stmt->bind_param('isidii', $categoryId, $title, $pageNumber, $unitPrice, $stockQty, $bookId);
        }

        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function insetSaleDetail($book_id, $sale_id, $qty, $price, $amount): void{
        $detail_stmt = $this -> db ->prepare("INSERT INTO SaleDetail (book_id, sale_id, qty, unit_price, amount) VALUES (?, ?, ?, ?, ?)");
        $detail_stmt->bind_param("iiidd", $book_id, $sale_id, $qty, $price, $amount);
        $detail_stmt->execute();
    }

    public function insertSale($sale_date, $staff_name, $total_amount): int{
        $stmt = $this -> db ->prepare("INSERT INTO Sales (sale_date, staff_name, total_amount) VALUES (?, ?, ?)");
        $stmt->bind_param("ssd", $sale_date, $staff_name, $total_amount);
        $stmt->execute();
        $sale_id = $stmt->insert_id ?? 0;
        return $sale_id;
    }

    public function getLatestOrders(int $limit = 5): array
    {
        if ($limit <= 0) {
            $limit = 5;
        }

        $stmt = $this->db->prepare('SELECT sale_date, staff_name, total_amount
            FROM Sales
            ORDER BY sale_date DESC, sale_id DESC
            LIMIT ?');

        if (!$stmt) {
            return [];
        }

        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result === false) {
            $stmt->close();
            return [];
        }

        $orders = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $orders;
    }

    public function getAllOrders(): array
    {
        $stmt = $this->db->prepare('SELECT sale_id, sale_date, staff_name, total_amount
            FROM Sales
            ORDER BY sale_date DESC, sale_id DESC');

        if (!$stmt) {
            return [];
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result === false) {
            $stmt->close();
            return [];
        }

        $orders = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $orders;
    }

    public function getOrderItemsBySaleIds(array $saleIds): array
    {
        $saleIds = array_values(array_unique(array_map('intval', $saleIds)));
        $saleIds = array_values(array_filter($saleIds, static fn($id) => $id > 0));

        if (empty($saleIds)) {
            return [];
        }

        $idsSql = implode(',', $saleIds);
        $stmt = $this->db->prepare("SELECT sd.sale_id, b.title, sd.qty
            FROM SaleDetail AS sd
            INNER JOIN books AS b ON b.book_id = sd.book_id
            WHERE sd.sale_id IN ($idsSql)
            ORDER BY sd.sale_id DESC, b.title ASC");

        if (!$stmt) {
            return [];
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result === false) {
            $stmt->close();
            return [];
        }

        $itemsBySaleId = [];

        while ($row = $result->fetch_assoc()) {
            $saleId = (int) ($row['sale_id'] ?? 0);
            if (!isset($itemsBySaleId[$saleId])) {
                $itemsBySaleId[$saleId] = [];
            }

            $itemsBySaleId[$saleId][] = [
                'title' => (string) ($row['title'] ?? ''),
                'qty' => (int) ($row['qty'] ?? 0),
            ];
        }

        $stmt->close();
        return $itemsBySaleId;
    }

    public function getMostPopularBooks(int $limit = 5): array
    {
        if ($limit <= 0) {
            $limit = 5;
        }

        $stmt = $this->db->prepare('SELECT b.title, COALESCE(SUM(sd.qty), 0) AS sold_qty
            FROM books AS b
            LEFT JOIN SaleDetail AS sd ON sd.book_id = b.book_id
            WHERE b.is_active = TRUE
            GROUP BY b.book_id, b.title, b.created_at
            ORDER BY sold_qty DESC, b.created_at DESC, b.book_id DESC
            LIMIT ?');

        if (!$stmt) {
            return [];
        }

        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result === false) {
            $stmt->close();
            return [];
        }

        $books = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $books;
    }

    public function getDashboardStats(): array
    {
        $stats = [
            'revenue' => 0.0,
            'total_sales' => 0,
            'low_stock' => 0,
            'books_available' => 0,
        ];

        $salesStmt = $this->db->prepare('SELECT COALESCE(SUM(total_amount), 0) AS revenue, COUNT(*) AS total_sales FROM Sales');
        if ($salesStmt) {
            $salesStmt->execute();
            $salesResult = $salesStmt->get_result();
            $salesRow = $salesResult ? $salesResult->fetch_assoc() : null;
            if ($salesRow) {
                $stats['revenue'] = (float) ($salesRow['revenue'] ?? 0);
                $stats['total_sales'] = (int) ($salesRow['total_sales'] ?? 0);
            }
            $salesStmt->close();
        }

        $lowStockStmt = $this->db->prepare('SELECT COUNT(*) AS low_stock
            FROM books
            WHERE is_active = TRUE AND stock_quantity > 0 AND stock_quantity <= 10');
        if ($lowStockStmt) {
            $lowStockStmt->execute();
            $lowStockResult = $lowStockStmt->get_result();
            $lowStockRow = $lowStockResult ? $lowStockResult->fetch_assoc() : null;
            if ($lowStockRow) {
                $stats['low_stock'] = (int) ($lowStockRow['low_stock'] ?? 0);
            }
            $lowStockStmt->close();
        }

        $availableStmt = $this->db->prepare('SELECT COUNT(*) AS books_available
            FROM books
            WHERE is_active = TRUE AND stock_quantity > 0');
        if ($availableStmt) {
            $availableStmt->execute();
            $availableResult = $availableStmt->get_result();
            $availableRow = $availableResult ? $availableResult->fetch_assoc() : null;
            if ($availableRow) {
                $stats['books_available'] = (int) ($availableRow['books_available'] ?? 0);
            }
            $availableStmt->close();
        }

        return $stats;
    }

    public function getBooksByCategoryId(int $categoryId): array{
        $stmt = $this->db->prepare('SELECT books.*, category.name FROM books 
            INNER JOIN category ON books.category_id = category.category_id
            WHERE category.category_id = ?
            AND books.stock_quantity >= 1
            AND books.is_active = TRUE
            ORDER BY books.created_at DESC, books.book_id DESC');
        $stmt->bind_param('i', $categoryId);
        $stmt->execute();
        $result = $stmt->get_result();
        $books = [];
        while ($row = $result->fetch_assoc()) {
            if(!empty($row['book_cover'])) {
                $row['book_cover_base64'] = 'data:image/jpeg;base64,' . base64_encode($row['book_cover']);
            } else {
               $row['book_cover_base64'] = './placeholder-image-vertical.png'; 
            }
            $books[] = $row;
        }
        return $books;
    }

    public function searchBooksByTitle(string $title): array{
        $search = "%" . $title . "%";
        $stmt = $this->db->prepare('SELECT b.*, c.name FROM books AS b
            INNER JOIN category AS c ON b.category_id = c.category_id
            WHERE b.title LIKE ?
            AND b.stock_quantity >= 1
            AND b.is_active = TRUE
            ORDER BY b.created_at DESC, b.book_id DESC');
        $stmt->bind_param('s', $search);
        $stmt->execute();
        $result = $stmt->get_result();

        $books = [];
        while ($row = $result->fetch_assoc()) {
            if(!empty($row['book_cover'])) {
                $row['book_cover_base64'] = 'data:image/jpeg;base64,' . base64_encode($row['book_cover']);
            } else {
               $row['book_cover_base64'] = './placeholder-image-vertical.png'; 
            }
            $books[] = $row;
        }
        return $books;
    }

     public  function getInventory(int $limit = 50, int $offset = 0): array {
        $stmt = $this->db->prepare('SELECT books.*, category.name FROM books INNER JOIN category 
            ON books.category_id = category.category_id 
            WHERE books.is_active = TRUE
            ORDER BY books.created_at DESC, books.book_id DESC LIMIT ?, ?');

        $stmt->bind_param('ii', $offset,$limit);
        $stmt->execute();
        $result = $stmt->get_result();

        $books = [];

        while ($row = $result->fetch_assoc()) {
            $row['status'] = $this -> getInventoryStatus($row['stock_quantity']);

            if (!empty($row['book_cover'])) {
                $row['book_cover_base64'] = 'data:image/jpeg;base64,' . base64_encode($row['book_cover']);
            } else {
                $row['book_cover_base64'] = '../placeholder-image-vertical.png';
            }
            $books[] = $row;
        }
      return $books;
    }

    public  function getBooks(int $limit = 50, int $offset = 0): array {
        $stmt = $this->db->prepare('SELECT books.*, category.name FROM books INNER JOIN category 
            ON books.category_id = category.category_id 
            WHERE books.stock_quantity >= 1
            AND books.is_active = TRUE
            ORDER BY books.created_at DESC, books.book_id DESC LIMIT ?, ?');

        $stmt->bind_param('ii', $offset,$limit);
        $stmt->execute();
        $result = $stmt->get_result();

        $books = [];

        while ($row = $result->fetch_assoc()) {
            if (!empty($row['book_cover'])) {
                $row['book_cover_base64'] = 'data:image/jpeg;base64,' . base64_encode($row['book_cover']);
            } else {
                $row['book_cover_base64'] = './placeholder-image-vertical.png';
            }
            $books[] = $row;
        }
      return $books;
    }

    public function getRandomBook(int $limit = 1) {
        $stmt = $this->db->prepare('SELECT books.*, category.name FROM books INNER JOIN category ON books.category_id = category.category_id WHERE books.stock_quantity >= 1 AND books.is_active = TRUE ORDER BY RAND() LIMIT ?;');
        $stmt->bind_param("i",$limit);
        $stmt->execute();
        $result = $stmt->get_result();

        $books = [];
        while ($row = $result->fetch_assoc()) {
            if (!empty($row['book_cover'])) {
                $row['book_cover_base64'] = 'data:image/jpeg;base64,' . base64_encode($row['book_cover']);
            } else {
                $row['book_cover_base64'] = './placeholder-image-vertical.png';
            }
            $books[] = $row;
        }

        return $books;
    }

    public function getBooksInIds(array $bookIds): array
    {
        $bookIds = array_values(array_unique(array_map('intval', $bookIds)));
        $bookIds = array_values(array_filter($bookIds, static fn($id) => $id > 0));

        if (empty($bookIds)) {
            return [];
        }

        $idsSql = implode(',', $bookIds);
        $stmt = $this->db->prepare("SELECT books.*, category.name FROM books
            INNER JOIN category ON books.category_id = category.category_id
            WHERE books.book_id IN ($idsSql)
            AND books.stock_quantity >= 1
            AND books.is_active = TRUE
            ORDER BY books.title ASC");
        $stmt->execute();
        $result = $stmt->get_result();

        $books = [];

        while ($row = $result->fetch_assoc()) {
            if (!empty($row['book_cover'])) {
                $row['book_cover_base64'] = 'data:image/jpeg;base64,' . base64_encode($row['book_cover']);
            } else {
                $row['book_cover_base64'] = './placeholder-image-vertical.png';
            }
            $books[] = $row;
        }
      return $books;
    }

    public function getCategory(int $limit = 12, int $offset = 0): array
    {
        $stmt = $this->db->prepare("SELECT * FROM Category AS c ORDER BY c.name ASC LIMIT ?, ?");
        $stmt->bind_param("ii", $offset, $limit);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result === false) {
            $stmt->close();
            return [];
        }

        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

        public function getAllCategory(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM Category AS c ORDER BY c.name ASC");
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result === false) {
            $stmt->close();
            return [];
        }

        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    public function getLengthCategory(){
        $stmt = $this->db->prepare("SELECT COUNT(*) AS row_count FROM Category");
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result === false) {
            $stmt->close();
            return 0;
        }
        $row = $result->fetch_assoc();
        $stmt->close();
        return (int)($row["row_count"] ?? 0);
    }

    public function createCategory(string $name): ?int
    {
        $stmt = $this->db->prepare('INSERT INTO Category (name) VALUES (?)');
        if (!$stmt) {
            return null;
        }

        $stmt->bind_param('s', $name);
        $ok = $stmt->execute();
        $insertId = $ok ? (int) $stmt->insert_id : 0;
        $stmt->close();

        return $insertId > 0 ? $insertId : null;
    }

    public function updateCategoryName(int $categoryId, string $name): bool
    {
        $existsStmt = $this->db->prepare('SELECT category_id FROM Category WHERE category_id = ? LIMIT 1');
        if (!$existsStmt) {
            return false;
        }

        $existsStmt->bind_param('i', $categoryId);
        $existsStmt->execute();
        $existsResult = $existsStmt->get_result();
        $exists = $existsResult ? $existsResult->fetch_assoc() : null;
        $existsStmt->close();

        if (!$exists) {
            return false;
        }

        $stmt = $this->db->prepare('UPDATE Category SET name = ? WHERE category_id = ?');
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param('si', $name, $categoryId);
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }

    public function countBooksInCategory(int $categoryId): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) AS total FROM Books WHERE category_id = ?');
        if (!$stmt) {
            return 0;
        }

        $stmt->bind_param('i', $categoryId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result ? $result->fetch_assoc() : null;
        $stmt->close();

        return (int) ($row['total'] ?? 0);
    }

    public function deleteCategory(int $categoryId): bool
    {
        $stmt = $this->db->prepare('DELETE FROM Category WHERE category_id = ?');
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param('i', $categoryId);
        $ok = $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();

        return $ok && $affected > 0;
    }
}
