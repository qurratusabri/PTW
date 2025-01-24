<?php
require 'dbconn.php';
session_start();

// Fetch services
$services = $conn->query("SELECT serviceName FROM services");

// Add new service if form is submitted
if (isset($_POST['addServiceButton']) && !empty($_POST['newService'])) {
    $newService = mysqli_real_escape_string($conn, $_POST['newService']);

    // Check if the service already exists
    $checkService = "SELECT * FROM services WHERE serviceName = '$newService'";
    $result = mysqli_query($conn, $checkService);

    if (mysqli_num_rows($result) == 0) {
        // Insert new service into the database
        $insertService = "INSERT INTO services (serviceName) VALUES ('$newService')";
        if (!mysqli_query($conn, $insertService)) {
            $_SESSION['message'] = "Error: " . mysqli_error($conn);
        } else {
            $_SESSION['message'] = "New service added successfully!";
        }
    } else {
        $_SESSION['message'] = "Service already exists!";
    }

    // Redirect to the same page to avoid resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
// Delete selected service
if (isset($_POST['deleteServiceButton']) && !empty($_POST['serviceToDelete'])) {
    $serviceToDelete = mysqli_real_escape_string($conn, $_POST['serviceToDelete']);

    // Delete service from the database
    $deleteQuery = "DELETE FROM services WHERE serviceName = '$serviceToDelete'";
    if (mysqli_query($conn, $deleteQuery)) {
        $_SESSION['message'] = "Service deleted successfully!";
    } else {
        $_SESSION['message'] = "Error deleting service: " . mysqli_error($conn);
    }

    // Redirect back to services page
    header("Location: services.php");
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icon -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!-- CSS -->
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" type="x-icon" href="helmet.png">

    <title>Add/Remove Services</title>
</head>
<body>

<div class="sidebar" id="sidebar">
    <div class="top">
        <div class="logo">
            <i class="bx bx-hard-hat"></i>
            <span>PermitToWork</span>
        </div>
        <i class="bx bx-menu" id="btn"></i>
    </div>
    <ul>
        <li>
            <a href="dashboard.php">
                <i class="bx bxs-grid-alt"></i>
                <span class="nav-item">Dashboard</span>
            </a>
        </li>
        <li>
            <a href="form.php">
                <i class="bx bx-file-blank"></i>
                <span class="nav-item">Form</span>
            </a>
        </li>
        <li>
            <a href="services.php">
                <i class="bx bx-add-to-queue"></i>
                <span class="nav-item">Services</span>
            </a>
        </li>
        <li>
            <a href="logout.php" onclick="return confirmLogout();">
                <i class="bx bx-log-out"></i>
                <span class="nav-item">Logout</span>
            </a>
        </li>
    </ul>
</div>

<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Are you sure you want to logout?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmLogoutBtn">Logout</button>
      </div>
    </div>
  </div>
</div>

<div class="main-content" id="main-content">
    <div class="container mt-5">
        <!-- Display success or error message -->
        <?php if (!empty($_SESSION['message'])): ?>
            <div class="alert alert-info">
                <?= $_SESSION['message']; ?>
            </div>
            <?php $_SESSION['message'] = ''; // Clear message after displaying ?>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Add or Remove Services 
                            <a href="dashboard.php" class="btn btn-primary float-end">BACK</a>
                        </h4>
                    </div>
                    <div class="card-body">
                    <h5>Add New Services</h5>
                        <form method="post" action="">
                            <div class="mb-3">
                                <input type="text" id="newService" name="newService" placeholder="Purchasing" class="form-control">
                            </div>
                            <button type="submit" name="addServiceButton" class="btn btn-primary">Add Service</button>
                            <hr>
                            <h5>Existing Services</h5>
                            <form action="" method="POST" onsubmit="return confirmDelete();">
                                <div class="mb-3">
                                    <select name="serviceToDelete" class="form-select" required>
                                        <?php if ($services->num_rows > 0): ?>
                                            <?php while ($row = $services->fetch_assoc()): ?>
                                                <option value="<?= htmlspecialchars($row['serviceName']) ?>">
                                                    <?= htmlspecialchars($row['serviceName']) ?>
                                                </option>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <option value="">No services available</option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <button type="submit" name="deleteServiceButton" class="btn btn-danger">Remove</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function confirmDelete() {
        return confirm("Are you sure you want to delete this service?");
    }
    function confirmLogout() {
        var confirmation = confirm("Are you sure you want to logout?");
        return confirmation;
    }
</script>
<script src="script.js"></script>
</body>
</html>
