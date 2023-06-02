<?php
	$inData = getRequestInfo();
	
	$name = $inData["name"];
	$phone = $inData["phone"];
	$email = $inData["email"];
	$userId = $inData["userId"];
	
	$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
	if ($conn->connect_error)
	{
		returnWithError($conn->connect_error);
	}
	else
	{
		$stmt = $conn->prepare("DELETE FROM Contacts WHERE Name = ? AND Phone = ? AND Email = ? AND UserID = ?");
		$stmt->bind_param("sssi", $name, $phone, $email, $userId);
		$stmt->execute();

		returnWithInfo("Contact removed.");
		
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
?>