<?php
include 'functions.php';
session_start();
$msg = '';
if (isset($_POST['register'])) {
    if ($_POST['user_type'] == 'admin') {
        $msg = 'Admin registration is not allowed.';
    } else {
        $msg = register_user($_POST['name'], $_POST['email'], $_POST['password'], $_POST['security_question'], $_POST['security_answer'], $_POST['user_type']);
        if ($msg == "Registration successful") {
            header("Location: login.php");
            exit();
        }
    }
}
?>

<?=template_header('Register')?>

<div class="content">
    <h2>Register</h2>
    <form action="register.php" method="post">
        <label for="name">Name</label>
        <input type="text" name="name" id="name" required><br><br>
        <label for="email">Email</label>
        <input type="email" name="email" id="email" required><br><br>
        <label for="password">Password</label>
        <input type="password" name="password" id="password" required><br><br>
        <label for="security_question">Security Question</label>
        <input type="text" name="security_question" id="security_question" required><br><br>
        <label for="security_answer">Answer</label>
        <input type="text" name="security_answer" id="security_answer" required><br><br>
        <label for="user_type">User Type</label>
        <select name="user_type" id="user_type">
            <option value="voter">Voter</option>
        </select>
        <input type="submit" name="register" value="Register" style="background-color: #2873cf; color: white; padding: 10px 20px; border: none; border-radius: 4px;">
    </form>
    <p><?=$msg?></p>
</div>

<?=template_footer()?>