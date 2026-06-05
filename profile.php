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
$message = "";
$messageType = "";

/* Drop class logic */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["dropEnrollmentID"], $_POST["dropCourseID"])) {
    $dropEnrollmentID = intval($_POST["dropEnrollmentID"]);
    $dropCourseID = intval($_POST["dropCourseID"]);

    $deleteSql = "DELETE FROM enrollments 
                  WHERE enrollmentID = ? AND studentID = ?";

    $deleteStmt = $con->prepare($deleteSql);
    $deleteStmt->bind_param("ii", $dropEnrollmentID, $studentID);

    if ($deleteStmt->execute()) {
        $updateSql = "UPDATE courses 
                      SET currentEnrollment = GREATEST(currentEnrollment - 1, 0) 
                      WHERE courseID = ?";

        $updateStmt = $con->prepare($updateSql);
        $updateStmt->bind_param("i", $dropCourseID);
        $updateStmt->execute();
        $updateStmt->close();

        $message = "Class successfully dropped.";
        $messageType = "success";
    } else {
        $message = "Unable to drop class.";
        $messageType = "error";
    }

    $deleteStmt->close();
}

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
        e.enrollmentID,
        c.courseID,
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
    <a href="contact.php">Contact</a>
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
        <p>Welcome, <?php echo htmlspecialchars($student["firstName"]); ?>. Your account information and class schedule are listed below.</p>

        <?php if ($message !== ""): ?>
            <p class="form_message <?php echo htmlspecialchars($messageType); ?>">
                <strong><?php echo htmlspecialchars($message); ?></strong>
            </p>
        <?php endif; ?>

        <div class="profile_info">
            <h2>Account Information</h2>
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
                    <div class="course_item">
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

                        <form method="POST" action="profile.php" style="margin-top: 10px;">
                            <input type="hidden" name="dropEnrollmentID" value="<?php echo htmlspecialchars($course["enrollmentID"]); ?>">
                            <input type="hidden" name="dropCourseID" value="<?php echo htmlspecialchars($course["courseID"]); ?>">
                            <button type="submit" onclick="return confirm('Are you sure you want to drop this class?');">
                                Drop Class
                            </button>
                        </form>
                    </div>
                    <hr>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No enrolled courses found.</p>
            <?php endif; ?>
        </div>

        <div class="profile_actions">
            <a class="button_primary" href="enrollment.php">Add More Courses</a>
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