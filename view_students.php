<?php
$page_title = "View Students";
require 'header.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

require 'config/db_config.php';

$result = $conn->query("SELECT * FROM students ORDER BY id DESC");
if (!$result) {
    die("❌ SQL Error: " . $conn->error);
}

// Session messages for delete
$msg = $_SESSION['msg'] ?? "";
$msg_type = $_SESSION['msg_type'] ?? "";
unset($_SESSION['msg'], $_SESSION['msg_type']);
?>

<div style="margin-bottom: 2rem;">
    <a href="index.php" style="color: var(--primary); text-decoration: none; font-weight: 600; font-size: 0.875rem;">← Back to Dashboard</a>
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-top: 1rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1>👥 Students Directory</h1>
            <p style="color: var(--text-muted);">Manage all registered students.</p>
        </div>
        <div style="flex: 1; max-width: 400px;">
            <input type="text" id="searchInput" placeholder="🔍 Search name or email..." style="background-color: var(--bg-card);">
        </div>
    </div>
</div>

<?php if ($msg): ?>
    <div class="alert alert-<?= $msg_type ?>">
        <?= htmlspecialchars($msg) ?>
    </div>
<?php endif; ?>

<div class="card" style="padding: 0; overflow: hidden;">
    <?php if ($result->num_rows > 0): ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody id="studentTable">
                    <?php while ($row = $result->fetch_assoc()): ?>
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
    <?php else: ?>
        <div style="padding: 3rem; text-align: center; color: var(--text-muted);">
            <p>No students found. Register one to get started.</p>
            <a href="add_student.php" class="btn btn-primary" style="margin-top: 1rem;">Add Student</a>
        </div>
    <?php endif; ?>
</div>

<style>
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
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll('#studentTable tr');

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });
</script>

<?php require 'footer.php'; ?>
