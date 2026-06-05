<?php
session_start();
require_once __DIR__ . "/includes/Database.php";

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {
        $message = "Please enter your username and password.";
        $messageType = "error";
    } else {
        $db = new Database();
        $con = $db->createConnection();

        $sql = "SELECT studentID, firstName, lastName, email, username, password 
                FROM students 
                WHERE username = ? 
                LIMIT 1";

        $stmt = $con->prepare($sql);

        if (!$stmt) {
            $message = "Database error preparing login.";
            $messageType = "error";
        } else {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows === 1) {
                $student = $result->fetch_assoc();

                if (password_verify($password, $student["password"])) {
                    $_SESSION["user_id"] = $student["studentID"];
                    $_SESSION["first_name"] = $student["firstName"];
                    $_SESSION["last_name"] = $student["lastName"];
                    $_SESSION["email"] = $student["email"];
                    $_SESSION["username"] = $student["username"];

                    header("Location: profile.php");
                    exit;
                } else {
                    $message = "Invalid username or password.";
                    $messageType = "error";
                }
            } else {
                $message = "Invalid username or password.";
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
    <title>Student Login</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="script.js" defer></script>
</head>

<body>

<div class="topnav">
    <a href="javascript:void(0);" class="icon" onclick="openNav()">&#9776;</a>
    <a href="index.php">Home</a>
    <a class="active" href="login.php">Login</a>
    <a href="registration.php">Register</a>
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
    <a class="active" href="login.php">Login</a>
    <a href="registration.php">Register</a>
    <a href="enrollment.php">Enrollment</a>

    <?php if (isset($_SESSION["user_id"])): ?>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    <?php endif; ?>
</div>

<main class="centered_page">
    <div class="form_wrapper">
        <h1>Student Login</h1>
        <p>Log in to access your profile and course enrollment options.</p>

        <?php if ($message !== ""): ?>
            <p class="form_message <?php echo htmlspecialchars($messageType); ?>">
                <strong><?php echo htmlspecialchars($message); ?></strong>
            </p>
        <?php endif; ?>

        <form method="POST" action="login.php" autocomplete="off">

            <div class="field">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit">Login</button>

        </form>
    </div>
</main>

</body>
</html>
