<?php
// Starts the session to stay logged in
session_start();

// Database connection function
function db_conn() {
    $dsn = "mysql:host=localhost;dbname=senior_project";
    $conn = new PDO($dsn, 'root', '');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $conn;
}

$conn = db_conn();

// Logout
if (isset($_POST['logout_btn'])) {
    session_destroy();
    header("Location: Home_style.php");
    exit;
}

// Sign Up
if (isset($_POST['signup_btn'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $retype_password = $_POST['retype_password'];
    $errors = [];
    // Validation of data entry
    if(empty($email) || empty($password)){ $errors[] = "Please fill all fields!"; }
    if($password !== $retype_password){ $errors[] = "Passwords do not match!"; }
    // Check AUBG domain
    if (!preg_match("/^[a-zA-Z0-9.]+@aubg\.edu$/", $email)) {
        $errors[] = "Invalid AUBG email!";
    } else {
        // Role logic: numbers = Student, no numbers = Staff
        $role = preg_match("/[0-9]/", $email) ? "Student" : "Staff";
    }
    //If no errors, insert data into the database
    if (empty($errors)) {
        $pass_hashed = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (email, password_hash, role) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt->execute([$email, $pass_hashed, $role])) {
            $_SESSION['msg'] = "Registration successful! Please log in.";
        }
    } else {
        $_SESSION['msg'] = implode("<br>", $errors); // Store all errors as one message
    }
    header("Location: Home_style.php"); //Redirect to the landing page
    exit;
}

// Login
if (isset($_POST['login_btn'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    // Look up the user by email and fetch their data
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    // Verify if the user exists and if the submitted password matches and store user identity
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['email'] = $user['email'];
        //Redirect depending on the role
        if ($_SESSION['role'] === 'Student') {
            header("Location: Student_profile.php");
        } else {
            $_SESSION['msg'] = "Welcome back, Staff!";
            header("Location: Home_style.php");
        }
        exit;
    //If error, redirect back to landing pg
    } else { 
        $_SESSION['msg'] = "Invalid email or password.";
        header("Location: Home_style.php");
        exit;
    }
}
?>