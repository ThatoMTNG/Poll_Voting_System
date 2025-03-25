<?php
include 'functions.php';
session_start();
$msg = '';

if (isset($_POST['login'])) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Hardcoded admin credentials
    $hardcoded_admins = [
        ['email' => '', 'password' => ''], //use a custom email for admin access
    ];

    // Check if user is an admin
    $is_admin = false;
    foreach ($hardcoded_admins as $admin) {
        if ($admin['email'] == $email && $admin['password'] == $password) {
            $_SESSION['user_id'] = $admin['email']; // Simplified for this example
            $_SESSION['user_type'] = 'admin';
            $is_admin = true;
            break;
        }
    }

    // If not an admin, check the database for regular users
    if (!$is_admin) {
        $msg = login_user($email, $password);
    } else {
        $msg = "Login successful";
    }

    if ($msg == "Login successful") {
        if ($_SESSION['user_type'] == 'admin') {
            header("Location: admin.php");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        $msg = "Invalid login credentials. Please check your email and password, or create a new account if you don't have one.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
<?=template_header('Login')?>
<div class="content">
    <h2>Login</h2>
    <form action="login.php" method="post">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" required><br><br>
        <label for="password">Password</label>
        <input type="password" name="password" id="password" required><br><br>
        <input type="submit" name="login" value="Login" style="background-color: #2873cf; color: white; padding: 10px 20px; border: none; border-radius: 4px;">
    </form>
    <p><?=$msg?></p>
    <p>Don't have an account? <a href="register.php">Register</a></p>
    <p>Forgot Password? <a href="reset_password.php">Reset</a></p>
</div>
<?=template_footer()?>
</body>
</html>
