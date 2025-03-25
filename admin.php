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

// Fetch all polls for the admin to manage
$stmt = $pdo->query('SELECT p.*, GROUP_CONCAT(pa.title ORDER BY pa.id) AS answers, SUM(pa.votes) AS total_votes, u.name AS created_by FROM polls p LEFT JOIN poll_answers pa ON pa.poll_id = p.id LEFT JOIN users u ON p.user_id = u.id GROUP BY p.id');
$polls = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch the number of registered voters
$stmt = $pdo->query('SELECT COUNT(*) AS total_voters FROM users');
$total_voters = $stmt->fetch(PDO::FETCH_ASSOC)['total_voters'];

?>

<?=template_header('Admin Dashboard')?>

<div class="content home">
    <h2>Admin Dashboard</h2>
    <div class="createpoll">
    <p>Total Registered Voters: <?=$total_voters?></p>
    </div><br><br>
    <a href="create.php" class="create-poll">Create Poll</a>
    <table>
        <thead>
            <tr>
                <td>#</td>
                <td>Title</td>
                <td>Answer Options</td>
                <td>Created By</td>
                <td>Total Votes</td>
                <td></td>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($polls as $poll): ?>
            <tr>
                <td><?=$poll['id']?></td>
                <td><?=htmlspecialchars($poll['title'], ENT_QUOTES)?></td>
                <td>
                    <?php foreach (explode(',', $poll['answers']) as $answer): ?>
                    <span class="poll-answer"><?=htmlspecialchars($answer, ENT_QUOTES)?></span>
                    <?php endforeach; ?>
                </td>
                <td><?=htmlspecialchars($poll['created_by'], ENT_QUOTES)?></td>
                <td><?=$poll['total_votes']?></td>
                <td class="actions">
                <a href="result.php?id=<?=$poll['id']?>" class="view" title="View Results"><svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9M12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17M12,4.5C7,4.5 2.73,7.61 1,12C2.73,16.39 7,19.5 12,19.5C17,19.5 21.27,16.39 23,12C21.27,7.61 17,4.5 12,4.5Z" /></svg></a>
                <a href="edit.php?id=<?=$poll['id']?>" class="edit" title="Edit Poll"><svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M2,17L1.5,18.5L1,20L2.5,20.5L4,21L5,20.5L12,12.5L11.5,12L2,17M19,3L21,4L11.5,13.5L9.5,14.5L10.5,12.5L20,3M17.5,3L16,4.5L18,5.5L19.5,4L17.5,3Z" /></svg></a>
                <a href="delete.php?id=<?=$poll['id']?>" class="trash" title="Delete Poll"><svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M9,3V4H4V6H19V4H14V3H9M5,7V20H18V7H5Z" /></svg></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?=template_footer()?>