<?php
	$inData = getRequestInfo();
	
	$id = 0;
	$registerDate = date('Y-m-d H:i:s');
	$firstName = $inData['firstName'];
	$lastName = $inData['lastName'];
	//$email = $inData['email'];
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

		// Validate the email address format
		/*if (!filter_var($email, FILTER_VALIDATE_EMAIL))
			returnWithError("Invalid email address.");
		$stmt = $conn->prepare("SELECT Email FROM Users WHERE Email = ?");
		$stmt->bind_param("s", $email);
		$stmt->execute();
		$result = $stmt->get_result();
		if($result->num_rows > 0)
		{
			// Display email error
			returnWithError("Error: Email Already in Use.");
			$stmt->close();
			$conn->close();
		}
		else
		{*/
			// Check if Username exists
			$stmt = $conn->prepare("SELECT Login FROM Users WHERE Login = ?");
			$stmt->bind_param("s", $username);
			$stmt->execute();
			$result = $stmt->get_result();
			if($result->num_rows > 0)
			{
				// Display Username Error
				returnWithError("Error: Username Already Exists.");
				$stmt->close();
				$conn->close();
				return;
			}

			// Check password strength
			if(!checkPasswordStrength($password))
			{
				returnWithError("Error: Password must contain: at least one capital letter, at least one special character, at least one number, must be eight characters or longer.");
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
	
	function checkPasswordStrength($password)
	{
		// Define the regex patterns for each requirement
		$patterns = [
		  '/[A-Z]/', // At least one capital letter
		  '/[!@#$%^&*(),.?":{}|<>]/', // At least one special character
		  '/\d/', // At least one number
		  '/.{8,}/' // At least 8 characters long
		];
	  
		// Check if the password meets all the requirements
		foreach ($patterns as $pattern)
		  if (!preg_match($pattern, $password))
			return false;
	  
		return true;
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
