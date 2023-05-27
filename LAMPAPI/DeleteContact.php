<?php
// Reads in request as Json file
	$inData = getRequestInfo();
	
	//Acesses json w/ key value pair
	$contactName = $inData["contactName"];
	
	//connection to database
	$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
	if ($conn->connect_error)
	{
		returnWithError($conn->connect_error);
	}
	else
	{
		// preparing mysql query replaces ? w/ the values in bind param
		$stmt = $conn->prepare("DELETE FROM Contacts WHERE Name = ?");
		$stmt->bind_param("s", $contactName);
		$stmt->execute(); // executes query
		$stmt->close(); //closing connection
		$conn->close();
		returnWithError("");
	}

	// I beleive this turns tmp from code.js into a json file
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
?>