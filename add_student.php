<?php
$page_title = "Add Student";
require 'header.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

require 'config/db_config.php';

$msg = "";
$msg_type = "";
$is_edit = false;
$student_id = $_GET['id'] ?? null;

// If ID is provided, it's an edit
if ($student_id) {
    $is_edit = true;
    $page_title = "Edit Student";
    $stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $student = $stmt->get_result()->fetch_assoc();
    if (!$student) {
        header("Location: view_students.php");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    if ($name && $email) {
        if ($is_edit) {
            $stmt = $conn->prepare("UPDATE students SET name = ?, email = ? WHERE id = ?");
            $stmt->bind_param("ssi", $name, $email, $student_id);
        } else {
            // Check if email already exists
            $check = $conn->prepare("SELECT id FROM students WHERE email = ?");
            $check->bind_param("s", $email);
            $check->execute();
            if ($check->get_result()->num_rows > 0) {
                $msg = "❌ Error: Email is already registered.";
                $msg_type = "error";
            } else {
                $stmt = $conn->prepare("INSERT INTO students (name, email) VALUES (?, ?)");
                $stmt->bind_param("ss", $name, $email);
            }
        }

        if (isset($stmt)) {
            if ($stmt->execute()) {
                $msg = $is_edit ? "✅ Student updated successfully!" : "✅ Student added successfully!";
                $msg_type = "success";
                if ($is_edit) {
                    $student['name'] = $name;
                    $student['email'] = $email;
                }
            } else {
                $msg = "❌ Error: " . $stmt->error;
                $msg_type = "error";
            }
            $stmt->close();
        }
    } else {
        $msg = "⚠️ All fields are required.";
        $msg_type = "error";
    }
}
?>

<div style="max-width: 600px; margin: 0 auto;">
    <div style="margin-bottom: 2rem;">
        <a href="index.php" style="color: var(--primary); text-decoration: none; font-weight: 600; font-size: 0.875rem;">← Back to Dashboard</a>
        <h1 style="margin-top: 1rem;"><?= $is_edit ? "✏️ Edit Student" : "👥 Add New Student" ?></h1>
        <p style="color: var(--text-muted);"><?= $is_edit ? "Update information for this student." : "Register a new student into the library system." ?></p>
    </div>

    <?php if ($msg): ?>
        <div class="alert alert-<?= $msg_type ?>">
            <?= htmlspecialchars($msg) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <form method="POST" action="">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required placeholder="e.g. John Doe" value="<?= htmlspecialchars($student['name'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required placeholder="e.g. john@example.com" value="<?= htmlspecialchars($student['email'] ?? '') ?>">
            </div>

            <button type="submit" class="btn btn-primary btn-block"><?= $is_edit ? "Update Student" : "Register Student" ?></button>
        </form>
    </div>
</div>

<?php require 'footer.php'; ?>
