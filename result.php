<?php
include 'functions.php';
include 'access_control.php';

// Connect to MySQL
$pdo = pdo_connect_mysql();

// If the GET request "id" exists (poll id)...
if (isset($_GET['id'])) {
    // MySQL query that selects the poll records by the GET request "id"
    $stmt = $pdo->prepare('SELECT * FROM polls WHERE id = ?');
    $stmt->execute([htmlspecialchars($_GET['id'], ENT_QUOTES)]);
    // Fetch the record
    $poll = $stmt->fetch(PDO::FETCH_ASSOC);
    // Check if the poll record exists with the id specified
    if ($poll) {
        // MySQL Query that will get all the answers from the "poll_answers" table
        $stmt = $pdo->prepare('SELECT * FROM poll_answers WHERE poll_id = ? ORDER BY votes DESC');
        $stmt->execute([htmlspecialchars($_GET['id'], ENT_QUOTES)]);
        // Fetch all poll answers
        $poll_answers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Total number of votes, will be used to calculate the percentage
        $total_votes = 0;
        foreach ($poll_answers as $poll_answer) {
            // Every poll answers votes will be added to total votes
            $total_votes += $poll_answer['votes'];
        }
    } else {
        exit('Poll doesn\'t exist or you don\'t have permission to view its results!');
    }
} else {
    exit('No poll ID specified.');
}

// Function to download results as CSV
function download_csv($poll, $poll_answers) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="poll_results.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, array('Poll Title', $poll['title']));
    fputcsv($output, array('Description', $poll['description']));
    fputcsv($output, array('Answer', 'Votes'));
    foreach ($poll_answers as $answer) {
        fputcsv($output, array($answer['title'], $answer['votes']));
    }
    fclose($output);
    exit;
}

// Handle CSV download request
if (isset($_GET['download']) && $_SESSION['user_type'] == 'admin') {
    download_csv($poll, $poll_answers);
}
?>

<?=template_header('Poll Results')?>

<div class="content poll-result">
    <h2><?=htmlspecialchars($poll['title'], ENT_QUOTES)?></h2>
    <p><?=htmlspecialchars($poll['description'], ENT_QUOTES)?></p>

    <div class="wrapper">
        <?php foreach ($poll_answers as $poll_answer): ?>
        <div class="poll-question">
            <p><?=htmlspecialchars($poll_answer['title'], ENT_QUOTES)?> <span>(<?=$poll_answer['votes']?> Votes)</span></p>
            <div class="result-bar-wrapper">
                <div class="result-bar" style="width:<?=($poll_answer['votes'] / $total_votes) * 100?>%;">
                    <?=$poll_answer['votes'] ? round(($poll_answer['votes'] / $total_votes) * 100) : 0?>%
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if ($_SESSION['user_type'] == 'admin'): ?>
        <a href="result.php?id=<?=htmlspecialchars($_GET['id'], ENT_QUOTES)?>&download=true" class="btn">Download Results as CSV</a>
    <?php endif; ?>

</div>

<?=template_footer()?>