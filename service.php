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

    public function getBooksByCategoryId(int $categoryId): array{
        $stmt = $this->db->prepare('SELECT books.*, category.name FROM books 
            INNER JOIN category ON books.category_id = category.category_id
            WHERE category.category_id = ?
            AND books.stock_quantity >= 1
            AND books.is_active = TRUE
            ORDER BY books.title ASC');
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
            ORDER BY b.title ASC');
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

    public  function getBooks(int $limit = 50, int $offset = 0): array {
        $stmt = $this->db->prepare('SELECT books.*, category.name FROM books INNER JOIN category 
            ON books.category_id = category.category_id 
            WHERE books.stock_quantity >= 1
            AND books.is_active = TRUE
            ORDER BY books.title ASC LIMIT ?, ?');

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
}
