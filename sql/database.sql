CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(100),
    genre VARCHAR(50),
    available BOOLEAN DEFAULT TRUE,
    added_on DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE issued_books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT,
    student_id INT,
    returned BOOLEAN DEFAULT FALSE,
    issue_date DATE,
    return_date DATE,
    FOREIGN KEY (book_id) REFERENCES books(id),
    FOREIGN KEY (student_id) REFERENCES students(id)
);

-- Optional sample books
INSERT INTO books (title, author, genre) VALUES
('The Great Gatsby', 'F. Scott Fitzgerald', 'Classic'),
('The Alchemist', 'Paulo Coelho', 'Fiction'),
('Clean Code', 'Robert C. Martin', 'Programming');
