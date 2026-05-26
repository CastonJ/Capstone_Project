<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/includes/Database.php";

$message = "";
$messageType = "";

$db = new Database();
$con = $db->createConnection();

$studentID = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $courseID = intval($_POST["courseID"] ?? 0);

    if ($courseID <= 0) {
        $message = "Please select a course.";
        $messageType = "error";
    } else {
        $checkCourse = $con->prepare(
            "SELECT courseID, courseCode, courseName, maxEnrollment, currentEnrollment 
             FROM courses 
             WHERE courseID = ?"
        );
        $checkCourse->bind_param("i", $courseID);
        $checkCourse->execute();
        $course = $checkCourse->get_result()->fetch_assoc();
        $checkCourse->close();

        if (!$course) {
            $message = "The selected course could not be found.";
            $messageType = "error";
        } else {
            $checkExisting = $con->prepare(
                "SELECT enrollmentID 
                 FROM enrollments 
                 WHERE studentID = ? AND courseID = ?"
            );
            $checkExisting->bind_param("ii", $studentID, $courseID);
            $checkExisting->execute();
            $existing = $checkExisting->get_result();
            $checkExisting->close();

            if ($existing->num_rows > 0) {
                $message = "You are already enrolled in this course.";
                $messageType = "error";
            } elseif ($course["currentEnrollment"] < $course["maxEnrollment"]) {
                $insertEnroll = $con->prepare(
                    "INSERT INTO enrollments (studentID, courseID, status) 
                     VALUES (?, ?, 'Enrolled')"
                );
                $insertEnroll->bind_param("ii", $studentID, $courseID);

                if ($insertEnroll->execute()) {
                    $updateCourse = $con->prepare(
                        "UPDATE courses 
                         SET currentEnrollment = currentEnrollment + 1 
                         WHERE courseID = ?"
                    );
                    $updateCourse->bind_param("i", $courseID);
                    $updateCourse->execute();
                    $updateCourse->close();

                    $message = "You have successfully enrolled in " . htmlspecialchars($course["courseCode"]) . " - " . htmlspecialchars($course["courseName"]) . ".";
                    $messageType = "success";
                } else {
                    $message = "Enrollment could not be completed.";
                    $messageType = "error";
                }

                $insertEnroll->close();
            } else {
                $positionQuery = $con->prepare(
                    "SELECT COUNT(*) + 1 AS waitlistPosition 
                     FROM waitlist 
                     WHERE courseID = ?"
                );
                $positionQuery->bind_param("i", $courseID);
                $positionQuery->execute();
                $position = $positionQuery->get_result()->fetch_assoc()["waitlistPosition"];
                $positionQuery->close();

                $insertWaitlist = $con->prepare(
                    "INSERT INTO waitlist (studentID, courseID, waitlistPosition) 
                     VALUES (?, ?, ?)"
                );
                $insertWaitlist->bind_param("iii", $studentID, $courseID, $position);

                if ($insertWaitlist->execute()) {
                    $message = "This course is full. You have been added to the waitlist.";
                    $messageType = "success";
                } else {
                    $message = "Could not add you to the waitlist.";
                    $messageType = "error";
                }

                $insertWaitlist->close();
            }
        }
    }
}

$courses = $con->query(
    "SELECT courseID, courseCode, courseName, semester, maxEnrollment, currentEnrollment 
     FROM courses 
     ORDER BY semester, courseCode"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Enrollment</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="script.js" defer></script>
</head>

<body>

<div class="topnav">
    <a href="javascript:void(0);" class="icon" onclick="openNav()">&#9776;</a>
    <a href="index.php">Home</a>
    <a href="login.php">Login</a>
    <a href="registration.php">Register</a>
    <a class="active" href="enrollment.php">Enrollment</a>
    <a href="profile.php">Profile</a>
    <a href="logout.php">Logout</a>
    <a href="#contact">Contact</a>
</div>

<div id="mySidenav" class="sidenav">
    <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
    <a href="index.php">Home</a>
    <a href="login.php">Login</a>
    <a href="registration.php">Register</a>
    <a class="active" href="enrollment.php">Enrollment</a>
    <a href="profile.php">Profile</a>
    <a href="logout.php">Logout</a>
</div>

<main class="centered_page">
    <div class="form_wrapper">
        <h1>Course Enrollment</h1>
        <p>Select a course below. If a course is full, you will be added to the waitlist.</p>

        <?php if ($message !== ""): ?>
            <p class="form_message <?php echo htmlspecialchars($messageType); ?>">
                <strong><?php echo $message; ?></strong>
            </p>
        <?php endif; ?>

        <form method="POST" action="enrollment.php">
            <div class="field">
                <label for="courseID">Available Courses</label>
                <select id="courseID" name="courseID" required>
                    <option value="">Select a course</option>

                    <?php while ($row = $courses->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($row["courseID"]); ?>">
                            <?php
                            echo htmlspecialchars(
                                $row["courseCode"] . " - " .
                                $row["courseName"] . " | " .
                                $row["semester"] . " | Seats: " .
                                $row["currentEnrollment"] . "/" .
                                $row["maxEnrollment"]
                            );
                            ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <button type="submit">Enroll</button>
        </form>
    </div>
</main>

</body>
</html>

<?php
$con->close();
?>