<?php
$page_title = "Add Book";
require 'header.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

require 'config/db_config.php';

$msg = "";
$msg_type = "";
$is_edit = false;
$book_id = $_GET['id'] ?? null;

// If ID is provided, it's an edit
if ($book_id) {
    $is_edit = true;
    $page_title = "Edit Book";
    $stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $book = $stmt->get_result()->fetch_assoc();
    if (!$book) {
        header("Location: view_books.php");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $genre = trim($_POST['genre']);

    if ($title && $author && $genre) {
        if ($is_edit) {
            $stmt = $conn->prepare("UPDATE books SET title = ?, author = ?, genre = ? WHERE id = ?");
            $stmt->bind_param("sssi", $title, $author, $genre, $book_id);
        } else {
            $stmt = $conn->prepare("INSERT INTO books (title, author, genre) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $title, $author, $genre);
        }

        if ($stmt->execute()) {
            $msg = $is_edit ? "✅ Book updated successfully!" : "✅ Book added successfully!";
            $msg_type = "success";
            if ($is_edit) {
                // Refresh book data for display
                $book['title'] = $title;
                $book['author'] = $author;
                $book['genre'] = $genre;
            }
        } else {
            $msg = "❌ Error: " . $stmt->error;
            $msg_type = "error";
        }
        $stmt->close();
    } else {
        $msg = "⚠️ All fields are required.";
        $msg_type = "error";
    }
}
?>

<div style="max-width: 600px; margin: 0 auto;">
    <div style="margin-bottom: 2rem;">
        <a href="index.php" style="color: var(--primary); text-decoration: none; font-weight: 600; font-size: 0.875rem;">← Back to Dashboard</a>
        <h1 style="margin-top: 1rem;"><?= $is_edit ? "✏️ Edit Book" : "➕ Add New Book" ?></h1>
        <p style="color: var(--text-muted);"><?= $is_edit ? "Update the details for this book." : "Fill in the details below to add a book to the catalog." ?></p>
    </div>

    <?php if ($msg): ?>
        <div class="alert alert-<?= $msg_type ?>">
            <?= htmlspecialchars($msg) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <form method="POST" action="">
            <div class="form-group">
                <label for="title">Book Title</label>
                <input type="text" id="title" name="title" required placeholder="e.g. The Great Gatsby" value="<?= htmlspecialchars($book['title'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="author">Author</label>
                <input type="text" id="author" name="author" required placeholder="e.g. F. Scott Fitzgerald" value="<?= htmlspecialchars($book['author'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="genre">Genre</label>
                <input type="text" id="genre" name="genre" required placeholder="e.g. Classic" value="<?= htmlspecialchars($book['genre'] ?? '') ?>">
            </div>

            <button type="submit" class="btn btn-primary btn-block"><?= $is_edit ? "Update Book" : "Add Book to Library" ?></button>
        </form>
    </div>
</div>

<?php require 'footer.php'; ?>
