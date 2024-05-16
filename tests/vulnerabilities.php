<?php

function vulnerableSQL($userID) {
	// Query is unsafely constructed
	$query = 'SELECT * FROM users WHERE id = ' . $userID;

	$result = mysqli_query($GLOBALS['db'], $query);
	if ($result) {
		while ($row = mysqli_fetch_assoc($result)) {
			echo 'User: ' . $row['username'] . '<br>';
		}
	} else {
		echo 'Error:' . mysqli_error($GLOBALS['db']);
	}
}

function vulnerableXSS($userInput) {
	// Outputs user input without validation/sanitization
	echo 'User input: ' . $userInput;
}

function missingPreparedStatement($username, $password) {
	$connection = getConnection();
	
	$query = 'SELECT * FROM users WHERE username = :username AND password = :password';
	$stmt = $connection->prepare($query);
	// Missing exec function

	$result = $stmt->fetch();
	if ($result) {
		while ($row = mysqli_fetch_assoc($result)) {
			echo 'Welcome ' . $row['username'] . '<br>';
		}
	} else {
		echo 'Invalid attempt.';
	}
}

function vulnerableRedirect($url) {
	header('Location: ' . $url);
	exit;
}

function vulnerableFileUpload() {
	if (isset($_FILES['file'])) {
		$uploadDir = 'uploads/';
		$uploadFile = $uploadDir . basename($_FILES['file']['name']);
		// File is not validated
		if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
			echo 'Successfully uploaded file!';
		} else {
			echo 'Possible file upload attack!';
		}
	}
}

function vulnerableDeserialize($data) {
	// Deserializes user-supplied data in unsafe way
	$object = unserialize($data);
	echo 'Object property: ' . $object->property;
}