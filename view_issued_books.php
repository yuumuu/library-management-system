<?php
$page_title = "Issued Books Log";
require 'header.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

require 'config/db_config.php';

$result = $conn->query("
    SELECT ib.id, b.title, s.name AS student_name, ib.issue_date, ib.return_date, ib.returned
    FROM issued_books ib
    JOIN students s ON ib.student_id = s.id
    JOIN books b ON ib.book_id = b.id
    ORDER BY ib.issue_date DESC
");

if (!$result) {
    die("❌ SQL Error: " . $conn->error);
}
?>

<div style="margin-bottom: 2rem;">
    <a href="index.php" style="color: var(--primary); text-decoration: none; font-weight: 600; font-size: 0.875rem;">← Back to Dashboard</a>
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-top: 1rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1>📋 Issued Books Log</h1>
            <p style="color: var(--text-muted);">History of all book borrowing activities.</p>
        </div>
        <div style="display: flex; gap: 10px; flex: 1; max-width: 500px;">
            <input type="text" id="searchInput" placeholder="🔍 Search records..." style="flex: 1; background-color: var(--bg-card);">
            <a href="export_issued_logs.php" class="btn btn-primary" style="white-space: nowrap; font-size: 0.875rem;">Download CSV</a>
        </div>
    </div>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <?php if ($result->num_rows > 0): ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Book Title</th>
                        <th>Student</th>
                        <th>Issue Date</th>
                        <th>Return Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td style="color: var(--text-muted);"><?= $i++ ?></td>
                            <td style="font-weight: 600;"><?= htmlspecialchars($row['title']) ?></td>
                            <td><?= htmlspecialchars($row['student_name']) ?></td>
                            <td><?= date('M d, Y', strtotime($row['issue_date'])) ?></td>
                            <td><?= $row['return_date'] ? date('M d, Y', strtotime($row['return_date'])) : '—' ?></td>
                            <td>
                                <?php if($row['returned']): ?>
                                    <span style="background: rgba(16, 185, 129, 0.1); color: var(--success); padding: 4px 10px; border-radius: 99px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Returned</span>
                                <?php else: ?>
                                    <span style="background: rgba(245, 158, 11, 0.1); color: var(--warning); padding: 4px 10px; border-radius: 99px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Issued</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div style="padding: 4rem; text-align: center; color: var(--text-muted);">
            <div style="font-size: 3rem; margin-bottom: 1rem;">📋</div>
            <p>No borrowing history found.</p>
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
