<?php
include("dbconn.php");
session_start(); // Start session to store user type

$newProjectsCount = 0;
$inProgressProjectsCount = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Secure the input data
    $username = $conn->real_escape_string($username);
    $password = $conn->real_escape_string($password);

    // Query to check if username and password exist in the applicant table
    $sql_applicant = "SELECT * FROM applicant WHERE username = '$username' AND password = '$password'";
    $result_applicant = $conn->query($sql_applicant);

    // Query to check if username and password exist in the admin table
    $sql_admin = "SELECT * FROM admin WHERE username = '$username' AND password = '$password'";
    $result_admin = $conn->query($sql_admin);

    if ($result_applicant->num_rows > 0) {
        $row = $result_applicant->fetch_assoc();
        $_SESSION['user_type'] = 'applicant'; // Set user type as applicant
        $_SESSION['user_id'] = $row['applicantID']; // Set user id from applicant table
        header("Location: appdb.php");
        exit;
    } elseif ($result_admin->num_rows > 0) {
        $row = $result_admin->fetch_assoc();
        $_SESSION['user_type'] = 'admin'; // Set user type as admin
        $_SESSION['user_id'] = $row['adminID']; // Set user id from admin table
        header("Location: dashboard.php");
        exit;
    } else {
        $loginSuccess = false;
    }
}

// Count the number of newly created projects that are still in progress
$sql_new_projects = "SELECT COUNT(*) AS new_count FROM form WHERE status = 'in progress' AND is_notified = FALSE";
$result_new_projects = $conn->query($sql_new_projects);
if ($result_new_projects->num_rows > 0) {
    $row = $result_new_projects->fetch_assoc();
    $newProjectsCount = $row['new_count'];
    // Mark newly created projects as notified
    $conn->query("UPDATE form SET is_notified = TRUE WHERE status = 'in progress' AND is_notified = FALSE");
}

// Count the total number of projects that are in progress
$sql_in_progress_projects = "SELECT COUNT(*) AS in_progress_count FROM form WHERE status = 'in progress'";
$result_in_progress_projects = $conn->query($sql_in_progress_projects);
if ($result_in_progress_projects->num_rows > 0) {
    $row = $result_in_progress_projects->fetch_assoc();
    $inProgressProjectsCount = $row['in_progress_count'];
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permit to Work</title>
    <link rel="shortcut icon" type="x-icon" href="helmet.png">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<style>
    body {
        background-color: #f4f4f9;
        font-family: 'Arial', sans-serif;
    }
    .container {
        margin-top: 30px;
    }
    .image-header {
        text-align: center;
        margin-bottom: 20px;
    }
    .image-header img {
        max-width: 100%;
        height: auto;
    }
    .card {
        width: 400px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        background-color: #fff;
    }
    .card-header {
        text-align: center;
        font-size: 28px;
        font-weight: bold;
        color: #444;
        margin-bottom: 20px;
    }
    .notification {
        margin-bottom: 20px;
        padding: 15px;
        background-color: #e3f2fd;
        border: 1px solid #90caf9;
        border-radius: 5px;
        color: #0d47a1;
        font-size: 16px;
        font-weight: bold;
        text-align: center;
    }
    .form-group label {
        font-weight: bold;
    }
    .btn {
        font-size: 16px;
        font-weight: bold;
    }
    footer {
        background-color: #333;
        color: white;
        text-align: center;
        padding: 5px 0;
        position: fixed;
        bottom: 0;
        width: 100%;
    }
    footer h3 {
        font-size: 14px;
        margin: 0;
    }
</style>
<body>
    <div class="container">
        <!-- Image Header -->
        <div class="image-header">
            <img src="kpjlg.png" width="330" height="80" alt="Header Image">
        </div>

        <!-- Status Notification -->
        <?php if ($newProjectsCount > 0 || $inProgressProjectsCount > 0): ?>
        <div class="notification">
            <?php
            echo $newProjectsCount . " new project(s) created.<br>";
            echo $inProgressProjectsCount . " project(s) are in progress.";
            ?>
        </div>
        <?php endif; ?>

        <!-- Login Card -->
        <div class="row justify-content-center">
            <div class="card">
                <div class="card-header">
                    Permit to Work
                </div>
                <div class="card-body">
                    <?php
                    if (isset($loginSuccess)) {
                        if ($loginSuccess) {
                            echo '<div class="alert alert-success" role="alert">Login successful!</div>';
                        } else {
                            echo '<div class="alert alert-danger" role="alert">Invalid username or password</div>';
                        }
                    }
                    ?>
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="username" class="form-control" id="username" name="username" placeholder="Enter username" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
<footer>
    <h3>&copy; 2025 All rights reserved || Created by IT Services</h3>
</footer>
</html>

