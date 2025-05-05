<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	if (session_status() === PHP_SESSION_NONE) {
		session_start();
	}
	require 'dbconn.php';
	
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;
	
	require 'PHPMailer/src/Exception.php';
	require 'PHPMailer/src/PHPMailer.php';
	require 'PHPMailer/src/SMTP.php';
	
	function sendAdminNotification($userName, $formId, $senderName, $companyName, $durationFrom, $durationTo, $timeFrom, $timeTo, $services, $workTypesString, $exactLocation) {
		$mail = new PHPMailer(true);
		try {
			// Gmail SMTP Configuration
			$mail->isSMTP();
			$mail->Host = 'smtp.gmail.com';
			$mail->SMTPAuth = true;
			$mail->Username = 'ethosylar1990@gmail.com';         // your Gmail address
			$mail->Password = 'wgxxyqfhbkfupegk';      // 16-digit App Password
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Use SSL
			$mail->Port = 465;
			
			$mail->setFrom('ethosylar1990@gmail.com', 'Permit To Work System');
			
			// Add recipients
			$adminEmails = [
			'haziq.fiqri.4417@gmail.com'
			];
			
			foreach ($adminEmails as $email) {
				$mail->addAddress($email);
			}
			
			$mail->isHTML(true);
			$mail->Subject = 'New Permit Submission by ' . $senderName;
			
			$mail->Body = "
			<html>
			<body style='font-family: Arial, sans-serif;'>
            <p>Dear Admin,</p>
			
            <p>A new <strong>Permit To Work</strong> form has been submitted. Below are the submitted details:</p>
			
            <table cellpadding='8' cellspacing='0' border='1' style='border-collapse: collapse; font-size: 14px;'>
			<tr>
			<td><strong>Submitted By</strong></td>
			<td>{$senderName} (Username: {$userName})</td>
			</tr>
			<tr>
			<td><strong>Company Name</strong></td>
			<td>{$companyName}</td>
			</tr>
			<tr>
			<td><strong>Service</strong></td>
			<td>{$services}</td>
			</tr>
			<tr>
			<td><strong>Work Duration</strong></td>
			<td>From {$durationFrom} to {$durationTo}</td>
			</tr>
			<tr>
			<td><strong>Work Time</strong></td>
			<td>From {$timeFrom} to {$timeTo}</td>
			</tr>
			<tr>
			<td><strong>Work Types</strong></td>
			<td>{$workTypesString}</td>
			</tr>
			<tr>
			<td><strong>Location</strong></td>
			<td>{$exactLocation}</td>
			</tr>
			<tr>
			<td><strong>Form ID</strong></td>
			<td>#{$formId}</td>
			</tr>
            </table>
			
            <p>You may review and proceed with the application at the following link:</p>
            <p><a href='http://localhost/PTW/edit.php?id={$formId}' target='_blank'>View Submitted Form</a></p>
			
            <p>Best regards,<br>Permit To Work System – IT Services @ KPJ Klang</p>
			</body>
			</html>";
			
			$mail->send();
			} catch (Exception $e) {
			error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
		}
	}
	
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
		try {
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
			$status = isset($_POST['status']) ? mysqli_real_escape_string($conn, $_POST['status']) : 'pending';
			
			// Normalize 'resume work' to 'in progress'
			if ($status === 'resume work') {
				$status = 'in progress';
				} elseif (empty($status)) {
				// Determine the status based on briefing fields (fallback)
				if (!empty($briefDate) && !empty($briefTime) && !empty($briefConducted)) {
					$status = 'in progress';
					} else {
					$status = 'pending';
				}
			}
			
			if (!in_array($status, ['completed', 'stop work', 'cancel', 'resume work', 'in progress', 'pending'])) {
				throw new Exception("Invalid status selected.");
			}
			
			// ─────────────────────────────────────────────────────────────
			// STEP 1: Fetch Existing Files (before any file operations)
			// ─────────────────────────────────────────────────────────────
			$sql_fetch_existing = "SELECT * FROM permit WHERE id='$applicantID'";
			$result = mysqli_query($conn, $sql_fetch_existing);
			$existing_permit = mysqli_fetch_assoc($result);
			$existingFiles = !empty($existing_permit['file']) ? explode(",", $existing_permit['file']) : [];
			
			
			// ─────────────────────────────────────────────────────────────
			// STEP 2: Handle New Uploads
			// ─────────────────────────────────────────────────────────────
			$newFiles = [];
			
			if (!empty($_FILES['files']['name'][0])) {
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
					
					if ($fileSize > 10 * 1024 * 1024) {
						echo "<script>alert('File too large. Max size is 10MB.'); window.history.back();</script>";
						exit();
					}
					
					$uniqueFileName = time() . "_" . basename($fileName);
					$filePath = $uploadDir . $uniqueFileName;
					
					if (move_uploaded_file($fileTmp, $filePath)) {
						$newFiles[] = $filePath;
						} else {
						echo "<script>alert('File upload failed for $fileName.'); window.history.back();</script>";
						exit();
					}
				}
			}
			
			
			// ─────────────────────────────────────────────────────────────
			// STEP 3: Handle Deletions (if any marked for deletion)
			// ─────────────────────────────────────────────────────────────
			$deletedFiles = isset($_POST['deleted_files']) ? $_POST['deleted_files'] : [];
			
			foreach ($deletedFiles as $deletedFile) {
				$deletedFile = trim($deletedFile);
				if (file_exists($deletedFile)) {
					unlink($deletedFile); // delete from disk
				}
			}
			
			// Remove deleted files from the existing list
			$remainingFiles = array_diff($existingFiles, $deletedFiles);
			
			// ─────────────────────────────────────────────────────────────
			// STEP 4: Combine all (remaining + new), then implode
			// ─────────────────────────────────────────────────────────────
			$finalFiles = array_merge($remainingFiles, $newFiles);
			$storedFilePath = implode(",", $finalFiles);
			
			
			// Start transaction 
			mysqli_begin_transaction($conn);
			
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
				$query_permit = "INSERT INTO permit 
				(id, signC, nameC, positionC, dateC, timeC, 
				signA, nameA, positionA, dateA, timeA, 
				signI, nameI, positionI, dateI, timeI, 
				signS, nameS, positionS, dateS, timeS, file) 
				VALUES 
				('$applicantID', '$signC', '$nameC', '$positionC', '$dateC', '$timeC', 
				'$signA', '$nameA', '$positionA', '$dateA', '$timeA', 
				'$signI', '$nameI', '$positionI', '$dateI', '$timeI', 
				'$signS', '$nameS', '$positionS', '$dateS', '$timeS', '$storedFilePath')";
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
			echo "<script>alert('Error: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
		}
	}
	
	if (isset($_POST['save_form'])) {
		require_once 'dbconn.php';
		
		$userID = $_SESSION['user_id'];
		$userType = $_SESSION['user_type'];
		$username = $_SESSION['username'];
		$column = $userType === 'admin' ? 'adminID' : 'applicantID';
		
		// Prepare values (sanitize)
		$name = $_POST['name'];
		$services = $_POST['services'];
		$status = 'pending';
		$remark = $_POST['remark'] ?? '';
		$durationFrom = $_POST['durationFrom'];
		$durationTo = $_POST['durationTo'];
		$timeFrom = $_POST['timeFrom'];
		$timeTo = $_POST['timeTo'];
		$companyName = $_POST['companyName'];
		$svName = $_POST['svName'];
		$icNo = $_POST['icNo'];
		$contactNo = $_POST['contactNo'];
		$longTermContract = $_POST['longTermContract'];
		$exactLocation = $_POST['exactLocation'];
		$briefDate = $_POST['briefDate'];
		$briefTime = $_POST['briefTime'];
		$briefConducted = $_POST['briefConducted'];
		
		$workersNamesString = implode(", ", $_POST['workersName'] ?? []);
		$passNosString = implode(", ", $_POST['passNo'] ?? []);
		$workTypesString = implode(", ", $_POST['workType'] ?? []);
		$hazardString = implode(", ", $_POST['hazards'] ?? []);
		$ppesString = implode(", ", $_POST['ppe'] ?? []);
		$worksitesString = implode(", ", $_POST['worksite'] ?? []);
		
		// Permit fields
		$signC = $_POST['signC'];
		$nameC = $_POST['nameC'];
		$positionC = $_POST['positionC'];
		$dateC = $_POST['dateC'];
		$timeC = $_POST['timeC'];
		$signA = $_POST['signA'];
		$nameA = $_POST['nameA'];
		$positionA = $_POST['positionA'];
		$dateA = $_POST['dateA'];
		$timeA = $_POST['timeA'];
		$signI = $_POST['signI'];
		$nameI = $_POST['nameI'];
		$positionI = $_POST['positionI'];
		$dateI = $_POST['dateI'];
		$timeI = $_POST['timeI'];
		$signS = $_POST['signS'];
		$nameS = $_POST['nameS'];
		$positionS = $_POST['positionS'];
		$dateS = $_POST['dateS'];
		$timeS = $_POST['timeS'];
		
		// 1. Insert into `form`
		$stmt = $conn->prepare("INSERT INTO form 
		(name, services, status, remark, durationFrom, durationTo, timeFrom, timeTo,
		companyName, svName, icNo, contactNo, longTermContract,
		workersName, passNo, exactLocation, workType, hazards,
		briefDate, briefTime, briefConducted, ppe, worksite, $column)
		VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		
		$stmt->bind_param("ssssssssssssssssssssssss", 
		$name, $services, $status, $remark, $durationFrom, $durationTo, $timeFrom, $timeTo,
		$companyName, $svName, $icNo, $contactNo, $longTermContract,
		$workersNamesString, $passNosString, $exactLocation, $workTypesString, $hazardString,
		$briefDate, $briefTime, $briefConducted, $ppesString, $worksitesString, $userID
		);
		
		if ($stmt->execute()) {
			$formId = $stmt->insert_id;
			
			// 2. Insert into `permit`
			$stmt_permit = $conn->prepare("INSERT INTO permit 
            (id, signC, nameC, positionC, dateC, timeC,
			signA, nameA, positionA, dateA, timeA,
			signI, nameI, positionI, dateI, timeI,
			signS, nameS, positionS, dateS, timeS) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
			
			$stmt_permit->bind_param("issssssssssssssssssss",
            $formId, $signC, $nameC, $positionC, $dateC, $timeC,
            $signA, $nameA, $positionA, $dateA, $timeA,
            $signI, $nameI, $positionI, $dateI, $timeI,
            $signS, $nameS, $positionS, $dateS, $timeS);
			
			if ($stmt_permit->execute()) {
				sendAdminNotification(
                $username, $formId, $name, $companyName, $durationFrom,
                $durationTo, $timeFrom, $timeTo, $services, $workTypesString, $exactLocation
				);
				$_SESSION['message'] = "Project and Permit Created Successfully";
				} else {
				$_SESSION['message'] = "Form Created but Permit Failed";
				error_log("Permit insert error: " . $stmt_permit->error);
			}
			
			} else {
			$_SESSION['message'] = "Project Not Created";
			error_log("Form insert error: " . $stmt->error);
		}
		
		header("Location: " . ($userType === 'admin' ? "dashboard.php" : "appdb.php"));
		exit();
	}
?>