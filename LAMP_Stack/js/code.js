const urlBase = 'http://openingdrive.space/LAMPAPI';
const extension = 'php';

let userId = 0;
let firstName = "";
let lastName = "";


function doLogin()
{
	userId = 0;
	firstName = "";
	lastName = "";
	
	let login = document.getElementById("loginName").value;
	let password = document.getElementById("loginPassword").value;
//	var hash = md5( password );

	document.getElementById("loginResult").innerHTML = "";

	let tmp = {login:login,password:password};
//	var tmp = {login:login,password:hash};
	let jsonPayload = JSON.stringify( tmp );
	
	let url = urlBase + '/Login.' + extension;

	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function() 
		{
			if (this.readyState == 4 && this.status == 200) 
			{
				let jsonObject = JSON.parse( xhr.responseText );
				userId = jsonObject.id;
		
				if( userId < 1 )
				{		
					document.getElementById("loginResult").innerHTML = "User/Password combination incorrect";
					return;
				}
		
				firstName = jsonObject.firstName;
				lastName = jsonObject.lastName;

				saveCookie();
	
				window.location.href = "contacts.html";
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("loginResult").innerHTML = err.message;
	}

}

function doRegistration()
{
	let register_username = document.getElementById("register_username").value;
	let register_password = document.getElementById("register_password").value;
	let register_fName = document.getElementById("register_fName").value;
	let register_lName = document.getElementById("register_lName").value;

//	var hash = md5( password );
	
	document.getElementById("registerResult").innerHTML = "";

	//let tmp = {username:register_username, email:register_email, firstName:register_fName, lastName:register_lName, password:register_password};
	let tmp = {username:register_username, firstName:register_fName, lastName:register_lName, password:register_password};
//	var tmp = {login:login,password:hash};
	let jsonPayload = JSON.stringify( tmp );
	
	let url = urlBase + '/Registration.' + extension;

	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function() 
		{
			if (this.readyState == 4 && this.status == 200) 
			{
				let jsonObject = JSON.parse(xhr.responseText);
				document.getElementById("registerResult").innerHTML = jsonObject.data;
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("registerResult").innerHTML = err.message;
	}

}

function saveCookie()
{
	let minutes = 20;
	let date = new Date();
	date.setTime(date.getTime()+(minutes*60*1000));	
	document.cookie = "firstName=" + firstName + ",lastName=" + lastName + ",userId=" + userId + ";expires=" + date.toGMTString();
}

function readCookie()
{
	userId = -1;
	let data = document.cookie;
	let splits = data.split(",");
	for(var i = 0; i < splits.length; i++) 
	{
		let thisOne = splits[i].trim();
		let tokens = thisOne.split("=");
		if( tokens[0] == "firstName" )
		{
			firstName = tokens[1];
		}
		else if( tokens[0] == "lastName" )
		{
			lastName = tokens[1];
		}
		else if( tokens[0] == "userId" )
		{
			userId = parseInt( tokens[1].trim() );
		}
	}
	
	if( userId < 0 )
	{
		window.location.href = "index.html";
	}
	else
	{
		document.getElementById("userName").innerHTML = "Logged in as " + firstName + " " + lastName;
	}
}

function doLogout()
{
	userId = 0;
	firstName = "";
	lastName = "";
	document.cookie = "firstName= ; expires = Thu, 01 Jan 1970 00:00:00 GMT";
	window.location.href = "index.html";
}
							
function addContact()
{
	// Grab values
	let contactFirstName = document.getElementById("contactFirstName").value;
	let contactLastName = document.getElementById("contactLastName").value;
	let contactPhone = document.getElementById("contactPhone").value;
	let contactEmail = document.getElementById("contactEmail").value;
	
	document.getElementById("contactAddResult").innerHTML = "";

	// Create json object
	let tmp = {contactFirstName, contactLastName, contactPhone, contactEmail, userId};
	let jsonPayload = JSON.stringify( tmp );

	let url = urlBase + '/AddContact.' + extension;
	
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function() 
		{
			if (this.readyState == 4 && this.status == 200) 
			{
				// Parse json
				let jsonObject = JSON.parse( xhr.responseText );
				document.getElementById("contactAddResult").innerHTML = jsonObject.results;

				// Refresh list if no errors are present
				if(!jsonObject.error)
					searchContact();
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("contactAddResult").innerHTML = err.message;
		
	}
}

// Global variables for saving original contact before editing
let origFirstName;
let origLastName;
let origPhone;
let origEmail;
let currentRow;

function editContact(button)
{
	// Check if editing a different row and revert any changes
	if(currentRow !== undefined && currentRow !== button.parentNode.parentNode)
	{
		// Remove editable table fields
		for (let i = 0; i < currentRow.cells.length - 3; i++)
		{
			currentRow.cells[i].classList.remove('editable-cell');
			currentRow.cells[i].contentEditable = false;
		}

		// Revert text
		currentRow.cells[0].textContent = origFirstName;
		currentRow.cells[1].textContent = origLastName;
		currentRow.cells[2].textContent = origEmail;
		currentRow.cells[3].textContent = origPhone;
		currentRow.cells[4].querySelector('button').textContent = "Edit";
	}

	// Set current row
	currentRow = button.parentNode.parentNode;

	// Set editable table fields of a given row
	for (let i = 0; i < currentRow.cells.length - 3; i++)
			currentRow.cells[i].contentEditable = true;

	if(button.textContent === 'Save')
	{
		// Get updated fields
		let editFirstName = currentRow.cells[0].textContent;
		let editLastName = currentRow.cells[1].textContent;
		let editEmail = currentRow.cells[2].textContent;
		let editPhone = currentRow.cells[3].textContent;
		let contactId = currentRow.cells[6].textContent;

		// Prep json
		let tmp = {editFirstName, editLastName, editPhone, editEmail, contactId, userId};
		let jsonPayload = JSON.stringify( tmp );

		let url = urlBase + '/EditContact.' + extension;

		// Only update database if changes were made
		if(origFirstName !== editFirstName || origLastName !== editLastName || origEmail !== editEmail || origPhone !== editPhone)
		{
			let xhr = new XMLHttpRequest();
			xhr.open("POST", url, true);
			xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
			try
			{
				xhr.onreadystatechange = function() 
				{
					if (this.readyState == 4 && this.status == 200) 
					{
						// Parse json and set search message
						let jsonObject = JSON.parse( xhr.responseText );
						document.getElementById("contactSearchResult").innerHTML = jsonObject.results;

						// Revert text if there is an error
						if(jsonObject.error)
						{
							currentRow.cells[0].textContent = origFirstName;
							currentRow.cells[1].textContent = origLastName;
							currentRow.cells[2].textContent = origEmail;
							currentRow.cells[3].textContent = origPhone;
						}
						else
						{
							// Place N/A in empty cells
							if(editLastName === "")
								currentRow.cells[1].textContent = "N/A";
							if(editEmail === "")
								currentRow.cells[2].textContent = "N/A";
							if(editPhone === "")
								currentRow.cells[3].textContent = "N/A";
						}
					}
				};
				xhr.send(jsonPayload);
			}
			catch(err)
			{
				document.getElementById("contactSearchResult").innerHTML = err.message;
			}
		}
		
		// Remove editable table fields 
		for (let i = 0; i < currentRow.cells.length - 3; i++)
		{
			currentRow.cells[i].classList.remove('editable-cell');
			currentRow.cells[i].contentEditable = false;
		}

		button.textContent = "Edit";
	}
	else
	{
		// Save original row details, change button text, and set css class
		origFirstName = currentRow.cells[0].textContent;
		origLastName = currentRow.cells[1].textContent;
		origEmail = currentRow.cells[2].textContent;
		origPhone = currentRow.cells[3].textContent;

		for (let i = 0; i < currentRow.cells.length - 3; i++)
			currentRow.cells[i].classList.add('editable-cell');

		button.textContent = "Save";
	}
}

function deleteContact(button)
{
	// Get row details
	let row = button.parentNode.parentNode;
	let name = row.cells[0].textContent;
	let email = row.cells[1].textContent;
	let phone = row.cells[2].textContent;
	let contactId = row.cells[6].textContent;

	// Set row class for css
	row.classList.add('delete-row');

	let result = confirm("Are you sure you want to delete the following contact?\nName: " + name + "\nEmail: " + email + "\nPhone: " + phone);
	
	if(result)
	{
		document.getElementById("contactSearchResult").innerHTML = "";

		let tmp = {contactId};
		let jsonPayload = JSON.stringify( tmp );

		let url = urlBase + '/DeleteContact.' + extension;
		
		let xhr = new XMLHttpRequest();
		xhr.open("POST", url, true);
		xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
		try
		{
			xhr.onreadystatechange = function() 
			{
				if (this.readyState == 4 && this.status == 200) 
				{
					// Set search message
					let jsonObject = JSON.parse( xhr.responseText );
					document.getElementById("contactSearchResult").innerHTML = jsonObject.results;
					
					// Clear row
					row.parentNode.removeChild(row);
				}
			};
			xhr.send(jsonPayload);
		}
		catch(err)
		{
			document.getElementById("contactSearchResult").innerHTML = err.message;
		}
	}
	row.classList.remove('delete-row');
}

function searchContact()
{
	let search = document.getElementById("searchContact").value;

	// Set headings
	let contactTable = 
	'<tr>' + 
	'<th onclick="sortTable(0)">First Name</th>' + 
	'<th onclick="sortTable(1)">Last Name</th>' + 
	'<th onclick="sortTable(2)">Email</th>' + 
	'<th onclick="sortTable(3)">Phone</th>' + 
	'<th>Edit</th>' + 
	'<th>Delete</th>' + 
	'<th style="display: none;">ID</th>' + 
	'</tr>';

	let tmp = {search, userId};
	let jsonPayload = JSON.stringify( tmp );

	let url = urlBase + '/SearchContacts.' + extension;

	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function()
		{
			if (this.readyState == 4 && this.status == 200)
			{
				let jsonObject = JSON.parse( xhr.responseText );

				// Check for error				
				if(jsonObject.error)
					document.getElementById("contactSearchResult").innerHTML = jsonObject.errorMessage;
				else
				{
					document.getElementById("contactSearchResult").innerHTML = "";

					// Fill out table
					for( let i=0; i < jsonObject.results.length; i++ )
					{
						contactTable += "<tr>";
						
						contactTable += "<td>" + jsonObject.results[i].firstName + "</td>";
						contactTable += "<td>" + jsonObject.results[i].lastName + "</td>";
						contactTable += "<td>" + jsonObject.results[i].email + "</td>";
						contactTable += "<td>" + jsonObject.results[i].phone + "</td>";

						// Edit & Delete Buttons
						contactTable += "<td>" + '<button onclick="editContact(this)">Edit</button>' + "</td>";
						contactTable += "<td>" + '<button onclick="deleteContact(this)">Delete</button>' + "</td>";
						
						// Contact ID
						contactTable += '<td style="display: none;">' + jsonObject.results[i].id + "</td>";
						
						contactTable += "</tr>";
					}
					
					// Set table
					document.getElementById("contactTable").innerHTML = contactTable;
				}
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("contactSearchResult").innerHTML = err.message;

	}
}

// Initial sorting order
let sortingOrder = "asc";

function sortTable(columnIndex)
{
	let table, rows, switching, i, x, y;
	table = document.getElementById("contactTable");
	switching = true;

	while (switching)
	{
		switching = false;
		rows = table.rows;

		for (i = 1; i < rows.length - 1; i++)
		{
			x = rows[i].getElementsByTagName("td")[columnIndex];
			y = rows[i + 1].getElementsByTagName("td")[columnIndex];

			let comparisonResult;
			if (sortingOrder === "asc")
				comparisonResult = x.innerHTML.toLowerCase().localeCompare(y.innerHTML.toLowerCase());
			else
				comparisonResult = y.innerHTML.toLowerCase().localeCompare(x.innerHTML.toLowerCase());

			if (comparisonResult > 0)
			{
				rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
				switching = true;
				break;
			}
		}
	}

	// Toggle sorting order
	if (sortingOrder === "asc")
		sortingOrder = "desc";
	else
		sortingOrder = "asc";
 }