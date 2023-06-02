<?php

	$inData = getRequestInfo();

	$userId = $inData["userId"];
	
	$searchResults = "";
	$searchCount = 0;

	$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
	if ($conn->connect_error)
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		// Prepare query - Search includes first name and last name partial matches
		$stmt = $conn->prepare("SELECT ID, FirstName, LastName, Phone, Email FROM Contacts WHERE FirstName LIKE ? AND UserID = ? OR LastName LIKE ? AND UserID = ? OR Email LIKE ? AND UserID = ? OR Phone LIKE ? AND UserID = ? ORDER BY FirstName ASC");
		$contactSearch = "%" . $inData["search"] . "%";
		$stmt->bind_param("sisisisi", $contactSearch, $userId, $contactSearch, $userId, $contactSearch, $userId, $contactSearch, $userId);
		$stmt->execute();
		
		$result = $stmt->get_result();
		
		// Array containing json objects
		$data = array();

		// Creating json objects and placing them in the $data array
		while($row = $result->fetch_assoc())
		{
			$object = array(
				'id' => $row["ID"],
				'firstName' => $row["FirstName"],
				'lastName' => $row["LastName"],
				'phone' => $row["Phone"],
				'email' => $row["Email"]
			);

			$data[] = $object;
		}

		// Return results
		if( $result->num_rows == 0 )
			returnWithError( "No Records Found" );
		else
			returnWithInfo( $data );
		
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
	
	function returnWithError( $err )
	{
		$data = array(
			'error' => true,
			'errorMessage' => $err,
			'results' => array()
		);
		sendResultInfoAsJson( json_encode($data) );
	}
	
	function returnWithInfo( $searchResults )
	{
		$data = array(
			'error' => false,
			'results' => $searchResults
		);
		sendResultInfoAsJson( json_encode($data) );
	}
	
?>