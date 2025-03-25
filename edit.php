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
        exit('Poll doesn\'t exist or you don\'t have permission to edit it!');
    }

    // Handle form submission
    if (!empty($_POST)) {
        // Update poll record
        $title = isset($_POST['title']) ? htmlspecialchars($_POST['title'], ENT_QUOTES) : '';
        $desc = isset($_POST['desc']) ? htmlspecialchars($_POST['desc'], ENT_QUOTES) : '';
        $stmt = $pdo->prepare('UPDATE polls SET title = ?, description = ? WHERE id = ?');
        $stmt->execute([$title, $desc, htmlspecialchars($_GET['id'], ENT_QUOTES)]);

        // Delete existing answers
        $stmt = $pdo->prepare('DELETE FROM poll_answers WHERE poll_id = ?');
        $stmt->execute([htmlspecialchars($_GET['id'], ENT_QUOTES)]);

        // Insert new answers
        $answers = isset($_POST['answers']) ? explode(PHP_EOL, $_POST['answers']) : [];
        foreach ($answers as $answer) {
            if (empty($answer)) continue;
            $stmt = $pdo->prepare('INSERT INTO poll_answers (poll_id, title, votes) VALUES (?, ?, 0)');
            $stmt->execute([htmlspecialchars($_GET['id'], ENT_QUOTES), htmlspecialchars($answer, ENT_QUOTES)]);
        }

        $msg = 'Updated Successfully!';
    }

    // Fetch the updated poll data
    $stmt = $pdo->prepare('SELECT p.*, GROUP_CONCAT(pa.title ORDER BY pa.id) AS answers FROM polls p LEFT JOIN poll_answers pa ON pa.poll_id = p.id WHERE p.id = ? GROUP BY p.id');
    $stmt->execute([htmlspecialchars($_GET['id'], ENT_QUOTES)]);
    $poll = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    exit('No ID specified!');
}
?>

<?=template_header('Edit Poll')?>

<div class="content update">
    <h2>Edit Poll</h2>
    <form action="edit.php?id=<?=$poll['id']?>" method="post">
        <label for="title">Title</label>
        <input type="text" name="title" id="title" value="<?=htmlspecialchars($poll['title'], ENT_QUOTES)?>" required>
        <label for="desc">Description</label>
        <input type="text" name="desc" id="desc" value="<?=htmlspecialchars($poll['description'], ENT_QUOTES)?>" required>
        <label for="answers">Answer Options (per line)</label>
        <textarea name="answers" id="answers" required><?=htmlspecialchars(str_replace(',', PHP_EOL, $poll['answers']), ENT_QUOTES)?></textarea>
        <button type="submit">Update</button>
    </form>
    <?php if ($msg): ?>
    <p><?=$msg?></p>
    <?php endif; ?>
</div>

<?=template_footer()?>