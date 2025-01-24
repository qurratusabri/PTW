<?php
require 'dbconn.php';
session_start();

// Handle new service submission 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['newService'])) 
{
     $newService = $conn->real_escape_string($_POST['newService']);
      // Save new service to the database if (!empty($newService)) 
      { $stmt = $conn->prepare("INSERT INTO services (serviceName) VALUES (?)"); 
        $stmt->bind_param("s", $newService); $stmt->execute(); $stmt->close(); 
    }
 } 
 // Fetch services 
 $services = $conn->query("SELECT serviceName FROM services");
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
    <!--css -->
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" type="x-icon" href="helmet.png">

    <title>Form</title>
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
    <div class="container mt-5">
        <?php include('message.php'); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Project Add 
                            <a href="appdb.php" class="btn btn-primary float-end">BACK</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="code.php" method="POST" onsubmit="return validateForm()">
                        <div class="row mb-4">
                        <h4>KPJ KLANG SPECIALIST HOSPITAL (Project Manager / Coordinator)</h4>
                            <div class="col-md-4">
                                <label>Applicant's Name:</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-4"> 
                                <label for="services">Services:</label> 
                                <select name="services" class="form-select" required> 
                                    <?php while ($row = $services->fetch_assoc()) { ?> 
                                        <option value="<?php echo $row['serviceName']; ?>"><?php echo $row['serviceName']; ?></option> 
                                        <?php } ?> 
                                    </select> 
                            </div>
                            </div>
                            <div class="row mb-4">
                                <h6>Work Duration (date):</h6>
                                <div class="col-md-4">
                                <label for="durationFrom">From:</label>
                                <input type="date" name="durationFrom" class="form-control" required></div>
                                <div class="col-md-4">
                                <label for="durationTo">To:</label>
                                <input type="date" name="durationTo" class="form-control" required>
                            </div>
                            </div>
                            <div class="row mb-4">
                                <h6>Work Time:</h6>
                                <div class="col-md-4">
                                <label for="timeFrom">From:</label>
                                <input type="time" name="timeFrom" class="form-control" required></div>
                                <div class="col-md-4">
                                <label for="timeTo">To:</label>
                                <input type="time" name="timeTo" class="form-control" required>
                            </div>
                            </div>
                            <div class="row mb-3">
                                <h4>CONTRACTOR</h4>
                                <div class="col-md-4">
                                <label for="companyName">Company Name:</label>
                                <input type="text" name="companyName" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                            <label for="svName">Supervisor Name:</label>
                                <input type="text" name="svName" class="form-control" required>  
                            </div>
                            <div class="col-md-4">
                            <label for="icNo">IC No./Passport No:</label>
                                <input type="number" name="icNo" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                            <label for="contactNo">Contact No.:</label>
                                <input type="tel" name="contactNo" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                            <label for="longTermContract">Term of Contract:</label>
                                <input type="text" name="longTermContract" class="form-control" required>
                            </div>
                            </div>
                            <div class="row md-4">
                            <div class="col-md-6">
                            <h4>Contractor Worker's Names:-</h4>
                                <table id="workersTable">
                                    <tr>
                                        <td>1</td>
                                        <td><input type="text" name="workersName[]" class="form-control" placeholder="Worker's Name" required></td>
                                        <td><input type="text" name="passNo[]" class="form-control" placeholder="IC No./Passport No." required></td>
                                        <td><button type="button" class="removeWorkerButton">Remove</button></td>  
                                    </tr>
                                </table>
                                <button type="button" id="addWorkerButton">Add Worker</button>  
                                </div>
                                <div class="col-md-4">
                                <h4>AREA / LOCATION OF WORK</h4>
                                <label for="exactLocation">Exact Location of Work:</label>
                                <input type="text" name="exactLocation" class="form-control" required>
                            </div>
                            </div>
                            <div class="row md-4">
                        
                                <div class="col-md-4">
                                    <h4>TYPE OF WORK</h4>
                                    <h6>Select Type(s) of Work:</h6>
                                    <input type="checkbox" name="workType[]" value="Aircond / Chiller">
                                    <label for="aircond">Aircond / Chiller</label><br>
                                    <input type="checkbox" name="workType[]" value="Pest Control">
                                    <label for="pc">Pest Control</label><br>
                                    <input type="checkbox" name="workType[]" value="Civil / Structural">
                                    <label for="cs">Civil / Structural</label><br>
                                    <input type="checkbox" name="workType[]" value="Roofing">
                                    <label for="roof">Roofing</label><br>
                                    <input type="checkbox" name="workType[]" value="Sewage">
                                    <label for="sewage">Sewage</label><br>
                                    <input type="checkbox" name="workType[]" value="Furniture">
                                    <label for="furniture">Furniture</label><br>
                                    <input type="checkbox" name="workType[]" value="Painting (internal / external)">
                                    <label for="painting">Painting (internal / external)</label><br>
                                    <input type="checkbox" name="workType[]" value="Flooring">
                                    <label for="flooring">Flooring</label><br>
                                    <input type="checkbox" name="workType[]" value="Wiring">
                                    <label for="wiring">Wiring</label><br>
                                    <input type="checkbox" name="workType[]" value="Electrical">
                                    <label for="electrical">Electrical</label><br>
                                    <input type="checkbox" name="workType[]" value="Plumbing">
                                    <label for="plum">Plumbing</label><br>
                                    <input type="checkbox" name="workType[]" value="Cabling">
                                    <label for="cable">Cabling</label><br>
                                    <input type="checkbox" name="workType[]" value="Maintenance">
                                    <label for="maintain">Maintenance</label><br>
                                    <input type="checkbox" name="workType[]" value="HEPA filter Servicing">
                                    <label for="hepa">HEPA filter Servicing</label><br>
                                    <input type="checkbox" name="workType[]" value="High Dusting">
                                    <label for="hd">High Dusting</label><br>
                                    <input type="checkbox" name="workType[]" value="Exterior facade cleaning">
                                    <label for="efc">Exterior facade cleaning</label><br>
                                    <input type="checkbox" name="workType[]" value="Renovation">
                                    <label for="renovate">Renovation</label><br>
                                    <input type="checkbox" name="workType[]" value="PPM">
                                    <label for="ppm">PPM</label><br>
                                    <input type="checkbox" name="workType[]" value="Corrective Maintenance">
                                    <label for="cm">Corrective Maintenance</label><br>
                                    <input type="checkbox" name="workType[]" value="Equipment breakdown">
                                    <label for="eb">Equipment breakdown</label><br>
                                    <label for="others">Others:</label><br>
                                    <input type="textbox" name="workType[]" class="form-control">
                                </div>
                            <div class="col-md-4">
                            <h4>WORKSITE PREPARATION / PRECAUTIONS</h4>
                                <h6>Select:</h6>
                                <input type="checkbox" name="worksite[]" value="Site prepared as informed">
                                <label for="site">Site prepared as informed</label><br>
                                <input type="checkbox" name="worksite[]" value="Scaffold Required">
                                <label for="scaffold">Scaffold Required</label><br>
                                <input type="checkbox" name="worksite[]" value="Toxic Fumes Detector">
                                <label for="toxic">Toxic Fumes Detector</label><br>
                                <input type="checkbox" name="worksite[]" value="PMA / PMT e.g crane">
                                <label for="pma">PMA / PMT e.g crane</label><br>
                                <input type="checkbox" name="worksite[]" value="Gas Detector">
                                <label for="gas">Gas Detector</label><br>
                                <input type="checkbox" name="worksite[]" value="Forced Ventilation">
                                <label for="forced">Forced Ventilation</label><br>
                                <input type="checkbox" name="worksite[]" value="Equipment Isolated">
                                <label for="equipment">Equipment Isolated</label><br>
                                <input type="checkbox" name="worksite[]" value="LOTO">
                                <label for="loto">LOTO</label><br>
                                <input type="checkbox" name="worksite[]" value="Additional Fire Extinguisher / blanket">
                                <label for="additional">Additional Fire Extinguisher / blanket</label><br>
                                <input type="checkbox" name="worksite[]" value="Equipment / Line Drained / Blinded">
                                <label for="blinded">Equipment / Line Drained / Blinded</label><br>
                                <input type="checkbox" name="worksite[]" value="Area Barricaded / Signed">
                                <label for="area">Area Barricaded / Signed</label><br>
                                <input type="checkbox" name="worksite[]" value="Confined Space">
                                <label for="confined">Confined Space</label><br>
                                <input type="checkbox" name="worksite[]" value="Secure Tools from Falling">
                                <label for="secure">Secure Tools from Falling</label><br>
                                <input type="checkbox" name="worksite[]" value="Noise / Dust Insulation">
                                <label for="noise">Noise / Dust Insulation</label><br>
                                <input type="checkbox" name="worksite[]" value="Ladder / Step Stool">
                                <label for="ladder">Ladder / Step Stool</label><br>
                                <input type="checkbox" name="worksite[]" value="Spillage Kits">
                                <label for="spillage">Spillage Kits</label><br>
                                <input type="checkbox" name="worksite[]" value="Inform Workers In and the Next Area">
                                <label for="inform">Inform Workers In and the Next Area</label><br>
                                <input type="checkbox" name="worksite[]" value="Hot work">
                                <label for="hot">Hot work</label><br>
                                <label for="others">If Others please state:</label><br>
                                <input type="textbox" name="worksite[]" class="form-control">
                            </div>
                            <div class="col-md-4">
                            <h4>PERSONAL PROTECTIVE EQUIPMENTS</h4>
                                    <h6>Select PPE:</h6>
                                    <input type="checkbox" name="ppe[]" value="Safety Helmet">
                                    <label for="helmet">Safety Helmet</label><br>
                                    <input type="checkbox" name="ppe[]" value="Face Shield">
                                    <label for="fc">Safe Shield</label><br>
                                    <input type="checkbox" name="ppe[]" value="Welding Mask">
                                    <label for="wm">Welding Mask</label><br>
                                    <input type="checkbox" name="ppe[]" value="Safety Shoes">
                                    <label for="shoes">Safety Shoes</label><br>
                                    <input type="checkbox" name="ppe[]" value="Chemical Boots">
                                    <label for="boots">Chemical Boots</label><br>
                                    <input type="checkbox" name="ppe[]" value="Leather Gloves">
                                    <label for="lg">Leather Gloves</label><br>
                                    <input type="checkbox" name="ppe[]" value="Safety Googles">
                                    <label for="goggles">Safety Goggles</label><br>
                                    <input type="checkbox" name="ppe[]" value="Canvas">
                                    <label for="canvas">Canvas</label><br>
                                    <input type="checkbox" name="ppe[]" value="Gloves">
                                    <label for="gloves">Gloves</label><br>
                                    <input type="checkbox" name="ppe[]" value="Full Body Harness">
                                    <label for="fbh">Full Body Harness</label><br>
                                    <input type="checkbox" name="ppe[]" value="Ear plug/ear muff">
                                    <label for="ear">Ear plug/ear muff</label><br>
                                    <h6>Respirator:</h6>
                                    <input type="checkbox" name="ppe[]" value="Dusk Mask">
                                    <label for="dusk">Dusk Mask</label><br>
                                    <input type="checkbox" name="ppe[]" value="Fumes Mask">
                                    <label for="fumes">Fumes Mask</label><br>
                                    <input type="checkbox" name="ppe[]" value="Painting Mask">
                                    <label for="painting">Painting Mask</label><br>
                                </div>
                                <p>
                                <div class="col-md-4">
                                    <button type="submit" name="save_form" class="btn btn-primary">Submit Form</button>
                                </div>
                                </p>
                                </div>
                            </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="script.js"></script>
    <script>
        const workersTable = document.getElementById('workersTable').getElementsByTagName('tbody')[0];
        const addWorkerButton = document.getElementById('addWorkerButton');

        // Function to update row numbers
        function updateRowNumbers() {
        const rows = workersTable.rows;
        for (let i = 0; i < rows.length; i++) {
            rows[i].cells[0].textContent = i + 1; // Update the No. column
        }
        }
        // Add Worker Button Logic
        addWorkerButton.addEventListener('click', function () {
            // Get the current row count
            const rowCount = workersTable.rows.length;

            // Create a new row
            const newRow = workersTable.insertRow();

            // Create cells
            const cell1 = newRow.insertCell(0); // Row Number
            const cell2 = newRow.insertCell(1); // Worker's Name Input
            const cell3 = newRow.insertCell(2); // Pass No Input
            const cell4 = newRow.insertCell(3); // Action Buttons

            // Populate the cells
            cell1.textContent = rowCount + 1;
            cell2.innerHTML = '<input type="text" name="workersName[]" class="form-control">';
            cell3.innerHTML = '<input type="text" name="passNo[]" class="form-control">';
            cell4.innerHTML = '<button type="button" class="removeWorkerButton">Remove</button>';

            // Add event listener for the Remove button
            const removeButton = cell4.querySelector('.removeWorkerButton');
            removeButton.addEventListener('click', function () {
                workersTable.deleteRow(newRow.rowIndex - 1);
                updateRowNumbers(); // Update the row numbers after deletion
            });
        });

        // Initial Remove Button Logic
        document.querySelectorAll('.removeWorkerButton').forEach(button => {
            button.addEventListener('click', function () {
                const row = button.closest('tr');
                workersTable.deleteRow(row.rowIndex - 1);
                updateRowNumbers(); // Update the row numbers after deletion
            });
        });
    
        function validateForm() {
            let workTypeChecked = false;
            let worksiteChecked = false;
            let ppeChecked = false;

            let workTypeCheckboxes = document.querySelectorAll('input[name="workType[]"]');
            let worksiteCheckboxes = document.querySelectorAll('input[name="worksite[]"]');
            let ppeCheckboxes = document.querySelectorAll('input[name="ppe[]"]');

            for (let i = 0; i < workTypeCheckboxes.length; i++) {
                if (workTypeCheckboxes[i].checked) {
                    workTypeChecked = true;
                    break;
                }
            }

            for (let i = 0; i < worksiteCheckboxes.length; i++) {
                if (worksiteCheckboxes[i].checked) {
                    worksiteChecked = true;
                    break;
                }
            }

            for (let i = 0; i < ppeCheckboxes.length; i++) {
                if (ppeCheckboxes[i].checked) {
                    ppeChecked = true;
                    break;
                }
            }

            if (!workTypeChecked) {
                alert("Please select at least one type of work.");
                return false;
            }
            
            if (!worksiteChecked) {
                alert("Please select at least one worksite preparation/precaution.");
                return false;
            }
            
            if (!ppeChecked) {
                alert("Please select at least one personal protective equipment.");
                return false;
            }

            return true;
        }
        function confirmLogout() {
        var confirmation = confirm("Are you sure you want to logout?");
        return confirmation;
    }
</script>
</body>
</html>