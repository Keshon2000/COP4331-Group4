<?php
	$inData = getRequestInfo();
	
	$contactName = $inData["contactName"];
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
		$stmt = $conn->prepare("INSERT INTO Contacts (Name, Phone, Email, UserID) VALUES(?, ?, ?, ?)");
		$stmt->bind_param("sssi", $contactName, $contactPhone, $contactEmail, $userId);
		$stmt->execute();
		$stmt->close();
		$conn->close();
		returnWithError("");
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
	
	function returnWithError( $err )
	{
		$retValue = '{"error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
?>