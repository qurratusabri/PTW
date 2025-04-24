<?php
    session_start();
    require 'dbconn.php';
	
	// Query for "In Progress" projects
	$queryInProgress = "SELECT * FROM form WHERE status = 'in progress'";
	$resultInProgress = mysqli_query($conn, $queryInProgress);
	$inProgressCount = mysqli_num_rows($resultInProgress);
	
	// Query for "Stop Work" projects
	$queryStopWork = "SELECT * FROM form WHERE status = 'stop work'";
	$resultStopWork = mysqli_query($conn, $queryStopWork);
	$stopWorkCount = mysqli_num_rows($resultStopWork);
	
	// Query for "Complete" projects
	$queryComplete = "SELECT * FROM form WHERE status = 'completed'";
	$resultComplete = mysqli_query($conn, $queryComplete);
	$completeCount = mysqli_num_rows($resultComplete);
	
	// Query for "Cancel" projects
	$queryCancel = "SELECT * FROM form WHERE status = 'cancel'";
	$resultCancel = mysqli_query($conn, $queryCancel);
	$cancelCount = mysqli_num_rows($resultCancel);
	
	// Query for "Pending" projects
	$queryPending = "SELECT * FROM form WHERE status = 'pending'";
	$resultPending = mysqli_query($conn, $queryPending);
	$pendingCount = mysqli_num_rows($resultPending);
	
	// Query for "Overdue" projects (today > durationTo AND still active)
	$queryOverdue = "SELECT * FROM form 
	WHERE durationTo < CURDATE() 
	AND status NOT IN ('completed', 'cancel', 'stop work')";
	$resultOverdue = mysqli_query($conn, $queryOverdue);
	$overdueCount = mysqli_num_rows($resultOverdue);
?>
<!doctype html>
<html lang="en">
	<head>
		<!-- Required meta tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		
		<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
		
		<!-- Bootstrap CSS -->
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
		<link rel="stylesheet" href="style.css">
		<link rel="shortcut icon" type="x-icon" href="helmet.png">
		
		<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" />
		
		<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
		<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
		<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
		<title>Dashboard</title>
	</head>
	<body>
		<div class="sidebar" id="sidebar">
			<div class="top">
				<div class="logo">
					<i class="bx bx-hard-hat"></i>
					<span> PermitToWork</span>
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
		
		<div class="main-content" id="main-content">
			<div class="row">
				<div class="user-label">
					Logged in as: <strong><?= ucfirst($_SESSION['user_type']) ?></strong>
				</div>
				<div class="container mt-4">
					<div class="row">
						<!-- Pending Card -->
						<div class="col-md-3 mb-2">
							<a href="pending.php?status=pending" class="text-white text-decoration-none">
								<div class="card status-card bg-primary text-white">
									<div class="card-header">
										<h4>Pending</h4>
									</div>
									<div class="card-body">
										<h1><?= $pendingCount; ?></h1>
										<p>Projects pending for approval</p>
										<hr>
										<ul>
											<?php while ($row = mysqli_fetch_assoc($resultPending)) : ?>
											<?php endwhile; ?>
										</ul>
									</div>
								</div>
							</a>
						</div>
						<!-- In Progress Card -->
						<div class="col-md-3 mb-2">
							<a href="inProgress.php?status=inProgress" class="text-white text-decoration-none">
								<div class="card status-card bg-warning text-white">
									<div class="card-header">
										<h4>In Progress</h4>
									</div>
									<div class="card-body">
										<h1><?= $inProgressCount; ?></h1>
										<p>Projects currently in progress</p>
										<hr>
										<ul>
											<?php while ($row = mysqli_fetch_assoc($resultInProgress)) : ?>
											<?php endwhile; ?>
										</ul>
									</div>
								</div>
							</a>
						</div>
						
						<!-- Complete Card -->
						<div class="col-md-3 mb-2">
							<a href="complete.php?status=completed" class="text-white text-decoration-none">
								<div class="card status-card bg-success text-white">
									<div class="card-header">
										<h4>Complete</h4>
									</div>
									<div class="card-body">
										<h1><?= $completeCount; ?></h1>
										<p>Projects that are completed</p>
										<hr>
										<ul>
											<?php while ($row = mysqli_fetch_assoc($resultComplete)) : ?>
											<?php endwhile; ?>
										</ul>
									</div>
								</div>
							</a>
						</div>
						
						<!-- Stop Work Card -->
						<div class="col-md-3 mb-2">
							<a href="stopWork.php?status=stopWork" class="text-white text-decoration-none">
								<div class="card status-card bg-secondary text-white">
									<div class="card-header">
										<h4>Stop Work</h4>
									</div>
									<div class="card-body">
										<h1><?= $stopWorkCount; ?></h1>
										<p>Projects that are stop work</p>
										<hr>
										<ul>
											<?php while ($row = mysqli_fetch_assoc($resultStopWork)) : ?>
											<?php endwhile; ?>
										</ul>
									</div>
								</div>
							</a>
						</div>
						<!-- Cancel Card -->
						<div class="col-md-3 mb-2">
							<a href="cancel.php?status=cancel" class="text-white text-decoration-none">
								<div class="card status-card bg-danger text-white">
									<div class="card-header">
										<h4>Cancel</h4>
									</div>
									<div class="card-body">
										<h1><?= $cancelCount; ?></h1>
										<p>Projects that are canceled</p>
										<hr>
										<ul>
											<?php while ($row = mysqli_fetch_assoc($resultCancel)) : ?>
											<!-- List items can be added here -->
											<?php endwhile; ?>
										</ul>
									</div>
								</div>
							</a>
						</div>
						<!-- Overdue Card -->
						<div class="col-md-3 mb-2">
							<a href="overdue.php?status=overdue" class="text-white text-decoration-none">
								<div class="card status-card bg-dark text-white">
									<div class="card-header">
										<h4>Overdue</h4>
									</div>
									<div class="card-body">
										<h1><?= $overdueCount; ?></h1>
										<p>Projects past their permit end date</p>
										<hr>
										<ul>
											<?php while ($row = mysqli_fetch_assoc($resultOverdue)) : ?>
											<!-- Example: <li><?= $row['name']; ?></li> -->
											<?php endwhile; ?>
										</ul>
									</div>
								</div>
							</a>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-12">
				<div class="card">
					<div class="card-header">
                        <h4 style="color:black;">Submission Details
							<a href="form.php" class="btn btn-primary float-end">Add Project</a>
							<div class="float-end me-2 d-flex align-items-center">
								<h6 for="filterDate" class="me-2" style="font-weight: bold;">Sort by Date:</h6>
								<input 
                                type="date" 
							id="filterDate" 
							class="form-control" 
							style="width: auto;" 
							placeholder="Select Date">
							</div>
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
							<th>Submission Date</th>
							<th>Status</th>
							<th>Action</th>
                            </tr>
							</thead>
							<tbody>
                            <?php 
                            $query = "SELECT * FROM form ORDER BY id DESC"; // Newest records first
                            $query_run = mysqli_query($conn, $query);
							
                            if (mysqli_num_rows($query_run) > 0) {
							foreach ($query_run as $ptw) {
							// Format the date to dd/mm/yy 
							$dateTime = new DateTime($ptw['date']); 
							$formattedDate = $dateTime->format('d/m/y');
							$formattedTime = $dateTime->format('h:i A');
							?>
							<tr>
							<td><?= htmlspecialchars($ptw['id']); ?></td>
							<td><?= htmlspecialchars($ptw['name']); ?></td>
							<td><?= htmlspecialchars($ptw['services']); ?></td>
							<td><?= htmlspecialchars($ptw['exactLocation']); ?></td>
							<td><?= htmlspecialchars($formattedDate . ' ' . $formattedTime); ?></td>
							<td class="<?= $ptw['status'] == 'in progress' ? 'bg-warning' : ($ptw['status'] == 'completed' ? 'bg-success' : ($ptw['status'] == 'stop work' ? 'bg-secondary' : ($ptw['status'] == 'cancel' ? 'bg-danger' : ($ptw['status'] == 'pending' ? 'bg-primary' :  '')))) ?>"> 
							<?= $ptw['status'] == 'in progress' ? 'In progress' : ($ptw['status'] == 'completed' ? 'Completed' : ($ptw['status'] == 'stop work' ? 'Stop Work' : ($ptw['status'] == 'cancel' ? 'Cancel' : ($ptw['status'] == 'pending' ? 'Pending' : '')))) ?>
							</td>
							<td>
							<a href="view.php?id=<?= $ptw['id']; ?>" class="btn btn-info btn-sm">View</a>
							<a href="edit.php?id=<?= $ptw['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
							</td>
							</tr>
							<?php
							}
                            } else {
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
							function confirmLogout() {
							var confirmation = confirm("Are you sure you want to logout?");
							return confirmation;
							}
							$(document).ready(function () {
							// Initialize DataTable
							var table = $('#myTable').DataTable({
							"order": [[0, "desc"]], // Sort by the first column in descending order
							"paging": true,         // Enable pagination
							"searching": true,      // Enable search box
							"info": true            // Show table information
							});
							// Custom date filter
							$('#filterDate').on('change', function () {
							var rawDate = $(this).val(); // Get the selected date in YYYY-MM-DD format
							if (rawDate) {
							// Convert to dd/mm/yy format
							var formattedDate = formatDateToDDMMYY(rawDate);
							
							// Filter the Submission Date column (index 4) using the formatted date
							table.column(4).search(formattedDate).draw();
							} else {
							// If no date is selected, clear the filter
							table.column(4).search('').draw();
							}
							});
							
							// Function to convert date to dd/mm/yy
							function formatDateToDDMMYY(date) {
							var [year, month, day] = date.split('-'); // Split the YYYY-MM-DD string
							return `${day}/${month}/${year.substring(2)}`; // Return dd/mm/yy format
							}
							});
							</script>
							<script src="script.js"></script>
							</body>
							</html>							