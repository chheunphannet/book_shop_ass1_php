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

    public  function getBooks(int $limit = 50, int $offset = 0): array {
        $stmt = $this->db->prepare('SELECT books.*, category.name FROM books INNER JOIN category 
            ON books.category_id = category.category_id 
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
        $stmt = $this->db->prepare('SELECT books.*, category.name FROM books INNER JOIN category ON books.category_id = category.category_id ORDER BY RAND() LIMIT ?;');
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
