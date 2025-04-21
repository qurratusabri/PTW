<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require 'dbconn.php';
if(isset($_POST['delete_form'])) {
    $applicantID = mysqli_real_escape_string($conn, $_POST['delete_form']);

    // Start transaction
    mysqli_begin_transaction($conn);
    try {
        $query_select = "SELECT file FROM permit WHERE id='$applicantID'";
        $result_select = mysqli_query($conn, $query_select);
        $row = mysqli_fetch_assoc($result_select);
        $filePath = $row['file'];

        $query_permit = "DELETE FROM permit WHERE id='$applicantID'";
        $query_run_permit = mysqli_query($conn, $query_permit);
        if (!$query_run_permit) {
            throw new Exception('Signature deletion failed: ' . mysqli_error($conn));
        }

        $query_form = "DELETE FROM form WHERE id='$applicantID'";
        $query_run_form = mysqli_query($conn, $query_form);
        if (!$query_run_form) {
            throw new Exception('Form deletion failed: ' . mysqli_error($conn));
        }

        if (!empty($filePath) && file_exists($filePath)) {
            unlink($filePath); // Delete file
        }

        // Commit transaction
        mysqli_commit($conn);
        header("Location: dashboard.php");
        exit(0);
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($conn);
        header("Location: dashboard.php");
        exit(0);
    }
}


if (isset($_POST['update_form'])) {
    $applicantID = mysqli_real_escape_string($conn, $_POST['applicantID']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $services = mysqli_real_escape_string($conn, $_POST['services']);
	$status = isset($_POST['status']) ? mysqli_real_escape_string($conn, $_POST['status']) : 'pending';
    //$status = mysqli_real_escape_string($conn, $_POST['status']);
    $remark = mysqli_real_escape_string($conn, $_POST['remark']); 
    $durationFrom = mysqli_real_escape_string($conn, $_POST['durationFrom']);
    $durationTo = mysqli_real_escape_string($conn, $_POST['durationTo']);
    $timeFrom = mysqli_real_escape_string($conn, $_POST['timeFrom']);
    $timeTo = mysqli_real_escape_string($conn, $_POST['timeTo']);
    $companyName = mysqli_real_escape_string($conn, $_POST['companyName']);
    $icNo = mysqli_real_escape_string($conn, $_POST['icNo']);
    $contactNo = mysqli_real_escape_string($conn, $_POST['contactNo']);
    $longTermContract = mysqli_real_escape_string($conn, $_POST['longTermContract']);
    $workersNames = isset($_POST['workersName']) ? $_POST['workersName'] : [];
    $passNos = isset($_POST['passNo']) ? $_POST['passNo'] : [];
    $exactLocation = mysqli_real_escape_string($conn, $_POST['exactLocation']);
    $workTypes = isset($_POST['workType']) ? $_POST['workType'] : [];
    $briefDate = isset($_POST['briefDate']) ? mysqli_real_escape_string($conn, $_POST['briefDate']) : null;
    $briefTime = isset($_POST['briefTime']) ? mysqli_real_escape_string($conn, $_POST['briefTime']) : null;
    $briefConducted = isset($_POST['briefConducted']) ? mysqli_real_escape_string($conn, $_POST['briefConducted']) : null;
    $signC = mysqli_real_escape_string($conn, $_POST['signC']);
    $nameC = isset($_POST['nameC']) ? mysqli_real_escape_string($conn, $_POST['nameC']) : null;
    $positionC = isset($_POST['positionC']) ? mysqli_real_escape_string($conn, $_POST['positionC']) : null;
    $dateC = isset($_POST['dateC']) ? mysqli_real_escape_string($conn, $_POST['dateC']) : null;
    $timeC = isset($_POST['timeC']) ? mysqli_real_escape_string($conn, $_POST['timeC']) : null;
    $signA = mysqli_real_escape_string($conn, $_POST['signA']);
    $nameA = isset($_POST['nameA']) ? mysqli_real_escape_string($conn, $_POST['nameA']) : null;
    $positionA = isset($_POST['positionA']) ? mysqli_real_escape_string($conn, $_POST['positionA']) : null;
    $dateA = isset($_POST['dateA']) ? mysqli_real_escape_string($conn, $_POST['dateA']) : null;
    $timeA = isset($_POST['timeA']) ? mysqli_real_escape_string($conn, $_POST['timeA']) : null;
    $signI = mysqli_real_escape_string($conn, $_POST['signI']);
    $nameI = isset($_POST['nameI']) ? mysqli_real_escape_string($conn, $_POST['nameI']) : null;
    $positionI = isset($_POST['positionI']) ? mysqli_real_escape_string($conn, $_POST['positionI']) : null;
    $dateI = isset($_POST['dateI']) ? mysqli_real_escape_string($conn, $_POST['dateI']) : null;
    $timeI = isset($_POST['timeI']) ? mysqli_real_escape_string($conn, $_POST['timeI']) : null;
    $signS = mysqli_real_escape_string($conn, $_POST['signS']);
    $nameS = isset($_POST['nameS']) ? mysqli_real_escape_string($conn, $_POST['nameS']) : null;
    $positionS = isset($_POST['positionS']) ? mysqli_real_escape_string($conn, $_POST['positionS']) : null;
    $dateS = isset($_POST['dateS']) ? mysqli_real_escape_string($conn, $_POST['dateS']) : null;
    $timeS = isset($_POST['timeS']) ? mysqli_real_escape_string($conn, $_POST['timeS']) : null;

    // Handle hazards: Make sure it's an array and implode it into a string
    $hazard = isset($_POST['hazards']) ? $_POST['hazards'] : [];  
    $hazardString = implode(", ", array_map(function ($hazards) use ($conn) {
        return mysqli_real_escape_string($conn, $hazards);
    }, $hazard));

    // Handle PPEs: Same as hazards
    $ppes = isset($_POST['ppe']) ? $_POST['ppe'] : [];
    $ppesString = implode(", ", array_map(function ($ppe) use ($conn) {
        return mysqli_real_escape_string($conn, $ppe);
    }, $ppes));

    // Handle PPEs: Same as hazards
    $infections = isset($_POST['infection']) ? $_POST['infection'] : [];
    $infectionsString = implode(", ", array_map(function ($infection) use ($conn) {
        return mysqli_real_escape_string($conn, $infection);
    }, $infections));

    // Handle PPEs: Same as hazards
    $worksites = isset($_POST['worksite']) ? $_POST['worksite'] : [];
    $worksitesString = implode(", ", array_map(function ($worksite) use ($conn) {
        return mysqli_real_escape_string($conn, $worksite);
    }, $worksites));

    // Combine workers' names into a comma-separated string
    $workersNamesString = implode(", ", array_map(function ($workerName) use ($conn) {
        return mysqli_real_escape_string($conn, $workerName);
    }, $workersNames));

    // Combine workers' names into a comma-separated string
    $passNosString = implode(", ", array_map(function ($passNo) use ($conn) {
        return mysqli_real_escape_string($conn, $passNo);
    }, $passNos));

    // Combine work types into a comma-separated string
    $workTypesString = implode(", ", array_map(function ($workType) use ($conn) {
        return mysqli_real_escape_string($conn, $workType);
    }, $workTypes));

    // Check if a status radio button was selected
    if (isset($_POST['status'])) {
        $status = $_POST['status']; // Get the selected status value
    } else {
        // Determine the status based on safety briefing fields
        if ($status === 'completed') {
            $status = 'completed';
        } elseif (!empty($briefDate) && !empty($briefTime) && !empty($briefConducted)) {
            $status = 'in progress';
        } else {
            $status = 'pending';
        }
    }

    /***if (!empty($_FILES['files']['name'][0])) {
        $filePaths = []; 
		$maxFileSize = 10 * 1024 * 1024;
    
        foreach ($_FILES['files']['name'] as $key => $fileName) {
            $fileTmp = $_FILES['files']['tmp_name'][$key];
            $fileSize = $_FILES['files']['size'][$key];
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
            $allowedExts = ['pdf', 'jpg', 'jpeg', 'png'];
            if (!in_array($fileExt, $allowedExts)) {
                die("Error: Invalid file type ($fileExt). Only PDF, JPG, JPEG, PNG allowed.");
            }
    
            if ($fileSize > $maxFileSize) {
                die("Error: File too large. Max size is 5MB.");
            }
    
            $uploadDir = "uploads/";
            $uniqueFileName = time() . "_" . $fileName;
            $filePath = $uploadDir . $uniqueFileName;
			
			if (!is_dir('uploads')) {
				mkdir('uploads', 0777, true);
			}
    
            if (move_uploaded_file($fileTmp, $filePath)) {
                $filePaths[] = $filePath; 
            } else {
				$_SESSION['message'] = "One or more files failed to upload. Please check file size and type.";
				header("Location: form.php");
				exit();
                //die("Error: File upload failed for $fileName.");
            }
        }
    
        $sql_fetch_existing = "SELECT file FROM permit WHERE id='$applicantID'";
        $result = mysqli_query($conn, $sql_fetch_existing);
        $row = mysqli_fetch_assoc($result);
        $existingFiles = !empty($row['file']) ? explode(",", $row['file']) : [];
    
        $allFiles = array_merge($existingFiles, $filePaths);
        $storedFilePath = implode(",", $allFiles);
    } else {
        // Keep old files if no new upload
        $storedFilePath = $existing_permit['file'];
    }***/
	
	if (!empty($_FILES['files']['name'][0])) {
		$filePaths = [];

		// Ensure uploads directory exists
		$uploadDir = "uploads/";
		if (!is_dir($uploadDir)) {
			mkdir($uploadDir, 0777, true);
		}

		foreach ($_FILES['files']['name'] as $key => $fileName) {
			$fileTmp = $_FILES['files']['tmp_name'][$key];
			$fileSize = $_FILES['files']['size'][$key];
			$fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

			$allowedExts = ['pdf', 'jpg', 'jpeg', 'png'];
			if (!in_array($fileExt, $allowedExts)) {
				echo "<script>alert('Invalid file type ($fileExt). Only PDF, JPG, JPEG, PNG allowed.'); window.history.back();</script>";
				exit();
			}

			if ($fileSize > 10485760) { // 10MB = 10 * 1024 * 1024
				echo "<script>alert('File too large. Max size is 10MB.'); window.history.back();</script>";
				exit();
			}

			$uniqueFileName = time() . "_" . $fileName;
			$filePath = $uploadDir . $uniqueFileName;

			if (move_uploaded_file($fileTmp, $filePath)) {
				$filePaths[] = $filePath;
			} else {
				echo "<script>alert('File upload failed for $fileName.'); window.history.back();</script>";
				exit();
			}
		}

		// Merge with existing files if editing
		$sql_fetch_existing = "SELECT file FROM permit WHERE id='$applicantID'";
		$result = mysqli_query($conn, $sql_fetch_existing);
		$row = mysqli_fetch_assoc($result);
		$existingFiles = !empty($row['file']) ? explode(",", $row['file']) : [];

		$allFiles = array_merge($existingFiles, $filePaths);
		$storedFilePath = implode(",", $allFiles);
	} else {
		$storedFilePath = isset($existing_permit['file']) ? $existing_permit['file'] : '';
	}

    // Start transaction 
    mysqli_begin_transaction($conn);
    try {
        // SQL Update query for form
        $query_form = "UPDATE form SET 
            name='$name', 
            services='$services', 
            status='$status', 
            remark='$remark', 
            durationFrom='$durationFrom', 
            durationTo='$durationTo', 
            timeFrom='$timeFrom', 
            timeTo='$timeTo', 
            companyName='$companyName', 
            icNo='$icNo', 
            contactNo='$contactNo', 
            longTermContract='$longTermContract', 
            workersName='$workersNamesString',
            passNo='$passNosString',  
            exactLocation='$exactLocation', 
            workType='$workTypesString', 
            hazards='$hazardString', 
            briefDate='$briefDate', 
            briefTime='$briefTime', 
            briefConducted='$briefConducted', 
            infection='$infectionsString',
            worksite='$worksitesString', 
            ppe='$ppesString' 
        WHERE id='$applicantID'";
        
        $query_run_form = mysqli_query($conn, $query_form);
        if(!$query_run_form){
            throw new Exception('Form update failed: ' . mysqli_error($conn));
        }

        // Check if permit record exists
        $query_check_permit = "SELECT * FROM permit WHERE id='$applicantID'";
        $query_run_check_permit = mysqli_query($conn, $query_check_permit);
        
        if (mysqli_num_rows($query_run_check_permit) > 0) {
            // Fetch the existing permit data
            $existing_permit = mysqli_fetch_assoc($query_run_check_permit);

            // Update existing permit record, preserving unchanged data
            $query_permit = "UPDATE permit SET 
                signC='" . (!empty($signC) ? $signC : $existing_permit['signC']) . "', 
                nameC='" . (!empty($nameC) ? $nameC : $existing_permit['nameC']) . "', 
                positionC='" . (!empty($positionC) ? $positionC : $existing_permit['positionC']) . "', 
                dateC='" . (!empty($dateC) ? $dateC : $existing_permit['dateC']) . "', 
                timeC='" . (!empty($timeC) ? $timeC : $existing_permit['timeC']) . "', 
                signA='" . (!empty($signA) ? $signA : $existing_permit['signA']) . "', 
                nameA='" . (!empty($nameA) ? $nameA : $existing_permit['nameA']) . "', 
                positionA='" . (!empty($positionA) ? $positionA : $existing_permit['positionA']) . "', 
                dateA='" . (!empty($dateA) ? $dateA : $existing_permit['dateA']) . "', 
                timeA='" . (!empty($timeA) ? $timeA : $existing_permit['timeA']) . "', 
                signI='" . (!empty($signI) ? $signI : $existing_permit['signI']) . "', 
                nameI='" . (!empty($nameI) ? $nameI : $existing_permit['nameI']) . "', 
                positionI='" . (!empty($positionI) ? $positionI : $existing_permit['positionI']) . "', 
                dateI='" . (!empty($dateI) ? $dateI : $existing_permit['dateI']) . "', 
                timeI='" . (!empty($timeI) ? $timeI : $existing_permit['timeI']) . "', 
                signS='" . (!empty($signS) ? $signS : $existing_permit['signS']) . "', 
                nameS='" . (!empty($nameS) ? $nameS : $existing_permit['nameS']) . "', 
                positionS='" . (!empty($positionS) ? $positionS : $existing_permit['positionS']) . "', 
                dateS='" . (!empty($dateS) ? $dateS : $existing_permit['dateS']) . "', 
                timeS='" . (!empty($timeS) ? $timeS : $existing_permit['timeS']) . "',
                file = '" . mysqli_real_escape_string($conn, $storedFilePath) . "'
            WHERE id='$applicantID'";

        } else {
            // Insert new permit record
            $query_permit = "INSERT INTO permit (id, signC, nameC, positionC, dateC, timeC, signA, nameA, positionA, dateA, timeA, signI, nameI, positionI, dateI, timeI, signS, nameS, positionS, dateS, timeS, file) VALUES 
            ('$applicantID', '$signC', '$nameC', '$positionC', '$dateC', '$timeC', '$signA', '$nameA', '$positionA', '$dateA', '$timeA', '$signI', '$nameI', '$positionI', '$dateI', '$timeI', '$signS', '$nameS', '$positionS', '$dateS', '$timeS', '$storedFilePath')";
        }
        $query_run_permit = mysqli_query($conn, $query_permit);
        if (!$query_run_permit){
            throw new Exception('Signature insertion/update failed: ' . mysqli_error($conn));
        }

        // Commit transaction
        mysqli_commit($conn);
        $_SESSION['message'] = "Project Updated Successfully";
        header("Location: edit.php?id=".$applicantID);
        exit(0);
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($conn);
        echo 'Error: ' . $e->getMessage();
    }
}

if(isset($_POST['save_form'])) {
    // Sanitize inputs
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $services = mysqli_real_escape_string($conn, $_POST['services']);
    $durationFrom = mysqli_real_escape_string($conn, $_POST['durationFrom']);
    $durationTo = mysqli_real_escape_string($conn, $_POST['durationTo']);
    $timeFrom = mysqli_real_escape_string($conn, $_POST['timeFrom']);
    $timeTo = mysqli_real_escape_string($conn, $_POST['timeTo']);
    $companyName = mysqli_real_escape_string($conn, $_POST['companyName']);
    $svName = mysqli_real_escape_string($conn, $_POST['svName']);
    $icNo = mysqli_real_escape_string($conn, $_POST['icNo']);
    $contactNo = mysqli_real_escape_string($conn, $_POST['contactNo']);
    $longTermContract = mysqli_real_escape_string($conn, $_POST['longTermContract']);
    $workersNames = $_POST['workersName']; // Get the array of workers' names
    $passNos = $_POST['passNo']; // Get the array of pass no
    $exactLocation = mysqli_real_escape_string($conn, $_POST['exactLocation']);
    $workTypes = $_POST['workType']; // Get the array of work types
    $worksites = $_POST['worksite']; // Get the array of worksites                         
    $ppes = $_POST['ppe']; // Get the array of ppe   
    $status = "pending"; // Set status by default
    $remark = mysqli_real_escape_string($conn, $_POST['remark']);

    // Combine workers' names into a single string
    $workersNamesString = implode(", ", array_map(function($workerName) use ($conn) {
        return mysqli_real_escape_string($conn, $workerName);
    }, $workersNames));

    // Combine pass no into a single string
    $passNosString = implode(", ", array_map(function($passNo) use ($conn) {
        return mysqli_real_escape_string($conn, $passNo);
    }, $passNos));

    // Combine work types into a single string
    $workTypesString = implode(", ", array_map(function($workType) use ($conn) {
        return mysqli_real_escape_string($conn, $workType);
    }, $workTypes));

    // Combine hazard into a single string
    $hazardString = implode(", ", array_map(function($hazards) use ($conn) {
        return mysqli_real_escape_string($conn, $hazards);
    }, $hazard));

    // Combine ppe into a single string
    $ppesString = implode(", ", array_map(function($ppe) use ($conn) {
        return mysqli_real_escape_string($conn, $ppe);
    }, $ppes));

    // Combine worksites into a single string
    $worksitesString = implode(", ", array_map(function($worksite) use ($conn) {
        return mysqli_real_escape_string($conn, $worksite);
    }, $worksites));

    session_start(); // Ensure the session is started
    $userID = $_SESSION['user_id']; // Assuming you store user ID in session
    $userType = $_SESSION['user_type'];

    // Debugging: Print session variables
    error_log("Session user_id: " . $userID);
    error_log("Session user_type: " . $userType);

    // Insert form data
    $column = ($userType == 'admin' ? 'adminID' : 'applicantID');
    $query = "INSERT INTO form (name,services,status,remark,durationFrom,durationTo,timeFrom,timeTo,companyName,svName,icNo,contactNo,longTermContract,workersName,passNo,exactLocation,workType,hazards,briefDate,briefTime,briefConducted,ppe,worksite,$column) VALUES ('$name','$services','$status','$remark','$durationFrom','$durationTo','$timeFrom','$timeTo','$companyName','$svName','$icNo','$contactNo','$longTermContract','$workersNamesString','$passNosString','$exactLocation','$workTypesString','$hazardString','$briefDate','$briefTime','$briefConducted','$ppesString','$worksitesString','$userID')";

    // Debugging: Print SQL query
    error_log("SQL query: " . $query);

    $query_run = mysqli_query($conn, $query);

    if ($query_run) {
        $_SESSION['message'] = "Project Created Successfully";
    } else {
        $_SESSION['message'] = "Project Not Created";
    
        // Debugging: Print SQL error
        error_log("SQL error: " . mysqli_error($conn));
    }
    
    // Check user type and redirect accordingly
    if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin') {
		header("Location: dashboard.php"); 
	} else {
		header("Location: appdb.php");
	}
    exit(0);
    
}
?>