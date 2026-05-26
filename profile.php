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

/* Get student profile information */
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

if (!$student) {
    $con->close();
    session_destroy();
    header("Location: login.php");
    exit;
}

/* Get enrolled courses for the logged-in student */
$enrolledSql = "
    SELECT 
        c.courseCode,
        c.courseName,
        c.semester,
        e.enrollmentDate,
        e.status
    FROM enrollments e
    INNER JOIN courses c ON e.courseID = c.courseID
    WHERE e.studentID = ?
    ORDER BY e.enrollmentDate DESC
";

$enrolledStmt = $con->prepare($enrolledSql);
$enrolledStmt->bind_param("i", $studentID);
$enrolledStmt->execute();
$enrolledCourses = $enrolledStmt->get_result();
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

        <div class="profile_info" style="margin-top: 20px;">
            <h2>Enrolled Courses</h2>

            <?php if ($enrolledCourses && $enrolledCourses->num_rows > 0): ?>
                <?php while ($course = $enrolledCourses->fetch_assoc()): ?>
                    <p>
                        <strong><?php echo htmlspecialchars($course["courseCode"]); ?>:</strong>
                        <?php echo htmlspecialchars($course["courseName"]); ?><br>

                        <strong>Semester:</strong>
                        <?php echo htmlspecialchars($course["semester"]); ?><br>

                        <strong>Status:</strong>
                        <?php echo htmlspecialchars($course["status"]); ?><br>

                        <strong>Enrollment Date:</strong>
                        <?php echo htmlspecialchars($course["enrollmentDate"]); ?>
                    </p>
                    <hr>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No enrolled courses found.</p>
            <?php endif; ?>
        </div>

        <div class="profile_actions">
            <a class="button_primary" href="enrollment.php">Go to Enrollment</a>
            <a class="button_secondary" href="logout.php">Logout</a>
        </div>

    </div>
</main>

</body>
</html>

<?php
$enrolledStmt->close();
$con->close();
?>