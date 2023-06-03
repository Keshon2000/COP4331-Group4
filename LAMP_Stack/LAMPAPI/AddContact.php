<?php
	$inData = getRequestInfo();
	
	$contactFirstName = $inData["contactFirstName"];
	$contactLastName = $inData["contactLastName"];
	$contactPhone = $inData["contactPhone"];
	$contactEmail = $inData["contactEmail"];
	$userId = $inData["userId"];
	
	$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
	if ($conn->connect_error)
	{
		returnWithError($conn->connect_error);
	}
	else
	{
		// Name is a required field, this must be provided
		if(empty($contactFirstName))
		{
			returnWithError("Error: First Name cannot be blank.");
			return;
		}

		// Validate Phone Number
		$pattern = "/^\d{3}-\d{3}-\d{4}$/"; // Format: ###-###-####
		if (!preg_match($pattern, $contactPhone) && !empty($contactPhone))
		{
			returnWithError("Error: Invalid Phone Number. (Format: ### - ### - ####)");
			return;
		}
		
		// Validate Email Format
		if (!filter_var($contactEmail, FILTER_VALIDATE_EMAIL) && !empty($contactEmail))
		{
			returnWithError("Error: Invalid email address. (Example: username@example.com)");
			return;
		}

		// Check for empty input and replace with N/A
		if(empty($contactLastName))
			$contactLastName = "N/A";
		if(empty($contactPhone))
			$contactPhone = "N/A";
		if(empty($contactEmail))
			$contactEmail = "N/A";

		// Check for duplicate contact
		$stmt = $conn->prepare("SELECT * FROM Contacts WHERE FirstName = ? AND LastName = ? AND Phone = ? AND Email = ? AND UserID = ?");
		$stmt->bind_param("ssssi", $contactFirstName, $contactLastName, $contactPhone, $contactEmail, $userId);
		$stmt->execute();

		$result = $stmt->get_result();
		if($result->num_rows >= 1)
		{
			returnWithError("Error: Duplicate Contact.");
			$stmt->close();
			$conn->close();
			return;
		}

		// Add Contact
		$stmt = $conn->prepare("INSERT INTO Contacts (FirstName, LastName, Phone, Email, UserID) VALUES(?, ?, ?, ?, ?)");
		$stmt->bind_param("ssssi", $contactFirstName, $contactLastName, $contactPhone, $contactEmail, $userId);
		$stmt->execute();

		returnWithInfo("Contact has been added.");

		$stmt->close();
		$conn->close();
	}

	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}
	
	function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		echo $obj;
	}

	function returnWithInfo( $searchResults )
	{
		$data = array(
			'results' => $searchResults
		);
		sendResultInfoAsJson( json_encode($data) );
	}

	function returnWithError( $err )
	{
		$data = array(
			'error' => true,
			'results' => $err
		);
		sendResultInfoAsJson( json_encode($data) );
	}
?>