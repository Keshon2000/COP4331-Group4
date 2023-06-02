<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Reads in request as Json file
	$inData = getRequestInfo();
	
	//Acesses json w/ key value pair
	$oldName = $inData["oldName"];
	$contactName = $inData["contactName"];
    $contactPhone = $inData["contactPhone"];
    $contactMail = $inData["contactMail"];
	
	//connection to database
	$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
	if ($conn->connect_error)
	{
		returnWithError($conn->connect_error);
	}
	else
	{
		// preparing mysql query replaces ? w/ the values in bind param
		$stmt = $conn->prepare("UPDATE Contacts Set Phone = ?, Mail = ? WHERE Name = ?");
		$stmt->bind_param("sss",$contactPhone,$contactMail,$oldName);
		$stmt->execute(); // executes query
		$stmt = $conn->prepare("UPDATE Contacts Set Name = ? WHERE Email = ?");
		$stmt->bind_param("ss",$contactName,$contactMail);
		$stmt->execute();
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