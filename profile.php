<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/includes/Database.php";

$db = new Database();
$con = $db->createConnection();

$studentID = $_SESSION["user_id"];

$sql = "SELECT studentID, firstName, lastName, email, phone, username, created_at
        FROM students
        WHERE studentID = ?
        LIMIT 1";

$stmt = $con->prepare($sql);
$stmt->bind_param("i", $studentID);
$stmt->execute();

$result = $stmt->get_result();
$student = $result->fetch_assoc();

$stmt->close();
$con->close();

if (!$student) {
    session_destroy();
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="script.js" defer></script>
</head>

<body>

<div class="topnav">
    <a href="javascript:void(0);" class="icon" onclick="openNav()">&#9776;</a>
    <a href="index.php">Home</a>
    <a href="login.php">Login</a>
    <a href="registration.php">Register</a>
    <a href="enrollment.php">Enrollment</a>
    <a class="active" href="profile.php">Profile</a>
    <a href="logout.php">Logout</a>
    <a href="#contact">Contact</a>
</div>

<div id="mySidenav" class="sidenav">
    <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
    <a href="index.php">Home</a>
    <a href="login.php">Login</a>
    <a href="registration.php">Register</a>
    <a href="enrollment.php">Enrollment</a>
    <a class="active" href="profile.php">Profile</a>
    <a href="logout.php">Logout</a>
</div>

<main class="profile_page">
    <div class="form_wrapper">

        <h1>Student Profile</h1>
        <p>Welcome, <?php echo htmlspecialchars($student["firstName"]); ?>. Your account information is listed below.</p>

        <div class="profile_info">
            <p><strong>Student ID:</strong> <?php echo htmlspecialchars($student["studentID"]); ?></p>
            <p><strong>First Name:</strong> <?php echo htmlspecialchars($student["firstName"]); ?></p>
            <p><strong>Last Name:</strong> <?php echo htmlspecialchars($student["lastName"]); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($student["email"]); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($student["phone"]); ?></p>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($student["username"]); ?></p>
            <p><strong>Account Created:</strong> <?php echo htmlspecialchars($student["created_at"]); ?></p>
        </div>

        <div class="profile_actions">
            <a class="button_primary" href="enrollment.php">Go to Enrollment</a>
            <a class="button_secondary" href="logout.php">Logout</a>
        </div>

    </div>
</main>

</body>
</html>
