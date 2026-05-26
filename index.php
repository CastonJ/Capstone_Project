<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css">
    <script src="script.js" defer></script>
    <title>Online Course Registration System</title>
</head>

<body>

<div class="topnav">
    <a href="javascript:void(0);" class="icon" onclick="openNav()">&#9776;</a>

    <a class="active" href="index.php">Home</a>
    <a href="login.php">Login</a>
    <a href="registration.php">Register</a>
    <a href="enrollment.php">Enrollment</a>

    <?php if (isset($_SESSION["user_id"])): ?>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    <?php endif; ?>

    <a href="#contact">Contact</a>
</div>

<div id="mySidenav" class="sidenav">
    <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
    <a class="active" href="index.php">Home</a>

    <?php if (!isset($_SESSION["user_id"])): ?>
        <a href="login.php">Login</a>
        <a href="registration.php">Register</a>
    <?php else: ?>
        <a href="profile.php">Profile</a>
        <a href="enrollment.php">Enrollment</a>
        <a href="logout.php">Logout</a>
    <?php endif; ?>

    <a href="#contact">Contact</a>
</div>

<div class="main_page">
    <h1>Welcome to the Online Course Registration System</h1>
    <p>Use this portal to register for an account, log in, view available courses, and enroll in classes.</p>
</div>

<div class="flex_side">
    <div>
        <h2>Student Enrollment</h2>
        <p>Students can create an account, log in, view available courses, and enroll in available classes through the online portal.</p>
    </div>

    <div>
        <h2>Course Availability</h2>
        <p>The system allows students to view course options by semester and supports enrollment limits and waitlist management.</p>
    </div>
</div>

<div id="contact" class="flex_side">
    <div>
        <h2>Need Help?</h2>
        <p>Contact the registration office for assistance with account access, course enrollment, or waitlist questions.</p>
    </div>
</div>

</body>
</html>