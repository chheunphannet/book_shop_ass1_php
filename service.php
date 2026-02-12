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
            throw new \RuntimeException('Database connection failed: ' . $this->db->connect_error);
        }
    }

    public function getConnection(): \mysqli
    {
        return $this->db;
    }

    public function getCategory(int $limit = 12): array
    {
        $stmt = $this->db->prepare("SELECT * FROM Category AS c ORDER BY c.name ASC LIMIT ?");
        $stmt->bind_param("i", $limit);
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
}


