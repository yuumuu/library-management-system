<?php
$page_title = "View Books";
require 'header.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

require 'config/db_config.php';

$result = $conn->query("SELECT * FROM books ORDER BY added_on DESC");
if (!$result) {
    die("❌ SQL Error: " . $conn->error);
}
?>

<div style="margin-bottom: 2rem;">
    <a href="index.php" style="color: var(--primary); text-decoration: none; font-weight: 600; font-size: 0.875rem;">← Back to Dashboard</a>
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-top: 1rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1>📖 Library Books</h1>
            <p style="color: var(--text-muted);">View and manage all books in the catalog.</p>
        </div>
        <div style="flex: 1; max-width: 400px;">
            <input type="text" id="searchInput" placeholder="🔍 Search title, author, or genre..." style="background-color: var(--bg-card);">
        </div>
    </div>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <?php if ($result->num_rows > 0): ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Genre</th>
                        <th>Status</th>
                        <th>Added On</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td style="font-weight: 600;"><?= htmlspecialchars($row['title']) ?></td>
                            <td><?= htmlspecialchars($row['author']) ?></td>
                            <td><span style="background: rgba(0,0,0,0.05); padding: 2px 8px; border-radius: 4px; font-size: 0.875rem;"><?= htmlspecialchars($row['genre']) ?></span></td>
                            <td>
                                <?php if($row['available']): ?>
                                    <span style="color: var(--success); font-size: 0.875rem; font-weight: 600;">● Available</span>
                                <?php else: ?>
                                    <span style="color: var(--error); font-size: 0.875rem; font-weight: 600;">● Issued</span>
                                <?php endif; ?>
                            </td>
                            <td style="color: var(--text-muted); font-size: 0.875rem;"><?= date('M d, Y', strtotime($row['added_on'])) ?></td>
                            <td style="text-align: right;">
                                <a href="add_book.php?id=<?= $row['id'] ?>" style="text-decoration: none; font-size: 1rem; padding: 4px 8px; border-radius: 4px;" title="Edit Book">✏️</a>
                                <a href="delete_book.php?id=<?= $row['id'] ?>" style="text-decoration: none; font-size: 1rem; padding: 4px 8px; border-radius: 4px;" onclick="return confirm('Are you sure you want to delete this book?')" title="Delete Book">🗑️</a>
                            </td>
                        </tr>
<?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div style="padding: 4rem; text-align: center; color: var(--text-muted);">
            <div style="font-size: 3rem; margin-bottom: 1rem;">📭</div>
            <p>No books added to the library yet.</p>
            <a href="add_book.php" class="btn btn-primary" style="margin-top: 1.5rem;">Add Your First Book</a>
        </div>
    <?php endif; ?>
</div>

<script>
    const searchInput = document.getElementById("searchInput");
    searchInput.addEventListener("keyup", function () {
        const filter = searchInput.value.toLowerCase();
        const rows = document.querySelectorAll("table tbody tr");

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(filter) ? "" : "none";
        });
    });
</script>

<?php require 'footer.php'; ?>
