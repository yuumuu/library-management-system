<?php
$page_title = "Issue Book";
require 'header.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

require 'config/db_config.php';

$msg = "";
$msg_type = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $book_id = $_POST['book_id'] ?? null;
    $student_id = $_POST['student_id'] ?? null;

    if ($book_id && $student_id) {
        $stmt = $conn->prepare("INSERT INTO issued_books (book_id, student_id, issue_date) VALUES (?, ?, CURDATE())");
        if ($stmt) {
            $stmt->bind_param("ii", $book_id, $student_id);

            if ($stmt->execute()) {
                // Mark book as unavailable
                $conn->query("UPDATE books SET available = FALSE WHERE id = $book_id");
                $msg = "✅ Book issued successfully!";
                $msg_type = "success";
            } else {
                $msg = "❌ Error: " . $stmt->error;
                $msg_type = "error";
            }
            $stmt->close();
        }
    } else {
        $msg = "⚠️ Please select a book and a valid student.";
        $msg_type = "error";
    }
}

// Fetch available books
$books = $conn->query("SELECT id, title FROM books WHERE available = TRUE");

// Fetch students for search (all of them, then we filter in JS for smoother UX if small, or we could do AJAX, but for native/praktikum, small JS list is fine)
$students_data = $conn->query("SELECT id, name, email FROM students");
$students = [];
while ($row = $students_data->fetch_assoc()) {
    $students[] = $row;
}
?>

<div style="max-width: 600px; margin: 0 auto;">
    <div style="margin-bottom: 2rem;">
        <a href="index.php" style="color: var(--primary); text-decoration: none; font-weight: 600; font-size: 0.875rem;">← Back to Dashboard</a>
        <h1 style="margin-top: 1rem;">📚 Issue Book</h1>
        <p style="color: var(--text-muted);">Assign a book to a registered student.</p>
    </div>

    <?php if ($msg): ?>
        <div class="alert alert-<?= $msg_type ?>">
            <?= htmlspecialchars($msg) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <form method="POST" action="" id="issueForm">
            <div class="form-group">
                <label for="book_id">Select Book</label>
                <select name="book_id" id="book_id" required>
                    <option value="">-- Choose a Book --</option>
                    <?php while ($row = $books->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['title']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group" style="position: relative;">
                <label for="student_search">Student Name or Email</label>
                <div style="position: relative;">
                    <input type="text" id="student_search" placeholder="Type to search..." autocomplete="off">
                    <input type="hidden" name="student_id" id="student_id_hidden" required>
                    
                    <!-- Custom Dropdown -->
                    <div id="search_results" class="custom-dropdown">
                        <div id="results_list"></div>
                        <a href="add_student.php" class="add-student-shortcut">
                            <span>➕</span>
                            <div>
                                <strong>Register New Student</strong>
                                <small>If student is not in the list</small>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <button type="submit" id="submitBtn" class="btn btn-primary btn-block" disabled>Issue Book Now</button>
        </form>

        <div style="margin-top: 1.5rem; display: flex; justify-content: center; gap: 20px; font-size: 0.875rem;">
            <a href="index.php" style="color: var(--text-muted); text-decoration: none;">← Back to Dashboard</a>
            <a href="view_issued_books.php" style="color: var(--primary); text-decoration: none; font-weight: 600;">📋 View All Issued Logs</a>
        </div>
    </div>
</div>

<style>
    .custom-dropdown {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 0.75rem;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
        z-index: 1000;
        margin-top: 5px;
        overflow: hidden;
    }

    .search-item {
        padding: 0.75rem 1rem;
        cursor: pointer;
        transition: background 0.2s;
        border-bottom: 1px solid rgba(0,0,0,0.02);
    }

    .search-item:hover {
        background: rgba(79, 70, 229, 0.05);
    }

    .search-item strong {
        display: block;
        font-size: 0.9375rem;
        color: var(--text-main);
    }

    .search-item small {
        display: block;
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    .add-student-shortcut {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 0.75rem 1rem;
        background: rgba(79, 70, 229, 0.03);
        text-decoration: none;
        color: var(--primary);
        border-top: 1px solid var(--border);
        transition: background 0.2s;
    }

    .add-student-shortcut:hover {
        background: rgba(79, 70, 229, 0.08);
    }

    .add-student-shortcut strong {
        display: block;
        font-size: 0.875rem;
    }

    .add-student-shortcut small {
        font-size: 0.75rem;
        opacity: 0.8;
    }
</style>

<script>
    const students = <?= json_encode($students) ?>;
    const searchInput = document.getElementById('student_search');
    const resultsDiv = document.getElementById('search_results');
    const listDiv = document.getElementById('results_list');
    const idHidden = document.getElementById('student_id_hidden');
    const submitBtn = document.getElementById('submitBtn');

    searchInput.addEventListener('focus', () => {
        if (searchInput.value.length > 0) resultsDiv.style.display = 'block';
    });

    searchInput.addEventListener('input', function() {
        const val = this.value.toLowerCase();
        listDiv.innerHTML = '';
        
        if (val.length === 0) {
            resultsDiv.style.display = 'none';
            idHidden.value = '';
            submitBtn.disabled = true;
            return;
        }

        resultsDiv.style.display = 'block';
        
        const filtered = students.filter(s => 
            s.name.toLowerCase().includes(val) || 
            s.email.toLowerCase().includes(val)
        ).slice(0, 5);

        if (filtered.length > 0) {
            filtered.forEach(s => {
                const div = document.createElement('div');
                div.className = 'search-item';
                div.innerHTML = `<strong>${s.name}</strong><small>${s.email}</small>`;
                div.onclick = () => {
                    searchInput.value = s.name + ' (' + s.email + ')';
                    idHidden.value = s.id;
                    resultsDiv.style.display = 'none';
                    submitBtn.disabled = false;
                };
                listDiv.appendChild(div);
            });
        } else {
            const div = document.createElement('div');
            div.style.padding = '1rem';
            div.style.textAlign = 'center';
            div.style.fontSize = '0.875rem';
            div.style.color = 'var(--text-muted)';
            div.innerText = 'No students found';
            listDiv.appendChild(div);
        }
        
        // Reset selection if user changes text
        if (idHidden.value) {
            idHidden.value = '';
            submitBtn.disabled = true;
        }
    });

    // Close dropdown on click outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !resultsDiv.contains(e.target)) {
            resultsDiv.style.display = 'none';
        }
    });
</script>

<?php require 'footer.php'; ?>
