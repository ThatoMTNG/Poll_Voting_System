<?php
include 'functions.php';
include 'access_control.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Connect to MySQL
$pdo = pdo_connect_mysql();
$msg = '';

// Check if POST data is not empty
if (!empty($_POST)) {
    $title = isset($_POST['title']) ? htmlspecialchars($_POST['title'], ENT_QUOTES) : '';
    $desc = isset($_POST['desc']) ? htmlspecialchars($_POST['desc'], ENT_QUOTES) : '';
    $user_id = $_SESSION['user_id'];

    // Insert new record into the "polls" table
    $stmt = $pdo->prepare('INSERT INTO polls (title, description, user_id) VALUES (?, ?, ?)');
    $stmt->execute([$title, $desc, $user_id]);

    // Get the last insert ID, this will be the poll id
    $poll_id = $pdo->lastInsertId();

    // Get the answers and convert the multiline string to an array
    $answers = isset($_POST['answers']) ? explode(PHP_EOL, $_POST['answers']) : [];
    foreach ($answers as $answer) {
        if (empty($answer)) continue;
        $stmt = $pdo->prepare('INSERT INTO poll_answers (poll_id, title, votes) VALUES (?, ?, 0)');
        $stmt->execute([$poll_id, htmlspecialchars($answer, ENT_QUOTES)]);
    }
    $msg = 'Created Successfully!';
}
?>

<?=template_header('Create Poll')?>

<div class="content update">
    <h2>Create Poll</h2>
    <form action="create.php" method="post">
        <label for="title">Title</label>
        <input type="text" name="title" id="title" placeholder="Title" required>

        <label for="desc">Description</label>
        <input type="text" name="desc" id="desc" placeholder="Description" required>

        <label for="answers">Answer Options (per line)</label>
        <textarea name="answers" id="answers" placeholder="Option 1<?=PHP_EOL?>Option 2<?=PHP_EOL?>Option 3" required></textarea>

        <button type="submit">Create</button>
    </form>
    <?php if ($msg): ?>
    <p><?=$msg?></p>
    <?php endif; ?>
</div>

<?=template_footer()?>