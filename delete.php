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

// Initialize message variable
$msg = '';

// Check that the poll ID exists
if (isset($_GET['id'])) {
    // Fetch the poll data
    $stmt = $pdo->prepare('SELECT * FROM polls WHERE id = ?');
    $stmt->execute([htmlspecialchars($_GET['id'], ENT_QUOTES)]);
    $poll = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Check if the poll exists
    if (!$poll) {
        exit('Poll doesn\'t exist or you don\'t have permission to delete it!');
    }

    // Make sure the user confirms before deletion
    if (isset($_GET['confirm'])) {
        if ($_GET['confirm'] == 'yes') {
            // Delete the poll and related answers
            $stmt = $pdo->prepare('DELETE p, pa FROM polls p LEFT JOIN poll_answers pa ON pa.poll_id = p.id WHERE p.id = ?');
            $stmt->execute([htmlspecialchars($_GET['id'], ENT_QUOTES)]);
            $msg = 'You have deleted the poll!';
        } else {
            header('Location: admin.php');
            exit;
        }
    }
} else {
    exit('No ID specified!');
}
?>

<?=template_header('Delete')?>

<div class="content delete">
    <h2>Delete Poll #<?=$poll['id']?></h2>
    <?php if ($msg): ?>
    <p><?=$msg?></p>
    <?php else: ?>
    <p>Are you sure you want to delete poll #<?=$poll['id']?>?</p>
    <div class="yesno">
        <a href="delete.php?id=<?=$poll['id']?>&confirm=yes">Yes</a>
        <a href="delete.php?id=<?=$poll['id']?>&confirm=no">No</a>
    </div>
    <?php endif; ?>
</div>

<?=template_footer()?>
