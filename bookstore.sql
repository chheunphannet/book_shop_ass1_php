DROP TABLE IF EXISTS SaleDetail;
DROP TABLE IF EXISTS Sales;
DROP TABLE IF EXISTS Books;
DROP TABLE IF EXISTS Category;

CREATE TABLE Category (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(25) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE Books (
    book_id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    isbn VARCHAR(25) UNIQUE,
    page_number SMALLINT UNSIGNED,
    unit_price DECIMAL(10,2) NOT NULL,
    stock_quantity INT UNSIGNED NOT NULL DEFAULT 0,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    book_cover MEDIUMBLOB,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_books_category
        FOREIGN KEY (category_id) REFERENCES Category(category_id),
    INDEX idx_books_category_id (category_id),
    INDEX idx_books_is_active_stock (is_active, stock_quantity),
    INDEX idx_books_created_at (created_at)
) ENGINE=InnoDB;

CREATE TABLE Sales (
    sale_id INT AUTO_INCREMENT PRIMARY KEY,
    sale_date DATETIME NOT NULL,
    staff_name VARCHAR(100) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_sales_sale_date (sale_date)
) ENGINE=InnoDB;

CREATE TABLE SaleDetail (
    sale_id INT NOT NULL,
    book_id INT NOT NULL,
    qty INT UNSIGNED NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (sale_id, book_id),
    CONSTRAINT fk_saledetail_sale
        FOREIGN KEY (sale_id) REFERENCES Sales(sale_id) ON DELETE CASCADE,
    CONSTRAINT fk_saledetail_book
        FOREIGN KEY (book_id) REFERENCES Books(book_id),
    INDEX idx_saledetail_book_id (book_id)
) ENGINE=InnoDB;
