<?php
	$inData = getRequestInfo();
	
	$origName = $inData["origName"];
	$origPhone = $inData["origPhone"];
	$origEmail = $inData["origEmail"];

	$editName = $inData["editName"];
	$editPhone = $inData["editPhone"];
	$editEmail = $inData["editEmail"];

	$userId = $inData["userId"];
	
	$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
	if ($conn->connect_error)
	{
		returnWithInfo($conn->connect_error);
	}
	else
	{
		// Check for duplicate contact
		$stmt = $conn->prepare("SELECT * FROM Contacts WHERE Name = ? AND Phone = ? AND Email = ? AND UserID = ?");
		$stmt->bind_param("sssi", $editName, $editPhone, $editEmail, $userId);
		$stmt->execute();

		$result = $stmt->get_result();
		if($result->num_rows >= 1)
		{
			returnWithError("Error: Duplicate Contact.");
			return;
		}

		// Name is a required field, this must be provided
		if(empty($editName))
		{
			returnWithError("Error: Name cannot be blank.");
			return;
		}

		// Check for empty input and replace with N/A
		if(empty($editPhone))
			$editPhone = "N/A";
		if(empty($editEmail))
			$editEmail = "N/A";

		// Update contact
		$stmt = $conn->prepare("UPDATE Contacts SET Name = ?, Phone = ?, Email = ? WHERE Name = ? AND Phone = ? AND Email = ? AND UserID = ?");
		$stmt->bind_param("ssssssi", $editName, $editPhone, $editEmail, $origName, $origPhone, $origEmail, $userId);
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