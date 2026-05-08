<?php
$page_title = "Library Dashboard";
require 'header.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

require 'config/db_config.php';

// Stats Queries
$totalBooks = $conn->query("SELECT COUNT(*) FROM books")->fetch_row()[0] ?? 0;
$issuedBooks = $conn->query("SELECT COUNT(*) FROM issued_books WHERE returned = 0")->fetch_row()[0] ?? 0;
$returnedBooks = $conn->query("SELECT COUNT(*) FROM issued_books WHERE returned = 1")->fetch_row()[0] ?? 0;
$totalStudents = $conn->query("SELECT COUNT(*) FROM students")->fetch_row()[0] ?? 0;

// Data for tabs
$books_list = $conn->query("SELECT * FROM books ORDER BY added_on DESC LIMIT 10");
$students_list = $conn->query("SELECT * FROM students ORDER BY id DESC LIMIT 10");

// Session messages
$msg = $_SESSION['msg'] ?? "";
$msg_type = $_SESSION['msg_type'] ?? "";
unset($_SESSION['msg'], $_SESSION['msg_type']);
?>

<!-- Hero Header -->
<div style="margin-bottom: 3rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 2rem; margin-bottom: 0.25rem;">📘 Dashboard Overview</h1>
        <p style="color: var(--text-muted); font-size: 1rem;">Welcome back, <strong><?= htmlspecialchars($_SESSION['admin']) ?></strong>! Here's what's happening.</p>
    </div>
    <div style="background: var(--bg-card); padding: 0.5rem 1rem; border-radius: 99px; border: 1px solid var(--border); font-size: 0.875rem; font-weight: 600; color: var(--text-muted);">
        📅 <?= date('l, d M Y') ?>
    </div>
</div>

<?php if ($msg): ?>
    <div class="alert alert-<?= $msg_type ?>">
        <?= htmlspecialchars($msg) ?>
    </div>
<?php endif; ?>

<!-- Stats Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
    <div class="card stat-card">
        <div class="stat-icon">📚</div>
        <div class="stat-value"><?= $totalBooks ?></div>
        <div class="stat-label">Total Books</div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon">📦</div>
        <div class="stat-value" style="color: var(--warning);"><?= $issuedBooks ?></div>
        <div class="stat-label">Currently Issued</div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon">🔁</div>
        <div class="stat-value" style="color: var(--success);"><?= $returnedBooks ?></div>
        <div class="stat-label">Returned Books</div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon">👥</div>
        <div class="stat-value"><?= $totalStudents ?></div>
        <div class="stat-label">Total Students</div>
    </div>
</div>

<!-- Quick Actions Section -->
<div style="margin-bottom: 3rem;">
    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 1.5rem;">
        <span style="font-size: 1.5rem;">⚡</span>
        <h2 style="margin: 0; font-size: 1.25rem;">Quick Actions</h2>
    </div>
    <div class="action-grid">
        <a href="add_student.php" class="action-tile">
            <span class="icon">👥</span>
            <span>Add Student</span>
        </a>
        <a href="add_book.php" class="action-tile">
            <span class="icon">➕</span>
            <span>Add Book</span>
        </a>
        <a href="issue_book.php" class="action-tile">
            <span class="icon">📦</span>
            <span>Issue Book</span>
        </a>
        <a href="return_book.php" class="action-tile">
            <span class="icon">📥</span>
            <span>Return Book</span>
        </a>
        <a href="view_issued_books.php" class="action-tile">
            <span class="icon">📋</span>
            <span>Issued Logs</span>
        </a>
    </div>
</div>

<!-- Tabs Section -->
<div class="card" style="padding: 0; overflow: hidden;">
    <div style="display: flex; border-bottom: 1px solid var(--border); background: rgba(0,0,0,0.02);">
        <button onclick="switchTab('books')" id="tab-btn-books" class="tab-btn active">📖 Books Catalog</button>
        <button onclick="switchTab('students')" id="tab-btn-students" class="tab-btn">👥 Registered Students</button>
    </div>

    <!-- Books Tab Content -->
    <div id="tab-books" class="tab-content active">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Genre</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $books_list->fetch_assoc()): ?>
                        <tr>
                            <td style="font-weight: 600;"><?= htmlspecialchars($row['title']) ?></td>
                            <td><?= htmlspecialchars($row['author']) ?></td>
                            <td><span style="background: rgba(0,0,0,0.05); padding: 4px 10px; border-radius: 6px; font-size: 0.8125rem; font-weight: 600;"><?= htmlspecialchars($row['genre']) ?></span></td>
                            <td>
                                <?php if($row['available']): ?>
                                    <span style="color: var(--success); font-size: 0.8125rem; font-weight: 700;">● Available</span>
                                <?php else: ?>
                                    <span style="color: var(--error); font-size: 0.8125rem; font-weight: 700;">● Issued</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: right;">
                                <a href="add_book.php?id=<?= $row['id'] ?>" class="btn-icon" title="Edit Book">✏️</a>
                                <a href="delete_book.php?id=<?= $row['id'] ?>" class="btn-icon" style="margin-left: 8px;" onclick="return confirm('Are you sure you want to delete this book?')" title="Delete Book">🗑️</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <div style="padding: 1.25rem; text-align: center; border-top: 1px solid var(--border); background: rgba(0,0,0,0.01);">
            <a href="view_books.php" style="font-size: 0.875rem; color: var(--primary); font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 5px;">
                View All Books <span>→</span>
            </a>
        </div>
    </div>

    <!-- Students Tab Content -->
    <div id="tab-students" class="tab-content">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email Address</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $students_list->fetch_assoc()): ?>
                        <tr>
                            <td style="color: var(--text-muted); font-weight: 600;">#<?= $row['id'] ?></td>
                            <td style="font-weight: 600;"><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td style="text-align: right;">
                                <a href="add_student.php?id=<?= $row['id'] ?>" class="btn-icon" title="Edit Student">✏️</a>
                                <a href="delete_student.php?id=<?= $row['id'] ?>" class="btn-icon" style="margin-left: 8px;" onclick="return confirm('Are you sure you want to delete this student?')" title="Delete Student">🗑️</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <div style="padding: 1.25rem; text-align: center; border-top: 1px solid var(--border); background: rgba(0,0,0,0.01);">
            <a href="view_students.php" style="font-size: 0.875rem; color: var(--primary); font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 5px;">
                View All Students <span>→</span>
            </a>
        </div>
    </div>
</div>

<style>
    .tab-btn {
        flex: 1;
        padding: 1rem;
        border: none;
        background: none;
        font-family: inherit;
        font-size: 0.9375rem;
        font-weight: 600;
        color: var(--text-muted);
        cursor: pointer;
        transition: all 0.2s;
        border-bottom: 2px solid transparent;
    }
    .tab-btn:hover {
        background: rgba(0,0,0,0.02);
        color: var(--text-main);
    }
    .tab-btn.active {
        color: var(--primary);
        border-bottom-color: var(--primary);
        background: white;
    }
    .tab-content { display: none; }
    .tab-content.active { display: block; }

    .btn-icon {
        text-decoration: none;
        font-size: 1.125rem;
        padding: 6px;
        border-radius: 8px;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .btn-icon:hover {
        background: rgba(0, 0, 0, 0.05);
        transform: scale(1.1);
    }
</style>

<script>
    function switchTab(tab) {
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        
        document.getElementById('tab-' + tab).classList.add('active');
        document.getElementById('tab-btn-' + tab).classList.add('active');
    }
</script>

<footer style="margin-top: 5rem; padding-bottom: 3rem; text-align: center; color: var(--text-muted); font-size: 0.875rem; border-top: 1px solid var(--border); padding-top: 2rem;">
    <p>&copy; <?= date('Y') ?> Library Management System. Crafted for Excellence.</p>
</footer>

<?php require 'footer.php'; ?>
