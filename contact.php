
<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Information</title>
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

    <?php if (isset($_SESSION["user_id"])): ?>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    <?php endif; ?>

    <a class="active" href="contact.php">Contact</a>
</div>

<div id="mySidenav" class="sidenav">
    <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>

    <a href="index.php">Home</a>
    <a href="login.php">Login</a>
    <a href="registration.php">Register</a>
    <a href="enrollment.php">Enrollment</a>

    <?php if (isset($_SESSION["user_id"])): ?>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    <?php endif; ?>

    <a class="active" href="contact.php">Contact</a>
</div>

<main class="profile_page">

    <div class="form_wrapper">

        <h1>Contact Information</h1>

        <p>
            If you need assistance with course registration, enrollment issues,
            account access, or general questions, please contact one of the
            departments below.
        </p>

        <div class="profile_info">

            <h2>Registrar Office</h2>
            <p><strong>Contact:</strong> Sarah Johnson</p>
            <p><strong>Phone:</strong> (555) 123-4567</p>
            <p><strong>Email:</strong> registrar@university.edu</p>
            <button onclick="showEmailMessage('registrar@university.edu')">E-Mail</button>

            <hr>

            <h2>Student Services</h2>
            <p><strong>Contact:</strong> Michael Carter</p>
            <p><strong>Phone:</strong> (555) 234-5678</p>
            <p><strong>Email:</strong> studentservices@university.edu</p>
            <button onclick="showEmailMessage('studentservices@university.edu')">E-Mail</button>

            <hr>

            <h2>Technical Support</h2>
            <p><strong>Contact:</strong> Jennifer Smith</p>
            <p><strong>Phone:</strong> (555) 345-6789</p>
            <p><strong>Email:</strong> support@university.edu</p>
            <button onclick="showEmailMessage('support@university.edu')">E-Mail</button>

            <hr>

            <h2>Admissions Office</h2>
            <p><strong>Contact:</strong> Robert Wilson</p>
            <p><strong>Phone:</strong> (555) 456-7890</p>
            <p><strong>Email:</strong> admissions@university.edu</p>
            <button onclick="showEmailMessage('admissions@university.edu')">E-Mail</button>

        </div>


    </div>

</main>

<script>
function showEmailMessage() {
    alert(
        "Demo Feature\n\n" +
        "This Online Course Registration System was developed for educational purposes.\n" +
        "Email functionality has not been implemented and the contact information displayed is fictional."
    );
}
</script>


</body>
</html>

