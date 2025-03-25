<?php
include 'functions.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Connection to database
$pdo = pdo_connect_mysql();

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT * FROM polls WHERE id = ?');
    $stmt->execute([ $_GET['id'] ]);
    // Fetch the record
    $poll = $stmt->fetch(PDO::FETCH_ASSOC);
    // Check if the poll record exists with the id specified
    if ($poll) {
        // MySQL query that selects all the poll answers
        $stmt = $pdo->prepare('SELECT * FROM poll_answers WHERE poll_id = ?');
        $stmt->execute([ $_GET['id'] ]);
        // Fetch all the poll answers
        $poll_answers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // If vote button is clicked
        if (isset($_POST['poll_answer'])) {
            // Check if user has already voted
            $stmt = $pdo->prepare('SELECT * FROM votes WHERE poll_id = ? AND user_id = ?');
            $stmt->execute([ $_GET['id'], $_SESSION['user_id'] ]);
            if ($stmt->rowCount() == 0) {
                // Insert vote record
                $stmt = $pdo->prepare('INSERT INTO votes (poll_id, user_id) VALUES (?, ?)');
                $stmt->execute([ $_GET['id'], $_SESSION['user_id'] ]);
                // Update vote count
                $stmt = $pdo->prepare('UPDATE poll_answers SET votes = votes + 1 WHERE id = ?');
                $stmt->execute([ $_POST['poll_answer'] ]);
                // Redirect user to the result page
                header('Location: result.php?id=' . $_GET['id']);
                exit;
            } else {
                $msg = 'You have already voted in this poll.';
            }
        }
    } else {
        exit('Poll with that ID does not exist.');
    }
} else {
    exit('No poll ID specified.');
}
?>

<?=template_header('Poll Vote')?>

<div class="content poll-vote">

    <h2><?=htmlspecialchars($poll['title'], ENT_QUOTES)?></h2>

    <p><?=htmlspecialchars($poll['description'], ENT_QUOTES)?></p>

    <?php if (isset($msg)): ?>
    <p><?=$msg?></p>
    <?php endif; ?>

    <form action="vote.php?id=<?=$_GET['id']?>" method="post">
        <?php for ($i = 0; $i < count($poll_answers); $i++): ?>
        <label>
            <input type="radio" name="poll_answer" value="<?=$poll_answers[$i]['id']?>"<?=$i == 0 ? ' checked' : ''?>>
            <?=htmlspecialchars($poll_answers[$i]['title'], ENT_QUOTES)?>
        </label>
        <?php endfor; ?>
        <div>
            <button type="submit">Vote</button>
            <a href="result.php?id=<?=$poll['id']?>">View Result</a>
        </div>
    </form>

</div>

<?=template_footer()?>