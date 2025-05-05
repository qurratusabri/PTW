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
	
	// Assuming you're fetching the form record from the database based on applicant ID
	if (isset($_GET['id'])) {
		$applicantID = mysqli_real_escape_string($conn, $_GET['id']);
		$query = "SELECT * FROM form WHERE id='$applicantID'";
		$query_run = mysqli_query($conn, $query);
		
		if (mysqli_num_rows($query_run) > 0) {
			$ptw = mysqli_fetch_array($query_run);
			// Ensure properly exploded into an array
			$hazardsArray = !empty($ptw['hazards']) ? explode(", ", $ptw['hazards']) : [];
			$ppeArray = !empty($ptw['ppe']) ? explode(", ", $ptw['ppe']) : [];
			$workTypeArray = !empty($ptw['workType']) ? explode(", ", $ptw['workType']) : [];
			$worksiteArray = !empty($ptw['worksite']) ? explode(", ", $ptw['worksite']) : [];
			$infectionArray = !empty($ptw['infection']) ? explode(", ", $ptw['infection']) : [];
			$workersNames = explode(", ", $ptw['workersName']);  // Convert to array
			$workTypes = !empty($ptw['workType']) ? explode(", ", $ptw['workType']) : [];
			$worksite = !empty($ptw['worksite']) ? explode(", ", $ptw['worksite']) : [];
			$hazards = !empty($ptw['hazards']) ? explode(", ", $ptw['hazards']) : [];
			$infection = !empty($ptw['infection']) ? explode(", ", $ptw['infection']) : [];
			
			if (!empty($ptw['workersName'])) {
				$workerNamesArray = explode(", ", $ptw['workersName']);
				} else {
				$workerNamesArray = [];
			}
			
			if (!empty($ptw['passNo'])) {
				$passNoArray = explode(", ", $ptw['passNo']);
				} else {
				$passNoArray = [];
			}
			
			if (isset($_GET['id'])) {
				$applicantID = mysqli_real_escape_string($conn, $_GET['id']);
				
				// Fetch from 'form' table
				$query = "SELECT * FROM form WHERE id='$applicantID'";
				$query_run = mysqli_query($conn, $query);
				
				if (mysqli_num_rows($query_run) > 0) {
					$ptw = mysqli_fetch_array($query_run);
					
					// Arrays for multiple checkbox values
					$hazardsArray = !empty($ptw['hazards']) ? explode(", ", $ptw['hazards']) : [];
					$ppeArray = !empty($ptw['ppe']) ? explode(", ", $ptw['ppe']) : [];
					$workTypeArray = !empty($ptw['workType']) ? explode(", ", $ptw['workType']) : [];
					$worksiteArray = !empty($ptw['worksite']) ? explode(", ", $ptw['worksite']) : [];
					$infectionArray = !empty($ptw['infection']) ? explode(", ", $ptw['infection']) : [];
					
					$workTypeOthersText = '';
					$worksiteOthersText = '';
					$hazardsOthersText = '';
					$infectionOthersText = '';
					
					$predefinedWorkTypes = [
					"Aircond / Chiller", "Pest Control", "Civil / Structural", "Roofing", "Sewage",
					"Furniture", "Painting (internal / external)", "Flooring", "Wiring", "Electrical",
					"Plumbing", "Cabling", "Maintenance", "HEPA filter Servicing", "High Dusting",
					"Exterior facade cleaning", "Renovation", "PPM", "Corrective Maintenance", "Equipment breakdown"
					];
					
					$predefinedWorksites = [
					"Site prepared as informed", "Scaffold Required", "Toxic Fumes Detector",
					"PMA / PMT e.g crane", "Gas Detector", "Forced Ventilation", "Equipment Isolated",
					"LOTO", "Additional Fire Extinguisher / blanket", "Equipment / Line Drained / Blinded",
					"Area Barricaded / Signed", "Confined Space", "Secure Tools from Falling",
					"Noise / Dust Insulation", "Ladder / Step Stool", "Spillage Kits",
					"Inform Workers In and the Next Area", "Hot work"
					];
					
					$predefinedHazards = [
					"Mechanical", "Biological", "Electrical", "Chemical", "Working > 24 hours"
					];
					
					$predefinedInfectionControls = [
					"Wet floor mat", "Canvas", "Seal the area (dust prevention)", "Assigned designated lift",
					"Exhaust ventilation (no broom)", "Assigned designated entry / exit", "Low odour chemicals",
					"Wet mop with disinfectant", "Clean / Dirty Shoes", "Negative pressure",
					"Waste segregation required", "Provide covered waste Bin"
					];
					
					// For workType
					foreach ($workTypeArray as $key => $type) {
						if (!in_array($type, $predefinedWorkTypes)) {
							$workTypeOthersText = $type;
							unset($workTypeArray[$key]);
							break; // only one "others" text
						}
					}
					
					// For worksite
					foreach ($worksiteArray as $key => $site) {
						if (!in_array($site, $predefinedWorksites)) {
							$worksiteOthersText = $site;
							unset($worksiteArray[$key]);
							break; // only one "others" text
						}
					}
					
					foreach ($hazardsArray as $key => $hazard) {
						if (!in_array($hazard, $predefinedHazards)) {
							$hazardsOthersText = $hazard;
							unset($hazardsArray[$key]);
							break;
						}
					}
					
					foreach ($infectionArray as $key => $inf) {
						if (!in_array($inf, $predefinedInfectionControls)) {
							$infectionOthersText = $inf;
							unset($infectionArray[$key]);
							break;
						}
					}
					
					$workerNamesArray = !empty($ptw['workersName']) ? explode(", ", $ptw['workersName']) : [];
					$passNoArray = !empty($ptw['passNo']) ? explode(", ", $ptw['passNo']) : [];
					
					// Also fetch from 'permit' table
					$permit_query = "SELECT * FROM permit WHERE id='$applicantID'";
					$permit_result = mysqli_query($conn, $permit_query);
					
					if (mysqli_num_rows($permit_result) > 0) {
						$permit = mysqli_fetch_array($permit_result);
						
						// Permit signature & details (Contractor, Area Owner, ICO, SHO)
						$signC = $permit['signC'];
						$nameC = $permit['nameC'];
						$positionC = $permit['positionC'];
						$dateC = $permit['dateC'];
						$timeC = $permit['timeC'];
						
						$signA = $permit['signA'];
						$nameA = $permit['nameA'];
						$positionA = $permit['positionA'];
						$dateA = $permit['dateA'];
						$timeA = $permit['timeA'];
						
						$signI = $permit['signI'];
						$nameI = $permit['nameI'];
						$positionI = $permit['positionI'];
						$dateI = $permit['dateI'];
						$timeI = $permit['timeI'];
						
						$signS = $permit['signS'];
						$nameS = $permit['nameS'];
						$positionS = $permit['positionS'];
						$dateS = $permit['dateS'];
						$timeS = $permit['timeS'];
					}
				}
			}
			
		}
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
		<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
		<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
		<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
		<link rel="stylesheet" href="style.css">
		<link rel="shortcut icon" type="x-icon" href="helmet.png">
		<style>
			input[type="file"].error {
			border: 2px solid red;
			}
			#drop-area {
			border: 2px dashed #ccc;
			border-radius: 10px;
			padding: 30px;
			text-align: center;
			margin-bottom: 20px;
			}
			.file-item {
			display: flex;
			align-items: center;
			margin: 8px 0;
			padding: 10px;
			background-color: #f8f9fa;
			border-radius: 6px;
			}
			.file-icon {
			margin-right: 10px;
			font-size: 20px;
			color: #007bff;
			}
			.file-name {
			flex-grow: 1;
			}
			.delete-btn {
			color: red;
			cursor: pointer;
			}
		</style>
		<title>Edit Form</title>
	</head>
	<body>
		<!-- Sidebar -->
		<?php include 'sidebar.php'; ?>
		
		<div class="main-content" id="main-content">
			<div class="container mt-5">
				<?php include('message.php'); ?>
				<div class="row">
					<div class="col-md-12">
						<div class="card">
							<div class="card-header">
								<h4>Edit Form 
									<a href="dashboard.php" class="btn btn-primary float-end">BACK</a>
								</h4>
							</div>
							<div class="card-body">
								<h4>KPJ KLANG SPECIALIST HOSPITAL (Project Manager / Coordinator)</h4>
								<?php
									if(isset($_GET['id']))
									{
										$applicantID = mysqli_real_escape_string($conn, $_GET['id']);
										$query = "SELECT * FROM form WHERE id='$applicantID' ";
										$query_run = mysqli_query($conn, $query);
										
										if(mysqli_num_rows($query_run) > 0)
										{
											$ptw = mysqli_fetch_array($query_run);
										?>
										
										<form action="code.php" method="POST" enctype="multipart/form-data">
											<input type="hidden" name="applicantID" value="<?= $ptw['id']; ?>">
											<div class="row mb-4">
												<div class="col-md-4">
													<label>Applicant's Name</label>
													<input type="text" name="name" value="<?=$ptw['name'];?>" class="form-control">
												</div>
												<div class="col-md-4">
													<label>Services</label>
													<select name="services" class="form-select">
														<?php if (!empty($ptw['services'])): ?>
														<!-- Display the previous value as the first option -->
														<option value="<?= htmlspecialchars($ptw['services']); ?>" selected>
															<?= htmlspecialchars($ptw['services']); ?>
														</option>
														<?php endif; ?>
														
														<!-- Populate the dropdown with other services from the database -->
														<?php
															$services = $conn->query("SELECT serviceName FROM services");
															if ($services->num_rows > 0) {
																while ($row = $services->fetch_assoc()) {
																	// Skip adding the current service again in the list
																	if ($row['serviceName'] != $ptw['services']) {
																		echo "<option value='" . htmlspecialchars($row['serviceName']) . "'>" . htmlspecialchars($row['serviceName']) . "</option>";
																	}
																}
																} else {
																echo "<option value=''>No services available</option>";
															}
														?>
													</select>
												</div>
												<div class="col-md-4">
													<label>Serial No.</label>
													<input type="text" name="id" value="<?=$ptw['id'];?>" class="form-control" disabled>
												</div>
											</div>
											<div class="row mb-4">
												<h6>Work Duration (date):</h6>
												<div class="col-md-4">
													<label for="durationFrom">From:</label>
												<input type="date" name="durationFrom" value="<?=$ptw['durationFrom'];?>" class="form-control" ></div>
												<div class="col-md-4">
													<label for="durationTo">To:</label>
													<input type="date" name="durationTo" value="<?=$ptw['durationTo'];?>" class="form-control">
												</div>
											</div>
											<div class="row mb-3">
												<h6>Work Time:</h6>
												<div class="col-md-4">
													<label for="timeFrom">From:</label>
												<input type="time" name="timeFrom" value="<?=$ptw['timeFrom'];?>" class="form-control"></div>
												<div class="col-md-4">
													<label for="timeTo">To:</label>
													<input type="time" name="timeTo" value="<?=$ptw['timeTo'];?>" class="form-control">
												</div>
											</div>
											<br>
											<hr>
											<div class="row mb-3">
												<h4>CONTRACTOR</h4>
												<div class="col-md-4">
													<label for="companyName">Company Name:</label>
													<input type="text" name="companyName" value="<?=$ptw['companyName'];?>" class="form-control">
												</div>
												<div class="col-md-4">
													<label for="svName">Supervisor Name:</label>
													<input type="text" name="svName" value="<?=$ptw['svName'];?>" class="form-control">
												</div>
												<div class="col-md-4">
													<label for="icNo">IC No./Passport No:</label>
													<input type="text" name="icNo" value="<?=$ptw['icNo'];?>" class="form-control">
												</div>
												<div class="col-md-4">
													<label for="contactNo">Contact No.:</label>
													<input type="tel" name="contactNo" value="<?=$ptw['contactNo'];?>" class="form-control">
												</div>
												<div class="col-md-4">
													<label for="longTermContract">Term of Contract:</label>
													<input type="text" name="longTermContract" value="<?=$ptw['longTermContract'];?>" class="form-control">
												</div>
											</div>
											<div class="row mb-4">
												<div class="col-md-4">
													<table>
														<tr>
															<th>No.</th>
															<th>Worker Name</th>
															<th>IC No./Passport No.</th>
														</tr>
														<?php
															// Display workers' names in numbered rows
															$counter = 1;
															$index = 0;  // Initialize the index variable
															foreach ($workersNames as $worker) {
																echo '<tr>';
																echo '<td>' . $counter . '</td>';
																echo '<td><input type="text" name="workersName[]" value="' . htmlspecialchars($worker) . '" class="form-control"></td>';
																$passNo = isset($passNoArray[$index]) ? htmlspecialchars($passNoArray[$index]) : '';
																echo '<td><input type="text" name="passNo[]" value="' . $passNo . '" class="form-control"></td>';  // Include the pass number input
																echo '</tr>';
																$counter++;
																$index++;  // Increment the index variable
															}
														?>
													</table>
												</div>
												<div class="col-md-4">
													<h4>AREA / LOCATION OF WORK</h4>
													<label for="exactLocation">Exact Location of Work:</label>
													<input type="text" name="exactLocation" value="<?=$ptw['exactLocation'];?>" class="form-control">
												</div>
											</div>
											
											<hr>
											<div class="row mb-4">
												<div class="col-md-4">
													<h4>TYPE OF WORK</h4>
													<h6>Select Type(s) of Work:</h6>
													<input type="checkbox" name="workType[]" value="Aircond / Chiller" <?php echo in_array('Aircond / Chiller', $workTypeArray) ? 'checked' : ''; ?>>
													<label for="aircond">Aircond/Chiller</label><br>
													<input type="checkbox" name="workType[]" value="Pest Control" <?php echo in_array('Pest Control', $workTypeArray) ? 'checked' : ''; ?>>
													<label for="pc">Pest Control</label><br>
													<input type="checkbox" name="workType[]" value="Civil / Structural" <?php echo in_array('Civil / Structural', $workTypeArray) ? 'checked' : ''; ?>>
													<label for="cs">Civil/Structural</label><br>
													<input type="checkbox" name="workType[]" value="Roofing" <?php echo in_array('Roofing', $workTypeArray) ? 'checked' : ''; ?>>
													<label for="roof">Roofing</label><br>
													<input type="checkbox" name="workType[]" value="Sewage" <?php echo in_array('Sewage', $workTypeArray) ? 'checked' : ''; ?>>
													<label for="sewage">Sewage</label><br>
													<input type="checkbox" name="workType[]" value="Furniture" <?php echo in_array('Furniture', $workTypeArray) ? 'checked' : ''; ?>>
													<label for="furniture">Furniture</label><br>
													<input type="checkbox" name="workType[]" value="Painting (internal / external)" <?php echo in_array('Painting (internal / external)', $workTypeArray) ? 'checked' : ''; ?>>
													<label for="painting">Painitng(internal/external)</label><br>
													<input type="checkbox" name="workType[]" value="Flooring" <?php echo in_array('Flooring', $workTypeArray) ? 'checked' : ''; ?>>
													<label for="flooring">Flooring</label><br>
													<input type="checkbox" name="workType[]" value="Wiring" <?php echo in_array('Wiring', $workTypeArray) ? 'checked' : ''; ?>>
													<label for="wiring">Wiring</label><br>
													<input type="checkbox" name="workType[]" value="Electrical" <?php echo in_array('Electrical', $workTypeArray) ? 'checked' : ''; ?>>
													<label for="electrical">Electrical</label><br>
													<input type="checkbox" name="workType[]" value="Plumbing" <?php echo in_array('Plumbing', $workTypeArray) ? 'checked' : ''; ?>>
													<label for="plum">Plumbing</label><br>
													<input type="checkbox" name="workType[]" value="Cabling" <?php echo in_array('Cabling', $workTypeArray) ? 'checked' : ''; ?>>
													<label for="cable">Cabling</label><br>
													<input type="checkbox" name="workType[]" value="Maintenance" <?php echo in_array('Maintenance', $workTypeArray) ? 'checked' : ''; ?>>
													<label for="maintain">Maintenance</label><br>
													<input type="checkbox" name="workType[]" value="HEPA filter Servicing" <?php echo in_array('HEPA filter Servicing', $workTypeArray) ? 'checked' : ''; ?>>
													<label for="hepa">HEPA filter Servicing</label><br>
													<input type="checkbox" name="workType[]" value="High Dusting" <?php echo in_array('High Dusting', $workTypeArray) ? 'checked' : ''; ?>>
													<label for="hd">High Dusting</label><br>
													<input type="checkbox" name="workType[]" value="Exterior facade cleaning" <?php echo in_array('Exterior facade cleaning', $workTypeArray) ? 'checked' : ''; ?>>
													<label for="efc">Exterior facade cleaning</label><br>
													<input type="checkbox" name="workType[]" value="Renovation" <?php echo in_array('Renovation', $workTypeArray) ? 'checked' : ''; ?>>
													<label for="renovate">Renovation</label><br>
													<input type="checkbox" name="workType[]" value="PPM" <?php echo in_array('PPM', $workTypeArray) ? 'checked' : ''; ?>>
													<label for="ppm">PPM</label><br>
													<input type="checkbox" name="workType[]" value="Corrective Maintenance" <?php echo in_array('Corrective Maintenance', $workTypeArray) ? 'checked' : ''; ?>>
													<label for="cm">Corrective Maintenance</label><br>
													<input type="checkbox" name="workType[]" value="Equipment breakdown" <?php echo in_array('Equipment breakdown', $workTypeArray) ? 'checked' : ''; ?>>
													<label for="eb">Equipment breakdown</label><br>
													<input type="checkbox" id="workType_other_checkbox" value="others" <?php echo !empty($workTypeOthersText) ? 'checked' : ''; ?>>
													<label for="workType_other_checkbox">Others:</label><br>
													<textarea id="workType_other_text" name="workType[]" class="form-control" style="<?php echo !empty($workTypeOthersText) ? '' : 'display: none;'; ?>" placeholder="Specify other work type"><?= htmlspecialchars($workTypeOthersText); ?></textarea>
													
												</div>
												<div class="col-md-4">
													<h4>WORKSITE PREPARATION / PRECAUTIONS</h4>
													<h6>Select:</h6>
													<input type="checkbox" name="worksite[]" value="Site prepared as informed" <?php echo in_array('Site prepared as informed', $worksiteArray) ? 'checked' : ''; ?>>
													<label for="site">Site prepared as informed</label><br>
													<input type="checkbox" name="worksite[]" value="Scaffold Required" <?php echo in_array('Scaffold Required', $worksiteArray) ? 'checked' : ''; ?>>
													<label for="scaffold">Scaffold Required</label><br>
													<input type="checkbox" name="worksite[]" value="Toxic Fumes Detector" <?php echo in_array('Toxic Fumes Detector', $worksiteArray) ? 'checked' : ''; ?>>
													<label for="toxic">Toxic Fumes Detector</label><br>
													<input type="checkbox" name="worksite[]" value="PMA / PMT e.g crane" <?php echo in_array('PMA / PMT e.g crane', $worksiteArray) ? 'checked' : ''; ?>>
													<label for="pma">PMA / PMT e.g crane</label><br>
													<input type="checkbox" name="worksite[]" value="Gas Detector" <?php echo in_array('Gas Detector', $worksiteArray) ? 'checked' : ''; ?>>
													<label for="gas">Gas Detector</label><br>
													<input type="checkbox" name="worksite[]" value="Forced Ventilation" <?php echo in_array('Forced Ventilation', $worksiteArray) ? 'checked' : ''; ?>>
													<label for="forced">Forced Ventilation</label><br>
													<input type="checkbox" name="worksite[]" value="Equipment Isolated" <?php echo in_array('Equipment Isolated', $worksiteArray) ? 'checked' : ''; ?>>
													<label for="equipment">Equipment Isolated</label><br>
													<input type="checkbox" name="worksite[]" value="LOTO" <?php echo in_array('LOTO', $worksiteArray) ? 'checked' : ''; ?>>
													<label for="loto">LOTO</label><br>
													<input type="checkbox" name="worksite[]" value="Additional Fire Extinguisher / blanket" <?php echo in_array('Additional Fire Extinguisher / blanket', $worksiteArray) ? 'checked' : ''; ?>>
													<label for="additional">Additional Fire Extinguisher / blanket</label><br>
													<input type="checkbox" name="worksite[]" value="Equipment / Line Drained / Blinded" <?php echo in_array('Equipment / Line Drained / Blinded', $worksiteArray) ? 'checked' : ''; ?>>
													<label for="blinded">Equipment / Line Drained / Blinded</label><br>
													<input type="checkbox" name="worksite[]" value="Area Barricaded / Signed" <?php echo in_array('Area Barricaded / Signed', $worksiteArray) ? 'checked' : ''; ?>>
													<label for="area">Area Barricaded / Signed</label><br>
													<input type="checkbox" name="worksite[]" value="Confined Space" <?php echo in_array('Confined Space', $worksiteArray) ? 'checked' : ''; ?>>
													<label for="confined">Confined Space</label><br>
													<input type="checkbox" name="worksite[]" value="Secure Tools from Falling" <?php echo in_array('Secure Tools from Falling', $worksiteArray) ? 'checked' : ''; ?>>
													<label for="secure">Secure Tools from Falling</label><br>
													<input type="checkbox" name="worksite[]" value="Noise / Dust Insulation" <?php echo in_array('Noise / Dust Insulation', $worksiteArray) ? 'checked' : ''; ?>>
													<label for="noise">Noise / Dust Insulation</label><br>
													<input type="checkbox" name="worksite[]" value="Ladder / Step Stool" <?php echo in_array('Ladder / Step Stool', $worksiteArray) ? 'checked' : ''; ?>>
													<label for="ladder">Ladder / Step Stool</label><br>
													<input type="checkbox" name="worksite[]" value="Spillage Kits" <?php echo in_array('Spillage Kits', $worksiteArray) ? 'checked' : ''; ?>>
													<label for="spillage">Spillage Kits</label><br>
													<input type="checkbox" name="worksite[]" value="Inform Workers In and the Next Area" <?php echo in_array('Inform Workers In and the Next Area', $worksiteArray) ? 'checked' : ''; ?>>
													<label for="inform">Inform Workers In and the Next Area</label><br>
													<input type="checkbox" name="worksite[]" value="Hot work" <?php echo in_array('Hot work', $worksiteArray) ? 'checked' : ''; ?>>
													<label for="hot">Hot work</label><br>
													<input type="checkbox" id="worksite_other_checkbox" value="others" <?php echo !empty($worksiteOthersText) ? 'checked' : ''; ?>>
													<label for="worksite_other_checkbox">If Others please state:</label><br>
													<textarea id="worksite_other_text" name="worksite[]" class="form-control" style="<?php echo !empty($worksiteOthersText) ? '' : 'display: none;'; ?>" placeholder="Specify other worksite preparation"><?= htmlspecialchars($worksiteOthersText); ?></textarea>
													
												</div>                                    
												<div class="col-md-4">
													<h4>PERSONAL PROTECTIVE EQUIPMENTS</h4>
													<h6>Select PPE:</h6>
													<input type="checkbox" name="ppe[]" value="Safety Helmet" <?php echo in_array('Safety Helmet', $ppeArray) ? 'checked' : ''; ?>>
													<label for="helmet">Safety Helmet</label><br>
													<input type="checkbox" name="ppe[]" value="Face Shield" <?php echo in_array('Face Shield', $ppeArray) ? 'checked' : ''; ?>>
													<label for="fc">Safe Shield</label><br>
													<input type="checkbox" name="ppe[]" value="Welding Mask" <?php echo in_array('Welding Mask', $ppeArray) ? 'checked' : ''; ?>>
													<label for="wm">Welding Mask</label><br>
													<input type="checkbox" name="ppe[]" value="Safety Shoes" <?php echo in_array('Safety Shoes', $ppeArray) ? 'checked' : ''; ?>>
													<label for="shoes">Safety Shoes</label><br>
													<input type="checkbox" name="ppe[]" value="Chemical Boots" <?php echo in_array('Chemical Boots', $ppeArray) ? 'checked' : ''; ?>>
													<label for="boots">Chemical Boots</label><br>
													<input type="checkbox" name="ppe[]" value="Leather Gloves" <?php echo in_array('Leather Gloves', $ppeArray) ? 'checked' : ''; ?>>
													<label for="lg">Leather Gloves</label><br>
													<input type="checkbox" name="ppe[]" value="Safety Googles" <?php echo in_array('Safety Google', $ppeArray) ? 'checked' : ''; ?>>
													<label for="goggles">Safety Goggles</label><br>
													<input type="checkbox" name="ppe[]" value="Canvas" <?php echo in_array('Canvas', $ppeArray) ? 'checked' : ''; ?>>
													<label for="canvas">Canvas</label><br>
													<input type="checkbox" name="ppe[]" value="Gloves" <?php echo in_array('Gloves', $ppeArray) ? 'checked' : ''; ?>>
													<label for="gloves">Gloves</label><br>
													<input type="checkbox" name="ppe[]" value="Full Body Harness" <?php echo in_array('Full Body Harness', $ppeArray) ? 'checked' : ''; ?>>
													<label for="fbh">Full Body Harness</label><br>
													<input type="checkbox" name="ppe[]" value="Ear plug / ear muff" <?php echo in_array('Ear plug / ear muff', $ppeArray) ? 'checked' : ''; ?>>
													<label for="ear">Ear plug/ear muff</label><br>
													<label>Respirator:</label><br>
													<input type="checkbox" name="ppe[]" value="Dusk Mask" <?php echo in_array('Dusk Mask', $ppeArray) ? 'checked' : ''; ?>>
													<label for="dusk">Dusk Mask</label><br>
													<input type="checkbox" name="ppe[]" value="Fumes Mask" <?php echo in_array('Fumes Mask', $ppeArray) ? 'checked' : ''; ?>>
													<label for="fumes">Fumes Mask</label><br>
													<input type="checkbox" name="ppe[]" value="Painting Mask" <?php echo in_array('Painting Mask', $ppeArray) ? 'checked' : ''; ?>>
													<label for="painting">Painting Mask</label><br>
												</div>
											</div>
											<hr>
											<div class="row mb-4">
												<div class="col-md-4">
													<h4>HAZARD ANALYSIS</h4>
													<h6>Select Hazards:</h6>
													<input type="checkbox" name="hazards[]" value="Mechanical" <?php echo in_array('Mechanical', $hazardsArray) ? 'checked' : ''; ?>>
													<label for="mechanical">Mechanical</label><br>
													<input type="checkbox" name="hazards[]" value="Biological" <?php echo in_array('Biological', $hazardsArray) ? 'checked' : ''; ?>>
													<label for="biological">Biological</label><br>
													<input type="checkbox" name="hazards[]" value="Electrical" <?php echo in_array('Electrical', $hazardsArray) ? 'checked' : ''; ?>>
													<label for="electrical">Electrical</label><br>
													<input type="checkbox" name="hazards[]" value="Chemical" <?php echo in_array('Chemical', $hazardsArray) ? 'checked' : ''; ?>>
													<label for="chemical">Chemical</label><br>
													<label for="others">Others:</label><br>
													<input type="checkbox" name="hazards[]" value="Working > 24 hours" <?php echo in_array('Working > 24 hours', $hazardsArray) ? 'checked' : ''; ?>>
													<label for="others">Working > 24 hours</label><br>
													<input type="checkbox" id="hazards_other_checkbox" value="others" <?php echo !empty($hazardsOthersText) ? 'checked' : ''; ?>>
													<label for="hazards_other_checkbox">Others:</label><br>
													<textarea id="hazards_other_text" name="hazards[]" class="form-control" style="<?php echo !empty($hazardsOthersText) ? '' : 'display: none;'; ?>" placeholder="Specify other hazard"><?= htmlspecialchars($hazardsOthersText); ?></textarea>
													
												</div>
												<div class="col-md-4">
													<h4>INFECTION CONTROL</h4>
													<input type="checkbox" name="infection[]" value="Wet floor mat" <?php echo in_array('Wet floor mat', $infectionArray) ? 'checked' : ''; ?>>
													<label for="wet">Wet floor mat</label><br>
													<input type="checkbox" name="infection[]" value="Canvas" <?php echo in_array('Canvas', $infectionArray) ? 'checked' : ''; ?>>
													<label for="canvas">Canvas</label><br>
													<input type="checkbox" name="infection[]" value="Seal the area (dust prevention)" <?php echo in_array('Seal the area (dust prevention)', $infectionArray) ? 'checked' : ''; ?>>
													<label for="seal">Seal the area (dust prevention)</label><br>
													<input type="checkbox" name="infection[]" value="Assigned designated lift" <?php echo in_array('Assigned designated lift', $infectionArray) ? 'checked' : ''; ?>>
													<label for="assigned">Assigned designated lift</label><br>
													<input type="checkbox" name="infection[]" value="Exhaust ventilation (no broom)" <?php echo in_array('Exhaust ventilation (no broom)', $infectionArray) ? 'checked' : ''; ?>>
													<label for="exhaust">Exhaust ventilation (no broom)</label><br>
													<input type="checkbox" name="infection[]" value="Assigned designated entry / exit" <?php echo in_array('Assigned designated entry / exit', $infectionArray) ? 'checked' : ''; ?>>
													<label for="designated">Assigned designated entry / exit</label><br>
													<input type="checkbox" name="infection[]" value="Low odour chemicals" <?php echo in_array('Low odour chemicals', $infectionArray) ? 'checked' : ''; ?>>
													<label for="low">Low odour chemicals</label><br>
													<input type="checkbox" name="infection[]" value="Wet mop with disinfectant" <?php echo in_array('Wet mop with disinfectant', $infectionArray) ? 'checked' : ''; ?>>
													<label for="mop">Wet mop with disinfectant</label><br>
													<input type="checkbox" name="infection[]" value="Clean / Dirty Shoes" <?php echo in_array('Clean / Dirty Shoes', $infectionArray) ? 'checked' : ''; ?>>
													<label for="clean">Clean / Dirty Shoes</label><br>
													<input type="checkbox" name="infection[]" value="Negative pressure" <?php echo in_array('Negative pressure', $infectionArray) ? 'checked' : ''; ?>>
													<label for="negative">Negative pressure</label><br>
													<input type="checkbox" name="infection[]" value="Waste segregation required" <?php echo in_array('Waste segregation required', $infectionArray) ? 'checked' : ''; ?>>
													<label for="segregation">Waste segregation required</label><br>
													<input type="checkbox" name="infection[]" value="Provide covered waste Bin" <?php echo in_array('Provide covered waste Bin', $infectionArray) ? 'checked' : ''; ?>>
													<label for="provide">Provide covered waste Bin</label><br>
													<input type="checkbox" id="infection_other_checkbox" value="others" <?php echo !empty($infectionOthersText) ? 'checked' : ''; ?>>
													<label for="infection_other_checkbox">Others:</label><br>
													<textarea id="infection_other_text" name="infection[]" class="form-control" style="<?php echo !empty($infectionOthersText) ? '' : 'display: none;'; ?>" placeholder="Specify other infection"><?= htmlspecialchars($infectionOthersText); ?></textarea>
													
												</div>
											</div>
											<hr>
											<div class="row mb-3">
												<h4>SAFETY BRIEFING RECORD</h4>
												<div class="col-md-4">
													<label for="briefDate">Date:</label>
												<input type="date" name="briefDate" value="<?=$ptw['briefDate'];?>" class="form-control"></div>
												<div class="col-md-4">      
													<label for="briefTime">Time:</label>
												<input type="time" name="briefTime" value="<?=$ptw['briefTime'];?>" class="form-control"></div>
												<div class="col-md-4">
													<label for="briefConducted">Conducted by:</label>
													<input type="text" name="briefConducted" value="<?=$ptw['briefConducted'];?>" class="form-control">
												</div>
											</div>
											<br>
											<hr>
											<div class="row mb-3">
												<h4>Permit Authorisation Section</h4>
												
												<!-- Contractor -->
												<div class="col-md-3">
													<h6><u>Contractor</u></h6>
													<label for="signC">Signature:</label><br>
													<?php if (!empty($permit['signC'])): ?>
													<img src="<?= htmlspecialchars($permit['signC']); ?>" width="200" height="200" style="border: 1px solid #000;" /><br>
													<?php else: ?>
													<canvas id="signC-pad" width="200" height="200" style="border: 1px solid #000;"></canvas><br>
													<?php endif; ?>
													<input type="hidden" name="signC" id="signC" value="<?= htmlspecialchars($permit['signC'] ?? '') ?>">
													<button id="clear-signC">Clear Signature</button>
													<br><label for="nameC">Name:</label>
													<input type="text" name="nameC" class="form-control" value="<?= htmlspecialchars($permit['nameC'] ?? '') ?>">
													<label for="positionC">Position:</label>
													<input type="text" name="positionC" class="form-control" value="<?= htmlspecialchars($permit['positionC'] ?? '') ?>">
													<label for="dateC">Date:</label>
													<input type="date" name="dateC" class="form-control" value="<?= htmlspecialchars($permit['dateC'] ?? '') ?>">
													<label for="timeC">Time:</label>
													<input type="time" name="timeC" class="form-control" value="<?= htmlspecialchars($permit['timeC'] ?? '') ?>">
												</div>
												
												<!-- Area Owner -->
												<div class="col-md-3">
													<h6><u>Area Owner</u></h6>
													<label for="signA">Signature:</label><br>
													<?php if (!empty($permit['signA'])): ?>
													<img src="<?= htmlspecialchars($permit['signA']); ?>" width="200" height="200" style="border: 1px solid #000;" /><br>
													<?php else: ?>
													<canvas id="signA-pad" width="200" height="200" style="border: 1px solid #000;"></canvas><br>
													<?php endif; ?>
													<input type="hidden" name="signA" id="signA" value="<?= htmlspecialchars($permit['signA'] ?? '') ?>">
													<button id="clear-signA">Clear Signature</button>
													<br><label for="nameA">Name:</label>
													<input type="text" name="nameA" class="form-control" value="<?= htmlspecialchars($permit['nameA'] ?? '') ?>">
													<label for="positionA">Position:</label>
													<input type="text" name="positionA" class="form-control" value="<?= htmlspecialchars($permit['positionA'] ?? '') ?>">
													<label for="dateA">Date:</label>
													<input type="date" name="dateA" class="form-control" value="<?= htmlspecialchars($permit['dateA'] ?? '') ?>">
													<label for="timeA">Time:</label>
													<input type="time" name="timeA" class="form-control" value="<?= htmlspecialchars($permit['timeA'] ?? '') ?>">
												</div>
												
												<!-- ICO -->
												<div class="col-md-3">
													<h6><u>ICO</u></h6>
													<label for="signI">Signature:</label><br>
													<?php if (!empty($permit['signI'])): ?>
													<img src="<?= htmlspecialchars($permit['signI']); ?>" width="200" height="200" style="border: 1px solid #000;" /><br>
													<?php else: ?>
													<canvas id="signI-pad" width="200" height="200" style="border: 1px solid #000;"></canvas><br>
													<?php endif; ?>
													<input type="hidden" name="signI" id="signI" value="<?= htmlspecialchars($permit['signI'] ?? '') ?>">
													<button id="clear-signI">Clear Signature</button>
													<br><label for="nameI">Name:</label>
													<input type="text" name="nameI" class="form-control" value="<?= htmlspecialchars($permit['nameI'] ?? '') ?>">
													<label for="positionI">Position:</label>
													<input type="text" name="positionI" class="form-control" value="<?= htmlspecialchars($permit['positionI'] ?? '') ?>">
													<label for="dateI">Date:</label>
													<input type="date" name="dateI" class="form-control" value="<?= htmlspecialchars($permit['dateI'] ?? '') ?>">
													<label for="timeI">Time:</label>
													<input type="time" name="timeI" class="form-control" value="<?= htmlspecialchars($permit['timeI'] ?? '') ?>">
												</div>
												
												<!-- SHO -->
												<div class="col-md-3">
													<h6><u>SHO</u></h6>
													<label for="signS">Signature:</label><br>
													<?php if (!empty($permit['signS'])): ?>
													<img src="<?= htmlspecialchars($permit['signS']); ?>" width="200" height="200" style="border: 1px solid #000;" /><br>
													<?php else: ?>
													<canvas id="signS-pad" width="200" height="200" style="border: 1px solid #000;"></canvas><br>
													<?php endif; ?>
													<input type="hidden" name="signS" id="signS" value="<?= htmlspecialchars($permit['signS'] ?? '') ?>">
													<button id="clear-signS">Clear Signature</button>
													<br><label for="nameS">Name:</label>
													<input type="text" name="nameS" class="form-control" value="<?= htmlspecialchars($permit['nameS'] ?? '') ?>">
													<label for="positionS">Position:</label>
													<input type="text" name="positionS" class="form-control" value="<?= htmlspecialchars($permit['positionS'] ?? '') ?>">
													<label for="dateS">Date:</label>
													<input type="date" name="dateS" class="form-control" value="<?= htmlspecialchars($permit['dateS'] ?? '') ?>">
													<label for="timeS">Time:</label>
													<input type="time" name="timeS" class="form-control" value="<?= htmlspecialchars($permit['timeS'] ?? '') ?>">
												</div>
											</div>
											<br>
											<hr>
											<div class="row mb-3">
												<div class="row mb-3">
													<div class="col-md-4">
														<h4>Status</h4>
														<input type="text" name="status_display" value="<?= htmlspecialchars($ptw['status']); ?>" class="form-control" disabled>
													</div>
													<div class="col-md-4">
														<h4>Closure / Stop Working Order</h4>
														<?php
															$statusOptions = [
															'completed' => 'Complete Work',
															'stop work' => 'Stop Work',
															'cancel' => 'Cancel Work',
															'resume work' => 'Resume Work'
															];
															foreach ($statusOptions as $value => $label):
															$checked = ($ptw['status'] === $value) ? 'checked' : '';
														?>
														<input type="radio" name="status" value="<?= $value ?>" <?= $checked ?> onchange="checkStatus()">
														<label for="<?= $value ?>"><?= $label ?></label><br>
														<?php endforeach; ?>
													</div>
													<div class="col-md-4">
														<h4>Reason of Stop / Cancel / Resume</h4>
														<input type="text" name="remark" id="remark" value="<?= htmlspecialchars($ptw['remark']); ?>" class="form-control">
													</div>
												</div>
											</div>
											<hr>
											<div class="row mb-3">
												<div class="row mb-3" id="file-upload-wrapper">
													<div class="row mb-3">
														<div class="col-md-4">
															<h4 for="file">Upload Files</h4>
															<input type="file" name="files[]" id="file" multiple>
														</div>
													</div>
													<br>
													<hr>
													<br>
													
													<?php if (!empty($permit['file'])): ?>
													<h5>Existing Uploaded Files</h5>
													<div id="existing-file-list">
														<?php 
															$files = explode(",", $permit['file']);
															foreach ($files as $index => $file):
															$file = trim($file);
															$fileName = basename($file);
															$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
															$iconMap = [
															'pdf' => 'fa-file-pdf',
															'png' => 'fa-file-image',	
															'jpg' => 'fa-file-image',
															'jpeg' => 'fa-file-image'
															];
															$iconClass = isset($iconMap[$ext]) ? $iconMap[$ext] : 'fa-file';
														?>
														<div class="file-item" id="file-row-<?= $index ?>">
															<i class="fas <?= $iconClass ?> file-icon"></i>
															<a href="<?= htmlspecialchars($file) ?>" target="_blank" class="file-name">
																<?= htmlspecialchars($fileName) ?>
															</a>
														</div>
														<?php endforeach; ?>
													</div>
													<?php else: ?>
													<p>No files uploaded.</p>
													<?php endif; ?>											
												</div>
												<div class="col-md-4">
													<button type="submit" name="update_form" class="btn btn-primary">
														Update Form
													</button>
												</div>
											</form>
											<?php
											}
											else
											{
												echo "<h4>No Record Found</h4>";
											}
										}
									?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
		<script src="script.js"></script>
		<script>
			document.getElementById('file').addEventListener('change', function () {
				const maxFileSize = 10 * 1024 * 1024; // 10MB in bytes
				const files = this.files;
				const errorContainer = document.getElementById('file-error');
				let errorMessage = '';
				
				for (let i = 0; i < files.length; i++) {
					if (files[i].size > maxFileSize) {
						errorMessage = `File "${files[i].name}" exceeds 10MB limit.`;
						break;
					}
				}
				
				if (errorMessage !== '') {
					errorContainer.textContent = errorMessage;
					this.classList.add('error');
					this.value = ''; // Clear selected files
					} else {
					errorContainer.textContent = ''; // Clear error message
					this.classList.remove('error');
				}
			});
			
			function checkStatus() {
				const status = document.querySelector('input[name="status"]:checked').value;
				const remarkField = document.getElementById('remark');
				if (['cancel', 'stop work', 'resume work'].includes(status)) {
					remarkField.required = true;
					} else {
					remarkField.required = false;
				}
			}
			input.style.display = (checkbox.checked || input.value.trim() !== "") ? "block" : "none";
			function confirmLogout() {
				var confirmation = confirm("Are you sure you want to logout?");
				return confirmation;
			}
		</script>
		<script>
			document.addEventListener("DOMContentLoaded", function () {
				function toggleInput(checkboxId, inputId) {
					const checkbox = document.getElementById(checkboxId);
					const input = document.getElementById(inputId);
					if (checkbox && input) {
						// Initial toggle on page load
						input.style.display = checkbox.checked ? "block" : "none";
						
						// Toggle on change
						checkbox.addEventListener("change", function () {
							input.style.display = checkbox.checked ? "block" : "none";
							if (!checkbox.checked) input.value = "";
						});
					}
				}
				
				toggleInput("infection_other_checkbox", "infection_other_text");
				toggleInput("workType_other_checkbox", "workType_other_text");
				toggleInput("worksite_other_checkbox", "worksite_other_text");
				toggleInput("hazards_other_checkbox", "hazards_other_text");
			});
		</script>
		<script>
			function deleteExistingFile(index, filePath) {
				const fileRow = document.getElementById('file-row-' + index);
				if (fileRow) {
					fileRow.remove();
					
					// Append to hidden deleted_files[] list
					const deletedInput = document.createElement("input");
					deletedInput.type = "hidden";
					deletedInput.name = "deleted_files[]";
					deletedInput.value = filePath;
					document.querySelector('form').appendChild(deletedInput);
				}
			}
		</script>
		<script>
			document.addEventListener("DOMContentLoaded", function () {
				const wrapper = document.getElementById('file-upload-wrapper');
				const fileListDisplay = document.getElementById('selected-files-list');
				const addMoreBtn = document.getElementById('add-more-files');
				
				const iconMap = {
					pdf: 'fa-file-pdf',
					jpg: 'fa-file-image',
					jpeg: 'fa-file-image',
					png: 'fa-file-image'
				};
				
				// Add file preview on input change
				wrapper.addEventListener('change', function (e) {
					if (e.target.classList.contains('file-input')) {
						const file = e.target.files[0];
						if (file) {
							const ext = file.name.split('.').pop().toLowerCase();
							const icon = iconMap[ext] || 'fa-file';
							
							// Add preview
							const li = document.createElement('li');
							li.className = "list-group-item d-flex justify-content-between align-items-center mt-1";
							li.innerHTML = `
							<div class="d-flex align-items-center">
						<i class="fas ${icon} fa-lg me-2"></i>
						<span>${file.name}</span>
						<span class="badge bg-secondary ms-2">${Math.round(file.size / 1024)} KB</span>
					</div>
					<button type="button" class="btn btn-sm btn-danger remove-file-btn">
						<i class="fas fa-trash-alt"></i>
					</button>
					`;
					
					fileListDisplay.appendChild(li);
					fileListDisplay.parentElement.style.display = 'block';
					}
					}
					});
					
					// Add more file inputs
					addMoreBtn.addEventListener('click', function () {
					const newInputGroup = document.createElement('div');
					newInputGroup.className = 'col-md-6 mb-2 file-upload-group';
					newInputGroup.innerHTML = `
					<input type="file" name="files[]" class="form-control file-input" accept=".pdf,.jpg,.jpeg,.png">
					`;
					wrapper.insertBefore(newInputGroup, addMoreBtn.closest('.col-md-6'));
					});
					
					// Just remove preview (not input)
					fileListDisplay.addEventListener('click', function (e) {
					const removeBtn = e.target.closest('.remove-file-btn');
					if (removeBtn) {
					const li = removeBtn.closest('li');
					if (li) li.remove();
					
					if (!fileListDisplay.hasChildNodes()) {
					fileListDisplay.parentElement.style.display = 'none';
					}
					}
					});
					
					// Initial hide if empty
					if (!fileListDisplay.hasChildNodes()) {
					fileListDisplay.parentElement.style.display = 'none';
					}
					});
				</script>	
			</body>
		</html>																												