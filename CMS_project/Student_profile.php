<?php
session_start();

// Database connection function (copied from Register.php)
function db_conn() {
    $dsn = "mysql:host=localhost;dbname=senior_project";
    $conn = new PDO($dsn, 'root', '');
    return $conn;
}

$conn = db_conn();
$user_id = $_SESSION['user_id'];
$errors = [];
$current_year = (int)date("Y");

// Initializing variables so they exist on first load
$first = $last = $grad = $phone = $custom_club = $bio = "";
$current_majors = [];
$current_clubs = [];

// List of available majors
$major_list = [
    "Business Administration", "Computer Science", "Economics", "European Studies", 
    "History and Civilizations", "Information Systems", "Journalism and Mass Communication", 
    "Mathematics", "Political Science and International Relations", "Film and Creative Media", 
    "Literature", "Modern Languages and Cultures", "Physics", "Psychology", "Self-Designed Major", "Undeclared"
];

// Fetch available clubs from database
$club_query = $conn->query("SELECT club_name FROM available_clubs ORDER BY club_name ASC");
$club_list = $club_query->fetchAll(PDO::FETCH_COLUMN);

// Check if student profile exists
$check_stmt = $conn->prepare("SELECT * FROM students WHERE user_id = ?");
$check_stmt->execute([$user_id]);
$student_profile = $check_stmt->fetch(PDO::FETCH_ASSOC);

// Dummy postings for the dashboard view (Replace this with SQL query later)
$postings = [];

// Profile Submission
if (isset($_POST['complete_profile_btn'])) {
    $first = trim($_POST['first_name']);
    $last = trim($_POST['second_name']);
    $grad = (int)$_POST['graduation_year'];
    $selected_majors = $_POST['majors'] ?? []; // Array from checkboxes
    $phone = trim($_POST['phone']);
    $selected_clubs = $_POST['clubs'] ?? []; // Array from checkboxes
    $custom_club = trim($_POST['custom_club']);
    $bio = strip_tags(trim($_POST['bio']));

    // Names validation (only letters)
    if (!preg_match("/^[a-zA-Z\s]+$/", $first)) { $errors[] = "First name must contain only letters."; }
    if (!preg_match("/^[a-zA-Z\s]+$/", $last)) { $errors[] = "Last name must contain only letters."; }

    // Grad year validation: 4 digits, starts with 20, not less than current year
    if (!preg_match("/^20[0-9]{2}$/", $grad) || $grad < $current_year) {
        $errors[] = "Graduation year must be a 4-digit year starting with 20 and cannot be earlier than $current_year.";
    }

    // Majors validation (select up to 3)
    $count = count($selected_majors);
    if ($count < 1 || $count > 3) {
        $errors[] = "Please select between 1 and 3 majors.";
    }

    //Phone validation
    if (!preg_match("/^[0-9]{7,15}$/", $phone)) {
        $errors[] = "Phone number is too short or too long.";
    }

    // Custom club validation and processing
    if (!empty($custom_club)) {
        // Only upper and lower case letters and spaces allowed
        if (!preg_match("/^[a-zA-Z\s]+$/", $custom_club)) {
            $errors[] = "Custom club name must contain only letters and spaces.";
        } else {
            // Add the custom club to the selection array
            $selected_clubs[] = $custom_club;
            
            // Save this new club name to available_clubs table, so it appears for other students in the future
            $stmt_club = $conn->prepare("INSERT IGNORE INTO available_clubs (club_name) VALUES (?)");
            $stmt_club->execute([$custom_club]);
        }
    }

    // Bio Validation
    if (strlen($bio) > 1500) {
        $errors[] = "Bio is too long. Please keep it under 1500 characters.";
    }

    // Insert into database or save the joined errors to the session
    if (empty($errors)) {
        $majors_string = implode(", ", $selected_majors);
        $clubs_string = implode(", ", $selected_clubs);
        
        $ins_stmt = $conn->prepare("INSERT INTO students (user_id, first_name, second_name, phone, graduation_year, major, club, bio) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        if ($ins_stmt->execute([$user_id, $first, $last, $phone, $grad, $majors_string, $clubs_string, $bio])) {
            header("Location: Home_style.php"); //switch later to the student dashboard
            exit();
        }
    } else { 
        $_SESSION['msg'] = implode("<br>", $errors);
    }
}
include 'Student_profile_style.php';
?>