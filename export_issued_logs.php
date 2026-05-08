<?php
// Start output buffering to prevent any accidental output from corrupting the CSV
ob_start();

session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

require 'config/db_config.php';

// Clear the buffer to ensure only the CSV is sent
ob_end_clean();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="library_issued_logs_' . date('Y-m-d') . '.csv"');

$output = fopen('php://output', 'w');

// Add UTF-8 BOM for Excel compatibility
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// CSV Headers
fputcsv($output, ['Log ID', 'Book Title', 'Student Name', 'Issue Date', 'Return Date', 'Status']);

// Fetch logs with JOINS
$query = "
    SELECT 
        ib.id, 
        b.title, 
        s.name AS student_name, 
        ib.issue_date, 
        ib.return_date, 
        ib.returned
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
            $row['return_date'] ? $row['return_date'] : 'Not Returned',
            $row['returned'] ? 'Returned' : 'Issued'
        ]);
    }
}

fclose($output);
exit();
