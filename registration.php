<?php
session_start();
require_once __DIR__ . "/includes/Database.php";

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $firstName = trim($_POST["firstName"] ?? "");
    $lastName  = trim($_POST["lastName"] ?? "");
    $email     = trim($_POST["email"] ?? "");
    $phone     = trim($_POST["phone"] ?? "");
    $username  = trim($_POST["username"] ?? "");
    $password  = $_POST["password"] ?? "";

    if ($firstName === "" || $lastName === "" || $email === "" || $username === "" || $password === "") {
        $message = "Please complete all required fields.";
        $messageType = "error";
    } else {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $db = new Database();
        $con = $db->createConnection();

        $sql = "INSERT INTO students (firstName, lastName, email, phone, username, password)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $con->prepare($sql);

        if (!$stmt) {
            $message = "Database error preparing registration.";
            $messageType = "error";
        } else {
            $stmt->bind_param("ssssss", $firstName, $lastName, $email, $phone, $username, $passwordHash);

            if ($stmt->execute()) {
                $message = "Registration successful! You can now log in.";
                $messageType = "success";
            } else {
                $message = "Registration failed. Email or username may already exist.";
                $messageType = "error";
            }

            $stmt->close();
        }

        $con->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="script.js" defer></script>
</head>

<body>

<div class="topnav">
    <a href="javascript:void(0);" class="icon" onclick="openNav()">&#9776;</a>
    <a href="index.php">Home</a>
    <a href="login.php">Login</a>
    <a class="active" href="registration.php">Register</a>
    <a href="enrollment.php">Enrollment</a>

    <?php if (isset($_SESSION["user_id"])): ?>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    <?php endif; ?>

    <a href="contact.php">Contact</a>
</div>

<div id="mySidenav" class="sidenav">
    <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
    <a href="index.php">Home</a>
    <a href="login.php">Login</a>
    <a class="active" href="registration.php">Register</a>
    <a href="enrollment.php">Enrollment</a>

    <?php if (isset($_SESSION["user_id"])): ?>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    <?php endif; ?>
</div>

<main class="centered_page">
    <div class="form_wrapper">
        <h1>Student Registration</h1>
        <p>Create a student account to access course enrollment features.</p>

        <?php if ($message !== ""): ?>
            <p class="form_message <?php echo htmlspecialchars($messageType); ?>">
                <strong><?php echo htmlspecialchars($message); ?></strong>
            </p>
        <?php endif; ?>

        <form method="POST" action="registration.php" autocomplete="off">

            <div class="field">
                <label for="firstName">First Name *</label>
                <input type="text" id="firstName" name="firstName" required>
            </div>

            <div class="field">
                <label for="lastName">Last Name *</label>
                <input type="text" id="lastName" name="lastName" required>
            </div>

            <div class="field">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="field">
                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone">
            </div>

            <div class="field">
                <label for="username">Username *</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="field">
                <label for="password">Password *</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit">Register</button>

        </form>
    </div>
</main>

</body>
</html>
