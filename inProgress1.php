<?php
    session_start();
    require 'dbconn.php';
?>
<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" />
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" type="x-icon" href="helmet.png">

    <title>In Progress Details</title>
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
                <a href="appdb.php">
                    <i class="bx bxs-grid-alt"></i>
                    <span class="nav-item">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="form1.php">
                    <i class="bx bx-file-blank"></i>
                    <span class="nav-item">Form</span>
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

    <div class="main-content" id="main-content">
        <div class="container mt-4">
            <div class="row">
            <div class="container">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>In Progress Details
                            <a href="appdb.php" class="btn btn-primary float-end">BACK</a>
                            </h4>
                        </div>
                        <div class="card-body">
                            <table id="myTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Applicant's Name</th>
                                        <th>Services</th>
                                        <th>Area / Location Of Work</th>
                                        <th>Submission Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        $query = "SELECT * FROM form WHERE status = 'in progress'";
                                        $query_run = mysqli_query($conn, $query);

                                        if(mysqli_num_rows($query_run) > 0)
                                        {
                                            foreach($query_run as $ptw)
                                            {
                                                 // Format the date to dd/mm/yy 
                                            $dateTime = new DateTime($ptw['date']); 
                                            $formattedDate = $dateTime->format('d/m/y');
                                            $formattedTime = $dateTime->format('h:i A');
                                                ?>
                                                <tr>
                                                    <td><?= $ptw['name']; ?></td>
                                                    <td><?= $ptw['services']; ?></td>
                                                    <td><?= $ptw['exactLocation']; ?></td>
                                                    <td><?= ($formattedDate . ' ' . $formattedTime); ?></td>
                                                    <td>
                                                        <a href="view1.php?id=<?= $ptw['id']; ?>" class="btn btn-info btn-sm">View</a>
                                                        <form action="code.php" method="POST" class="d-inline"></form>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                        else
                                        {
                                            echo "<h5> No Record Found </h5>";
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
        let btn = document.querySelector('#btn');
        let sidebar = document.querySelector('.sidebar');

        btn.onclick = function () {
            sidebar.classList.toggle('active');
        };
    });
    function confirmLogout() {
        var confirmation = confirm("Are you sure you want to logout?");
        return confirmation;
    }
    $(document).ready(function() {
    $('#myTable').DataTable({
        "order": [[0, "desc"]], // Sort by the first column in descending order
        "paging": true,        // Enable pagination
        "searching": true,     // Enable search box
        "info": true           // Show table information
    });
});
    </script>
</body>
</html>
