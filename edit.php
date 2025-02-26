<?php
session_start();
require 'dbconn.php';
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
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" type="x-icon" href="helmet.png">
    <title>Edit Form</title>
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

                                <form action="code.php" method="POST">
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
                                        <label for="others">Others:</label><br>
                                        <input type="textbox" name="workType[]" class="form-control" <?php echo in_array('others', $workTypeArray) ? 'checked' : ''; ?>>
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
                                        <label for="others">If Others please state:</label><br>
                                        <input type="textbox" name="worksite[]" class="form-control" <?php echo in_array('others', $worksiteArray) ? 'checked' : ''; ?>>
                                        <!-- Add more options similarly -->
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
                                            <!-- Add more options similarly -->
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
                                <label for="others">Others:</label><br>
                                <input type="textbox" name="infection[]" class="form-control" <?php echo in_array('others', $infectionArray) ? 'checked' : ''; ?>>
                                <!-- Add more options similarly -->
                            </div>
                                </div>
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
                                    <div class="row mb-3">
                                    <h4>Permit Authorisation Section</h4>
                                    <div class="col-md-3">
                                    <h6><u>Contractor</u></h6>
                                    <label for="signC">Signature:</label>
                                    <br><canvas id="signC-pad" width="200" height="200" style="border: 1px solid #000;"></canvas></br>
                                    <input type="hidden" name="signC" id="signC">
                                    <button id="clear-signC">Clear Signature</button> <!-- Clear button -->
                                    <br><label for="nameC">Name:</label></br>
                                    <input type="text" name="nameC" class="form-control">
                                    <label for="positionC">Position:</label>
                                    <input type="text" name="positionC" class="form-control">
                                    <label for="dateC">Date:</label>
                                    <input type="date" name="dateC" class="form-control">
                                    <label for="timeC">Time:</label>
                                    <input type="time" name="timeC" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                    <h6><u>Area Owner</u></h6>
                                    <label for="signA">Signature:</label>
                                    <br><canvas id="signA-pad" width="200" height="200" style="border: 1px solid #000;"></canvas></br>
                                    <input type="hidden" name="signA" id="signA">
                                    <button id="clear-signA">Clear Signature</button> <!-- Clear button -->
                                    <br><label for="nameA">Name:</label></br>
                                    <input type="text" name="nameA" class="form-control">
                                    <label for="positionA">Position:</label>
                                    <input type="text" name="positionA" class="form-control">
                                    <label for="dateA">Date:</label>
                                    <input type="date" name="dateA" class="form-control">
                                    <label for="timeA">Time:</label>
                                    <input type="time" name="timeA" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                    <h6><u>ICO</u></h6>
                                    <label for="signI">Signature:</label>
                                    <br><canvas id="signI-pad" width="200" height="200" style="border: 1px solid #000;"></canvas></br>
                                    <input type="hidden" name="signI" id="signI" >
                                    <button id="clear-signI">Clear Signature</button> <!-- Clear button -->
                                    <br><label for="nameI">Name:</label></br>
                                    <input type="text" name="nameI" class="form-control">
                                    <label for="positionI">Position:</label>
                                    <input type="text" name="positionI" class="form-control">
                                    <label for="dateI">Date:</label>
                                    <input type="date" name="dateI" class="form-control">
                                    <label for="timeI">Time:</label>
                                    <input type="time" name="timeI" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                    <h6><u>SHO</u></h6>
                                    <label for="signS">Signature:</label>
                                    <br><canvas id="signS-pad" width="200" height="200" style="border: 1px solid #000;"></canvas></br>
                                    <input type="hidden" name="signS" id="signS">
                                    <button id="clear-signS">Clear Signature</button> <!-- Clear button -->
                                    <br><label for="nameS">Name:</label></br>
                                    <input type="text" name="nameS" class="form-control">
                                    <label for="positionS">Position:</label>
                                    <input type="text" name="positionS" class="form-control">
                                    <label for="dateS">Date:</label>
                                    <input type="date" name="dateS" class="form-control">
                                    <label for="timeS">Time:</label>
                                    <input type="time" name="timeS" class="form-control">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                <div class="col-md-4">
                                <h4>Status</h4>
                                <input type="text" name="status" value="<?=$ptw['status'];?>" class="form-control" disabled>
                            </div>
                            <div class="col-md-4">
                                <h4>Closure / Stop Working Order</h4>
                                <input type="radio" name="status" value="completed" onchange="checkStatus()">
                                <label for="completed">Complete Work</label>
                                <input type="radio" name="status" value="stop work" onchange="checkStatus()">
                                <label for="stopWork">Stop Work</label>
                                <input type="radio" name="status" value="cancel" onchange="checkStatus()">
                                <label for="cancel">Cancel Work</label>
                            </div>
                            <div class="col-md-4">
                                <h4>Reason of Stop work / Cancel work</h4>
                                <input type="text" name="remark" id="remark" value="<?=$ptw['remark'];?>" class="form-control">
                            </div>
                            <script>
                            function checkStatus() {
                                var status = document.querySelector('input[name="status"]:checked').value;
                                var remarkField = document.getElementById('remark');
                                
                                if (status === 'cancel' || status === 'stop work') {
                                    remarkField.required = true;
                                } else {
                                    remarkField.required = false;
                                }
                            }
                            </script>
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
            function confirmLogout() {
            var confirmation = confirm("Are you sure you want to logout?");
            return confirmation;
        }
            </script>
</body>
</html>