<?php
include 'dbcon.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: index.php");
    exit;
}

$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    $stmt->close();
    header("Location: index.php?msg=deleted");
    exit;
}

$stmt->close();
$conn->close();

header("Location: index.php");
exit;
?>