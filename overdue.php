<?php
    session_start();
    require 'dbconn.php';
	// If the user is not logged in, redirect to login page
	if (!isset($_SESSION['user_type'])) {
		header("Location: index.php");
		exit;
	}

	// Optional: Restrict page access based on user_type
	if ($_SESSION['user_type'] !== 'admin') {
		echo "<script>alert('Access denied: Admins only'); window.location.href='appdb.php';</script>";
		exit;
	}
?>
<!doctype html>
<html lang="en">
<head>
    <!-- Meta & Title -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Overdue Details</title>

    <!-- Icons & Bootstrap -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" />
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <!-- Custom Styles -->
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" type="x-icon" href="helmet.png">
</head>
<body>
    <!-- Sidebar -->
	<?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <div class="container mt-4">
            <div class="row">
            <div class="container">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Overdue Details
                            <a href="dashboard.php" class="btn btn-primary float-end">BACK</a>
                            </h4>
                        </div>
                        <div class="card-body">
                            <table id="myTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Serial No.</th>
                                        <th>Applicant's Name</th>
                                        <th>Services</th>
                                        <th>Area / Location Of Work</th>
                                        <th>Permit Expired</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        $query = "SELECT * FROM form 
                                                  WHERE durationTo < CURDATE() 
                                                  AND status NOT IN ('completed', 'cancel', 'stop work')";
                                        $query_run = mysqli_query($conn, $query);

                                        if(mysqli_num_rows($query_run) > 0)
                                        {
                                            foreach($query_run as $ptw)
                                            {
                                                $permitDate = new DateTime($ptw['durationTo']); 
                                                $formattedPermitDate = $permitDate->format('d/m/y');
                                                ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($ptw['id']); ?></td>
                                                    <td><?= htmlspecialchars($ptw['name']); ?></td>
                                                    <td><?= htmlspecialchars($ptw['services']); ?></td>
                                                    <td><?= htmlspecialchars($ptw['exactLocation']); ?></td>
                                                    <td><?= $formattedPermitDate; ?></td>
                                                    <td class="bg-dark text-white">Overdue</td>
                                                    <td>
                                                        <a href="view.php?id=<?= $ptw['id']; ?>" class="btn btn-info btn-sm">View</a>
                                                        <a href="edit.php?id=<?= $ptw['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                        else
                                        {
                                            echo "<h5> No Overdue Records Found </h5>";
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function confirmLogout() {
        return confirm("Are you sure you want to logout?");
    }
    $(document).ready(function() {
        $('#myTable').DataTable({
            "order": [[0, "desc"]],
            "paging": true,
            "searching": true,
            "info": true
        });
    });
    </script>
    <script src="script.js"></script>
</body>
</html>
