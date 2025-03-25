<?php
include 'functions.php';
include 'access_control.php';

// Ensure the user is an admin
if ($_SESSION['user_type'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Connect to MySQL
$pdo = pdo_connect_mysql();

// Fetch current polls and reassign IDs
$stmt = $pdo->query('SELECT id FROM polls ORDER BY id');
$polls = $stmt->fetchAll(PDO::FETCH_ASSOC);

$new_id = 1;
foreach ($polls as $poll) {
    $old_id = $poll['id'];

    // Update poll ID
    $stmt = $pdo->prepare('UPDATE polls SET id = ? WHERE id = ?');
    $stmt->execute([$new_id, $old_id]);

    // Update related poll answers
    $stmt = $pdo->prepare('UPDATE poll_answers SET poll_id = ? WHERE poll_id = ?');
    $stmt->execute([$new_id, $old_id]);

    // If you have any other related tables, update them similarly
    // $stmt = $pdo->prepare('UPDATE votes SET poll_id = ? WHERE poll_id = ?');
    // $stmt->execute([$new_id, $old_id]);

    $new_id++;
}

// Reset the auto-increment value
$stmt = $pdo->query('ALTER TABLE polls AUTO_INCREMENT = ' . ($new_id));

// Output message
echo 'Polls renumbered successfully!';
?>
