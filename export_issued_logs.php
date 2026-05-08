<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

require 'config/db_config.php';

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="issued_books_log.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['ID', 'Title', 'Student Name', 'Issue Date', 'Return Date', 'Status']);

$query = "
    SELECT ib.id, b.title, s.name AS student_name, ib.issue_date, ib.return_date, ib.returned
    FROM issued_books ib
    JOIN books b ON ib.book_id = b.id
    JOIN students s ON ib.student_id = s.id
    ORDER BY ib.issue_date DESC
";

$result = $conn->query($query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['id'],
            $row['title'],
            $row['student_name'],
            $row['issue_date'],
            $row['return_date'] ?? '',
            $row['returned'] ? 'Returned' : 'Issued'
        ]);
    }
}

fclose($output);
exit();
