<?php
include 'functions.php';
session_start();
$msg = '';
$email = $_POST['email'] ?? '';
$security_question = '';
$show_question = false;

if (isset($_POST['submit_email'])) {
    $pdo = pdo_connect_mysql();
    $stmt = $pdo->prepare("SELECT security_question FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        $security_question = $user['security_question'];
        $show_question = true;
    } else {
        $msg = 'No account found with that email.';
    }
}

if (isset($_POST['reset'])) {
    $security_answer = $_POST['security_answer'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $pdo = pdo_connect_mysql();
    $stmt = $pdo->prepare("SELECT password, security_answer FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verify security answer and check if new password is different from the old password
    if ($user && password_verify($security_answer, $user['security_answer'])) {
        if (password_verify($new_password, $user['password'])) {
            $msg = 'New password cannot be the same as the old password.';
        } elseif (strlen($new_password) < 6 || !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $new_password) || !preg_match('/[0-9]/', $new_password)) {
            $msg = 'Password must be at least 6 characters long, with at least one special symbol and one number.';
        } else {
            $new_password_hashed = password_hash($new_password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
            $stmt->execute([$new_password_hashed, $email]);
            $msg = 'Password reset successful! Please log in with your new password.';
            header("Location: login.php");
            exit();
        }
    } else {
        $msg = 'Invalid answer to security question.';
    }
}
?>

<?=template_header('Reset Password')?>
<div class="content">
    <h2>Reset Password</h2>
    <form action="reset_password.php" method="post">
        <?php if (!$show_question): ?>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?=htmlspecialchars($email, ENT_QUOTES)?>" required><br><br>
            <input type="submit" name="submit_email" value="Next" style="background-color: #2873cf; color: white; padding: 10px 20px; border: none; border-radius: 4px;">
        <?php else: ?>
            <p>Security Question: <?=htmlspecialchars($security_question, ENT_QUOTES)?></p>
            <input type="hidden" name="email" value="<?=htmlspecialchars($email, ENT_QUOTES)?>">
            <label for="security_answer">Answer to Security Question</label>
            <input type="text" name="security_answer" id="security_answer" required><br><br>
            <label for="new_password">New Password</label>
            <input type="password" name="new_password" id="new_password" required><br><br>
            <input type="submit" name="reset" value="Reset" style="background-color: #2873cf; color: white; padding: 10px 20px; border: none; border-radius: 4px;">
        <?php endif; ?>
    </form>
    <p><?=$msg?></p>
</div>
<?=template_footer()?>
