<?php
	$inData = getRequestInfo();

	//TODO
	// Registration Date
	// Test everything as lowercase
	
	$id = 0;
	$registerDate = date('Y-m-d H:i:s');
	$firstName = $inData['firstName'];
	$lastName = $inData['lastName'];
	$email = $inData['email'];
	$username = $inData['username'];
	$password = $inData['password'];

	$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
	if( $conn->connect_error )
	{
		returnWithError( $conn->connect_error );
	}
	else
	{
		// Check if Email is Already in use
		  // Email field is not currently in database
		/*$stmt = $conn->prepare("SELECT Email FROM Users WHERE Email = ?");
		$stmt->bind_param("s", $email);
		$stmt->execute();
		$result = $stmt->get_result();
		if($result->num_rows > 0)
		{
			// Display email error
			returnWithError("Email Already in Use.");
			$stmt->close();
			$conn->close();
		}
		else
		{*/
			// Check if Username exists
			$message = "";
			$stmt = $conn->prepare("SELECT Login FROM Users WHERE Login = ?");
			$stmt->bind_param("s", $username);
			$stmt->execute();
			$result = $stmt->get_result();
			if($result->num_rows > 0)
			{
				// Display Username Error
				returnWithError("Username Already Exists.");
				$stmt->close();
				$conn->close();
				return;
			}
			
			// Add user to database
			$stmt = $conn->prepare("INSERT INTO Users(FirstName, LastName, Login, Password) VALUES (?, ?, ?, ?)");
			$stmt->bind_param("ssss", $firstName, $lastName, $username, $password);
			$stmt->execute();
			// Display success
			returnWithInfo("Account Created.");
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
		$retValue = '{"data": "' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
	function returnWithInfo( $message )
	{
		$retValue = '{"data": "' . $message . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
?>
