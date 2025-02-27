<?php
require 'dbconn.php';

if (isset($_GET['id'])) {
    $applicantID = (int)$_GET['id'];
    $query_form = "SELECT * FROM form WHERE id='$applicantID'";
    $query_run_form = mysqli_query($conn, $query_form);

    if ($query_run_form && mysqli_num_rows($query_run_form) > 0) {
        $ptw = mysqli_fetch_array($query_run_form);

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

        //query to fetch signature data
        $query_permit = "SELECT * FROM permit WHERE id='$applicantID'";
        $query_run_permit = mysqli_query($conn, $query_permit);

        //check if siganture data exists
        $permit = mysqli_fetch_assoc($query_run_permit);    } else {
        echo "<h4>No Data Found for the specified ID.</h4>";
        exit;
    }
}

// Initialize $selectedHazards
$selectedHazards = [];

// Fetch hazards related to the form
$hazardQuery = "SELECT hazards FROM form WHERE id = ?";
$stmt = $conn->prepare($hazardQuery);

if (!$stmt) {
    die("Error preparing statement: " . $conn->error); // Debug error
}

$stmt->bind_param("i", $applicantID);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $selectedHazards[] = $row['hazards'];
}
$stmt->close();

// Fetch selected work types from the database
$selectedWorkTypes = [];
$query = "SELECT workType FROM form WHERE id = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Error preparing statement: " . $conn->error); // Debug error
}

$stmt->bind_param("i", $applicantID);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $selectedWorkTypes[] = $row['workType'];
}
$stmt->close();
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

    <title>View Details</title>
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
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Form View Details 
                            <a href="dashboard.php" class="btn btn-primary float-end">BACK</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <h4>KPJ KLANG SPECIALIST HOSPITAL (Project Manager / Coordinator)</h4>
                            <div class="col-md-4">
                                <label>Applicant's Name :</label>
                                <?=$ptw['name'];?>
                            </div>
                            <div class="col-md-4">
                                <label>Services :</label>
                                <?=$ptw['services'];?>
                            </div>
                            <div class="col-md-4">
                                <label>Serial No. :</label>
                                <?=$ptw['id'];?>
                            </div>
                            </div>
                            <div class="row mb-4">
                                <h6>Work Duration:</h6>
                                <div class="col-md-4">
                                <label>From :</label>
                                <?=$ptw['durationFrom'];?>
                                </div>
                                <div class="col-md-4">
                                <label>To :</label>
                                <?=$ptw['durationTo'];?>
                            </div>
                            </div>
                            <div class="row mb-4">
                                <h6>Work Time:</h6>
                                <div class="col-md-4">
                                <label>From :</label>
                                <?=$ptw['timeFrom'];?>
                                </div>
                                <div class="col-md-4">
                                <label>To :</label>
                                <?=$ptw['timeTo'];?>
                            </div>
                            </div>
                            <hr>
                            <div class="row mb-3">
                            <h4>CONTRACTOR</h4>
                            <div class="col-md-4">
                                <label>Company Name :</label>
                                <?=$ptw['companyName'];?>
                            </div>
                            <div class="col-md-4">
                                <label>Supervisor Name :</label>
                                <?=$ptw['svName'];?>
                            </div>
                            <div class="col-md-4">
                                <label>IC No./Passport No :</label>
                                <?=$ptw['icNo'];?>
                            </div>
                            <div class="col-md-4">
                                <label>Contact No. :</label>
                                <?=$ptw['contactNo'];?>
                            </div>
                            <div class="col-md-4">
                                <label>Term of Contract :</label>
                                <?=$ptw['longTermContract'];?>
                            </div>
                            </div>
                            <hr>
                            <div class="row mb-3">
                            <div class="col-md-4">
                                <h4>Contractor Worker's Names</h4>
                                <table class="table table-bordered table-striped"> 
                                <thead> 
                                    <tr> 
                                        <th>No.</th> 
                                        <th>Worker Name</th> 
                                        <th>IC No./Passport No.</th> 
                                    </tr> 
                                </thead> 
                                <tbody> 
                                <?php if (!empty($workerNamesArray)): ?> 
                                    <?php foreach ($workerNamesArray as $index => $workerName): ?> 
                                        <tr> 
                                            <td><?= $index + 1; ?></td> 
                                            <td><p><?= htmlspecialchars($workerName); ?></p></td> 
                                            <td><p><?= !empty($passNoArray[$index]) ? htmlspecialchars($passNoArray[$index]) : 'N/A'; ?></p></td> 
                                        </tr> 
                                    <?php endforeach; ?> 
                                <?php else: ?> 
                                    <tr> 
                                        <td colspan="3">No Workers Found</td> 
                                    </tr> 
                                <?php endif; ?> 
                                </tbody> 
                            </table>
                                        </p>
                                            </div>
                                        <div class="col-md-4">
                                            <h4>TYPE OF WORK</h4>
                                                    <p class="check-control">
                                                    <?=$ptw['workType'];?>
                                                </p>
                                        </div>
                                        <div class="col-md-4">
                                            <h4>AREA / LOCATION OF WORK</h4>
                                            <label>Exact Location of Work :</label>
                                                <?=$ptw['exactLocation'];?>
                                        </div>
                                            </div>
                                            <hr>
                                        <table class="invisible-table">
                                            <tr>
                                                <th>
                                                    <h4>WORKSITE PREPARATION / PRECAUTIONS</h4>
                                                </th>
                                                <th>
                                                    <h4>PERSONAL PROTECTIVE EQUIPMENTS</h4>
                                                </th>
                                                <th>
                                                    <h4>HAZARDS ANALYSIS</h4>
                                                </th>
                                                <th>
                                                    <h4>INFECTION CONTROL</h4>
                                                </th>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p class="check-control">
                                                        <?=$ptw['worksite'];?>
                                                    </p>
                                                </td>
                                                <td>
                                                    <p class="check-control">
                                                        <?=$ptw['ppe'];?>
                                                    </p>
                                                </td>
                                                <td>
                                                    <p class="check-control">
                                                        <?=$ptw['hazards'];?>
                                                    </p>
                                                </td>
                                                <td>
                                                    <p class="check-control">
                                                        <?=$ptw['infection'];?>
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                        <hr>
                                        <div class="row mb-3">
                                        <div class="row mb-3">
                                        <h4>SAFETY BRIEFING RECORD</h4>
                                        <div class="col-md-4">
                                            <label>Date :</label>
                                            <?= !empty($ptw['briefDate']) ? $ptw['briefDate'] : 'No safety briefing conducted yet'; ?>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Time :</label>
                                            <?= !empty($ptw['briefTime']) ? $ptw['briefTime'] : 'No safety briefing conducted yet'; ?>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Conducted by :</label>
                                            <?= !empty($ptw['briefConducted']) ? $ptw['briefConducted'] : 'No safety briefing conducted yet'; ?>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row mb-3">
                                    <?php
                                    $status = (!empty($ptw['briefDate']) && !empty($ptw['briefTime']) && !empty($ptw['briefConducted'])) ? 'in progress' : 'pending';
                                    ?>
                                    <?php if ($permit): ?> 
                                        <h4>Permit Authorisation Section</h4>
                                    <div class="col-md-3">
                                        <h6><u>Contractor</u></h6>
                                        <label>Signature :</label>
                                        <div class="card-body">
                                        <?php if (!empty($permit['signC'])): ?>
                                            <img src="<?= strpos($permit['signC'], 'data:image/png;base64,') === 0 ? $permit['signC'] : 'data:image/png;base64,' . $permit['signC']; ?>" alt="signC" class="img-fluid">
                                                                            <?php else: ?>
                                                <p>No signature found.</p>
                                            <?php endif; ?>
                                        </div>
                                        <p><label>Name :</label>
                                        <?= $permit['nameC']; ?></p>
                                        <p><label>Position :</label>
                                        <?= $permit['positionC']; ?></p>
                                        <p><label>Date :</label>
                                        <?= $permit['dateC']; ?></p>
                                        <p><label>Time :</label>
                                        <?= $permit['timeC']; ?></p>
                                    </div>
                                    <div class="col-md-3">
                                    <h6><u>Area Owner</u></h6>
                                    <label>Signature :</label>
                                    <div class="card-body">
                                        <?php if (!empty($permit['signA'])): ?>
                                            <img src="<?= strpos($permit['signA'], 'data:image/png;base64,') === 0 ? $permit['signA'] : 'data:image/png;base64,' . $permit['signA']; ?>" alt="signA" class="img-fluid">
                                                                            <?php else: ?>
                                                <p>No signature found.</p>
                                            <?php endif; ?>
                                        </div>                                    
                                        <p><label>Name :</label>
                                    <?=$permit['nameA'];?></p>
                                    <p><label>Position :</label>
                                    <?=$permit['positionA'];?></p>
                                    <p><label>Date :</label>
                                    <?=$permit['dateA'];?></p>
                                    <p><label>Time :</label>
                                    <?=$permit['timeA'];?></p>
                                    </div>
                                    <div class="col-md-3">
                                    <h6><u>ICO</u></h6>
                                    <label>Signature :</label>
                                    <div class="card-body">
                                        <?php if (!empty($permit['signI'])): ?>
                                            <img src="<?= strpos($permit['signI'], 'data:image/png;base64,') === 0 ? $permit['signI'] : 'data:image/png;base64,' . $permit['signI']; ?>" alt="signI" class="img-fluid">
                                                                            <?php else: ?>
                                                <p>No signature found.</p>
                                            <?php endif; ?>
                                        </div>
                                        <p><label>Name :</label>
                                    <?=$permit['nameI'];?></p>
                                    <p><label>Position :</label>
                                    <?=$permit['positionI'];?></p>
                                    <p><label>Date :</label>
                                    <?=$permit['dateI'];?></p>
                                    <p><label>Time :</label>
                                    <?=$permit['timeI'];?></p>
                                    </div>

                                    <div class="col-md-3">
                                    <h6><u>SHO</u></h6>
                                    <label>Signature :</label>
                                    <div class="card-body">
                                        <?php if (!empty($permit['signS'])): ?>
                                            <img src="<?= strpos($permit['signS'], 'data:image/png;base64,') === 0 ? $permit['signS'] : 'data:image/png;base64,' . $permit['signS']; ?>" alt="signC" class="img-fluid">
                                                   <?php else: ?>
                                                <p>No signature found.</p>
                                            <?php endif; ?>
                                        </div>                                    
                                        <p><label>Name :</label>
                                    <?=$permit['nameS'];?></p>
                                    <p><label>Position :</label>
                                    <?=$permit['positionS'];?></p>
                                    <p><label>Date :</label>
                                    <?=$permit['dateS'];?></p>
                                    <p><label>Time :</label>
                                    <?=$permit['timeS'];?></p>
                                    </div>
                            <?php else: ?> 
                                <p>No signature found for this form.</p> 
                                <?php endif; ?>
                            </div>
                            <hr>
                            <div class="col-md-3">
                                <h4>Remark</h4>
                                <?php
                                // Set default value to "no remarks" if remark is empty
                                $remark = !empty($ptw['remark']) ? $ptw['remark'] : 'no remarks';
                                echo $remark;
                                ?>
                            </div>
                            <hr>
                            <h4>Uploaded Files:</h4>
                            <div class="card-body">
                                <?php if (!empty($permit['file'])): ?>
                                    <div style="display: flex; flex-wrap: wrap; gap: 15px;">
                                        <?php 
                                            $filePaths = explode(",", $permit['file']); // Convert to array

                                            // Separate images and PDFs
                                            $imageFiles = [];
                                            $pdfFiles = [];

                                            foreach ($filePaths as $filePath) {
                                                $filePath = trim($filePath);
                                                $fileExt = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

                                                if (in_array($fileExt, ['jpg', 'jpeg', 'png'])) {
                                                    $imageFiles[] = $filePath;
                                                } elseif ($fileExt === 'pdf') {
                                                    $pdfFiles[] = $filePath;
                                                }
                                            }
                                        ?>

                                        <!-- Display Images Side by Side -->
                                        <?php if (!empty($imageFiles)): ?>
                                            <div style="display: flex; flex-wrap: wrap; gap: 15px;">
                                                <?php foreach ($imageFiles as $imagePath): ?>
                                                    <?php $imageName = basename($imagePath); ?>
                                                    <div style="display: flex; flex-direction: column; align-items: center; gap: 5px;">
                                                        <div style="display: flex; justify-content: center; align-items: center; width: 300px; height: 200px; overflow: hidden; border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                                                            <img src="<?= $imagePath ?>" alt="Uploaded Image" style="max-width: 100%; max-height: 100%; object-fit: cover;">
                                                        </div>
                                                        <a href="<?= $imagePath ?>" download="<?= $imageName ?>" style="text-decoration: none; color: #007bff; font-weight: bold;">Download Image</a>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Display PDFs in a Column -->
                                        <?php if (!empty($pdfFiles)): ?>
                                            <div style="display: flex; flex-direction: column; gap: 10px; width: 100%; margin-top: 15px;">
                                                <?php foreach ($pdfFiles as $pdfPath): ?>
                                                    <?php $pdfName = basename($pdfPath); ?>
                                                    <a href="<?= $pdfPath ?>" target="_blank" style="display: block; text-decoration: none; color: #007bff; font-weight: bold;"><?= $pdfName ?></a>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <p>No file uploaded.</p>
                                <?php endif; ?>
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