<?php
// Connection function
function pdo_connect_mysql() {
    $DATABASE_HOST = 'localhost';
    $DATABASE_USER = 'root';
    $DATABASE_PASS = '';
    $DATABASE_NAME = 'phppoll';
    try {
        return new PDO('mysql:host=' . $DATABASE_HOST . ';dbname=' . $DATABASE_NAME . ';charset=utf8', $DATABASE_USER, $DATABASE_PASS);
    } catch (PDOException $exception) {
        exit('Failed to connect to database!');
    }
}

// Function to register a new user
function register_user($name, $email, $password, $security_question, $security_answer, $user_type) {
    $pdo = pdo_connect_mysql();
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Invalid email format";
    }
    if (strlen($password) < 6 || !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password) || !preg_match('/[0-9]/', $password)) {
        return "Password must be at least 6 characters long, with at least one special symbol and one number.";
    }
    $password_hashed = password_hash($password, PASSWORD_BCRYPT);
    $security_answer_hashed = password_hash($security_answer, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, security_question, security_answer, user_type) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $email, $password_hashed, $security_question, $security_answer_hashed, $user_type]);
    return "Registration successful";
}

// Function to login a user
function login_user($email, $password) {
    $pdo = pdo_connect_mysql();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_type'] = $user['user_type'];
        return "Login successful";
    } else {
        return "Invalid email or password";
    }
}

// Function to reset password
function reset_password($email, $security_answer, $new_password) {
    $pdo = pdo_connect_mysql();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($security_answer, $user['security_answer'])) {
        $new_password_hashed = password_hash($new_password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->execute([$new_password_hashed, $email]);
        return "Password reset successful";
    } else {
        return "Invalid email or answer to security question";
    }
}

// Template header function
function template_header($title) {
    echo '<!DOCTYPE html>
    <html>
        <head>
            <meta charset="utf-8">
            <title>' . $title . '</title>
            <link href="style.css" rel="stylesheet" type="text/css">
        </head>
        <body>
        <nav class="navtop">
            <div>
                <h1>Poll & Voting System</h1>';
    if (isset($_SESSION['user_id'])) {
        echo '<a href="index.php">Polls</a>';
        if ($_SESSION['user_type'] == 'admin') {
            echo '<a href="admin.php">Admin Dashboard</a>';
        }
        echo '<a href="logout.php">Logout</a>';
    }
    echo '</div>
        </nav>';
}

// Template footer function
function template_footer() {
    echo '</body>
    </html>';
}
?>