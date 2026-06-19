<?php
$page_title = "Return Book";
require 'header.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

require 'config/db_config.php';

$msg = "";
$msg_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $issue_id = $_POST['issue_id'];

    // Get book_id before updating
    $stmt_book = $conn->prepare("SELECT book_id FROM issued_books WHERE id = ?");
    $stmt_book->bind_param("i", $issue_id);
    $stmt_book->execute();
    $book_data = $stmt_book->get_result()->fetch_assoc();
    $book_id = $book_data['book_id'] ?? null;

    if ($book_id) {
        $stmt = $conn->prepare("UPDATE issued_books SET returned = 1, return_date = CURDATE() WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $issue_id);
            if ($stmt->execute()) {
                // Update book availability
                $conn->query("UPDATE books SET available = TRUE WHERE id = $book_id");
                $msg = "✅ Book returned successfully!";
                $msg_type = "success";
            } else {
                $msg = "❌ Error: " . $stmt->error;
                $msg_type = "error";
            }
            $stmt->close();
        }
    } else {
        $msg = "❌ Error finding issued record.";
        $msg_type = "error";
    }
}

// Fetch unreturned books
$issued = $conn->query("
    SELECT issued_books.id, books.title, students.name AS student_name
    FROM issued_books 
    JOIN books ON issued_books.book_id = books.id 
    JOIN students ON issued_books.student_id = students.id
    WHERE issued_books.returned = 0
");
?>

<div style="max-width: 600px; margin: 0 auto;">
    <div style="margin-bottom: 2rem;">
        <a href="index.php" style="color: var(--primary); text-decoration: none; font-weight: 600; font-size: 0.875rem;">← Back to Dashboard</a>
        <h1 style="margin-top: 1rem;">📥 Return Book</h1>
        <p style="color: var(--text-muted);">Process a returned book to update inventory.</p>
    </div>

    <?php if ($msg): ?>
        <div class="alert alert-<?= $msg_type ?>">
            <?= htmlspecialchars($msg) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <form method="POST" action="">
            <div class="form-group">
                <label for="issue_id">Select Issued Book</label>
                <select name="issue_id" id="issue_id" required>
                    <option value="">-- Choose a Record --</option>
                    <?php while ($row = $issued->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>">
                            <?= htmlspecialchars($row['title']) ?> (borrowed by <?= htmlspecialchars($row['student_name']) ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Complete Return</button>
        </form>
    </div>
    
    <div style="margin-top: 2rem; text-align: center;">
        <a href="view_issued_books.php" style="color: var(--text-muted); font-size: 0.875rem; text-decoration: none;">View all issued logs →</a>
    </div>
</div>

<?php require 'footer.php'; ?>
