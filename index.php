<?php
include 'dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($name !== "" && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $conn->prepare(
            "INSERT INTO users (name, email) VALUES (?, ?)"
        );
        $stmt->bind_param("ss", $name, $email);

        if ($stmt->execute()) {
            header("Location: index.php?msg=added");
            exit;
        }

        $error = "Unable to add user.";
        $stmt->close();
    } else {
        $error = "Please enter a name and a valid email address.";
    }
}

$result = $conn->query(
    "SELECT * FROM users ORDER BY id DESC"
);

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$msg = $_GET['msg'] ?? '';
$messages = [
    'added'   => 'User added successfully!',
    'updated' => 'User updated successfully!',
    'deleted' => 'User deleted successfully!'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Management System</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <h2 class="text-center mb-4">User Management System</h2>

    <?php if (isset($messages[$msg])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= e($messages[$msg]) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <?= e($error) ?>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
        </div>

        <div class="card-body">
            <form method="POST" action="index.php">
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name"
                           class="form-control"
                           placeholder="Enter full name"
                           maxlength="100" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email"
                           class="form-control"
                           placeholder="Enter email address"
                           maxlength="150" required>
                </div>

                <button type="submit" class="btn btn-primary"></button>
            </form>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white"></div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped
                              table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Email Address</th>
                            <th>Date Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= e($row['id']) ?></td>
                                <td><?= e($row['name']) ?></td>
                                <td><?= e($row['email']) ?></td>
                                <td><?= e($row['created_at']) ?></td>
                                <td class="text-nowrap">
                                    <a href="edit.php?id=<?= e($row['id']) ?>"
                                       class="btn btn-warning btn-sm">
                                    </a>
                                    
                                    <a href="delete.php?id=<?= e($row['id']) ?>"
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Are you sure you want to delete this user?');">
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>