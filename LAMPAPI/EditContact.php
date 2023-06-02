<?php
	$inData = getRequestInfo();

	$editFirstName = $inData["editFirstName"];
	$editLastName = $inData["editLastName"];
	$editPhone = $inData["editPhone"];
	$editEmail = $inData["editEmail"];
	$contactId = $inData["contactId"];

	$userId = $inData["userId"];
	
	$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
	if ($conn->connect_error)
	{
		returnWithInfo($conn->connect_error);
	}
	else
	{
		// Validate Email Format
		if (!filter_var($editEmail, FILTER_VALIDATE_EMAIL) && !empty($editEmail) && $editEmail != "N/A")
		{
			returnWithError("Error: Invalid email address. (Example: username@example.com)");
			return;
		}

		// Validate Phone Number
		$pattern = "/^\d{3}-\d{3}-\d{4}$/"; // Format: ###-###-####
		if (!preg_match($pattern, $editPhone) && !empty($editPhone) && $editPhone != "N/A")
		{
			returnWithError("Error: Invalid Phone Number. (Format: ### - ### - ####)");
			return;
		}

		// Check for duplicate contact (case-sensitive)
		$stmt = $conn->prepare("SELECT * FROM Contacts WHERE BINARY FirstName = ? AND BINARY LastName = ? AND Phone = ? AND BINARY Email = ? AND UserID = ?");
		$stmt->bind_param("ssssi", $editFirstName, $editLastName, $editPhone, $editEmail, $userId);
		$stmt->execute();

		$result = $stmt->get_result();
		if($result->num_rows >= 1)
		{
			returnWithError("Error: Duplicate Contact.");
			$stmt->close();
			$conn->close();
			return;
		}

		// Name is a required field, this must be provided
		if(empty($editFirstName))
		{
			returnWithError("Error: Name cannot be blank.");
			$stmt->close();
			$conn->close();
			return;
		}

		// Check for empty input and replace with N/A
		if(empty($editLastName))
			$editLastName = "N/A";
		if(empty($editPhone))
			$editPhone = "N/A";
		if(empty($editEmail))
			$editEmail = "N/A";

		// Update contact
		$stmt = $conn->prepare("UPDATE Contacts SET FirstName = ?, LastName = ?, Phone = ?, Email = ? WHERE ID = ?");
		$stmt->bind_param("ssssi", $editFirstName, $editLastName, $editPhone, $editEmail, $contactId);
		$stmt->execute();

		returnWithInfo("Contact Edit Saved.");
		
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