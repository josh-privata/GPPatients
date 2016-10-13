<?php /* This script defines functions used by the website.
 */
require('mysqli_connect.php');
/* This function determines an absolute URL and redirects the user there.
 * The function takes one argument: the page to be redirected to. */
function redirect_user ($page = '') {
	$url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
	$url = rtrim($url, '/\\');
	$url .= '/' . $page;
	header("Location: $url");
	exit();
}
/* This function validates the form data (the email address and password).
 * If both are present, the database is queried.
 * The function requires a database connection.
 * The function returns an array of information, including:
 * - a TRUE/FALSE variable indicating success
 * - an array of either errors or the database result */
function check_login($dbc, $username = '', $pass = ''){
    // Initialize error array
    $errors = array();
    // Validate the username
    if (empty($username)) {
        $errors[] = 'You forgot to enter your username';
    } else {
        $e = mysqli_real_escape_string($dbc, sanitise_input($dbc,$username));
    }
    // Validate the password
    if (empty($pass)) {
        $errors[] = 'You forgot to enter your password.';
    } else {
        $p = mysqli_real_escape_string($dbc, sanitise_input($dbc,$pass));
    }
    // Query database
    if (empty($errors)) {
        $q = "SELECT username FROM authorized_users WHERE username='$e' AND password=SHA1('$p')";
        $r = @mysqli_query($dbc, $q);
        if (mysqli_num_rows($r) == 1) {
            $row = mysqli_fetch_array($r, MYSQLI_NUM);
            return array(true, $row);
        } else {
            $errors[]='The email address and password entered do not match those on file.';
        }
    }
        return array(false,$errors);
    }
/* This function sanitises form input.
 * The function takes one argument: user form input.
 * The function returns the sanitised form input. */
function sanitise_input ($dbc,$input='') {
    return trim(strip_tags(htmlentities(htmlspecialchars(mysqli_real_escape_string($dbc,$input)))));
}
/* This page prints any errors associated with logging in
 * and it creates the login page.
 * This function accepts no input and return the login form */
function login () {
print'<form action="home.php" method="post">
	<p>Username: <input type="text" name="username" size="20" maxlength="60" /> </p>
	<p>Password: <input type="password" name="pass" size="20" maxlength="20" /></p>
	<p><input type="submit" name="submit" value="Login" /></p>
</form>';
    include('footer.php');
}
/* This function queries the patient data for a particular
 * patient via patientid.
 * The function requires a database connection and a patientid.
 * The function returns an array of information, including:
 * - a TRUE/FALSE variable indicating success
 * - an array of either errors or the database result */
function get_patient_by_id ($dbc, $patientid='') {
    $errors = array();
    $q = "SELECT first_name, last_name, date_of_birth, sex, address, city, phone, patientid FROM patients WHERE patientid=$patientid";
    $r = @mysqli_query($dbc, $q);
    // Valid patient ID
    if (mysqli_num_rows($r) == 1) {
        $row = mysqli_fetch_array($r, MYSQLI_NUM);
        return array(true,$row);
    } else {
        $errors[]='There was an error accessing the patient';
    }
    return array(false, $errors);
}
/* This function queries the patient data for a particular
 * patient via last_name.
 * The function requires a database connection and a last_name.
 * The function returns an array of database results */
function get_patient_by_lastname ($dbc, $last_name) {
    $q = "SELECT patientid, CONCAT(last_name, ', ', first_name) AS Name, date_of_birth, phone FROM patients WHERE last_name=$last_name";
    return @mysqli_query($dbc, $q);
}
/* This function retrieves all the records from the users table.
 * The function requires a database connection.
 * The function returns an array of the query result */
function getall_patients ($dbc) {
    $q = "SELECT patientid, CONCAT(last_name, ', ', first_name) AS Name, date_of_birth, phone FROM patients ORDER BY Name DESC";
    return @mysqli_query ($dbc, $q);
}
/* This function updates the patient data for a particular
 * patient via patientid.
 * The function requires a database connection and all patient details.
 * The function returns an array of information, including:
 * - a TRUE/FALSE variable indicating success
 * - an array of either errors or the database result */
function update_patient ($dbc, $first_name='', $last_name='', $date_of_birth='', $sex='', $address='', $city='', $phone='', $patientid=''){
    // Initialize error array
    $errors = array();
    // Check for a first name
    if (!($first_name)) {
        $errors[] = 'You forgot to enter a first name.';
    } else {
        $fn = mysqli_real_escape_string($dbc, sanitise_input($dbc,$first_name));
    }
    // Check for a last name
    if (!($last_name)) {
        $errors[] = 'You forgot to enter a last name.';
    } else {
        $ln = mysqli_real_escape_string($dbc, sanitise_input($dbc,$last_name));
    }
    // Check for an DOB
    if (empty($date_of_birth)) {
        $errors[] = 'You forgot to enter a date of birth.';
    } else {
        $db = mysqli_real_escape_string($dbc, sanitise_input($dbc,$date_of_birth));
    }
    // Check for a sex
    if (empty($sex)) {
        $errors[] = 'You forgot to enter a sex.';
    } else {
        $sx = mysqli_real_escape_string($dbc, sanitise_input($dbc,$sex));
    }
    // Check for a address
    if (empty($address)) {
        $errors[] = 'You forgot to enter an address.';
    } else {
        $ad = mysqli_real_escape_string($dbc, sanitise_input($dbc,$address));
    }
    // Check for a city
    if (empty($city)) {
        $errors[] = 'You forgot to enter a city.';
    } else {
        $ci = mysqli_real_escape_string($dbc, sanitise_input($dbc,$city));
    }
    // Check for a phone
    if (empty($phone)) {
        $errors[] = 'You forgot to enter a contact number.';
    } else {
        $ph = mysqli_real_escape_string($dbc, sanitise_input($dbc,$phone));
    }
    // Query Database
    if (empty($errors)) {
        $q = "UPDATE patients
                  SET first_name='$fn',last_name='$ln',date_of_birth='$db',sex='$sx',address='$ad',city='$ci',phone='$ph'
                  WHERE patientid=$patientid LIMIT 1";
        $r = @mysqli_query ($dbc, $q);
        if ($r) {
            return array(true,$r);
        } else {
            $errors[]='There was an error modifying the patient';
        }
    }
    return array(false, $errors);
}
/* This function adds the patient data for a new patient via patientid.
 * The function requires a database connection and all patient details.
 * The function returns an array of information, including:
 * - a TRUE/FALSE variable indicating success
 * - an array of either errors or the database result */
function add_patient ($dbc, $first_name='', $last_name='', $date_of_birth='', $sex='', $address='', $city='', $phone=''){
    // Initialize error array
    $errors = array();
    // Check for a first name
    if (!($first_name)) {
        $errors[] = 'You forgot to enter a first name.';
    } else {
        $fn = mysqli_real_escape_string($dbc, sanitise_input($dbc,$first_name));
    }
    // Check for a last name
    if (!($last_name)) {
        $errors[] = 'You forgot to enter a last name.';
    } else {
        $ln = mysqli_real_escape_string($dbc, sanitise_input($dbc,$last_name));
    }
    // Check for an DOB
    if (empty($date_of_birth)) {
        $errors[] = 'You forgot to enter a date of birth.';
    } else {
        $db = mysqli_real_escape_string($dbc, sanitise_input($dbc,$date_of_birth));
    }
    // Check for a sex
    if (empty($sex)) {
        $errors[] = 'You forgot to enter a sex.';
    } else {
        $sx = mysqli_real_escape_string($dbc, sanitise_input($dbc,$sex));
    }
    // Check for a address
    if (empty($address)) {
        $errors[] = 'You forgot to enter an address.';
    } else {
        $ad = mysqli_real_escape_string($dbc, sanitise_input($dbc,$address));
    }
    // Check for a city
    if (empty($city)) {
        $errors[] = 'You forgot to enter a city.';
    } else {
        $ci = mysqli_real_escape_string($dbc, sanitise_input($dbc,$city));
    }
    // Check for a phone
    if (empty($phone)) {
        $errors[] = 'You forgot to enter a contact number.';
    } else {
        $ph = mysqli_real_escape_string($dbc, sanitise_input($dbc,$phone));
    }
    // Query Database
    if (empty($errors)) {
        $q = "INSERT INTO patients (first_name, last_name, date_of_birth, sex, address, city, phone) 
              VALUES ('$fn','$ln','$db','$sx','$ad','$ci','$ph')";
        $r = @mysqli_query ($dbc, $q);
        if (mysqli_affected_rows($dbc) == 1) {
            return array(true,$r);
        } else {
            $errors[]='There was an error adding the patient';
        }
    }
    return array(false, $errors);
}
/* This function deletes the patient data for a particular patient via patientid.
 * The function requires a database connection and a patientid.
 * The function returns an array of information, including:
 * - a message or FALSE variable indicating success
 * - an array of errors*/
function delete_patient ($dbc, $patientid='') {
    $errors = array();
        $q = "DELETE FROM patients WHERE patientid=$patientid LIMIT 1";
        $r = @mysqli_query ($dbc, $q);
        if (mysqli_affected_rows($dbc) == 1) {
            echo '<p>The patient has been deleted.</p>';
    } else {
        $errors[]='There was an error deleting the patient';
    }
    return array(false, $errors);
}
/* This function shows the patient data in tabular form.
 * The function requires a database connection and a
 * query to display.
 * The function returns a formatted table of results */
function show_patient_table ($query) {
    // Table header
    //include ('header.html');
    echo '<table align="center" cellspacing="0" cellpadding="5" width="75%">
<tr>
	<td align="left"><b>Patient Name</b></td>
	<td align="left"><b>Date of Birth</b></td>
	<td align="left"><b>Contact Phone</b></td>

</tr>';
    $bg = '#eeeeee';
    // Populate table
    $r=$query;
    while ($row = @mysqli_fetch_array ($r, MYSQLI_NUM)) {
        $bg = ($bg == '#eeeeee' ? '#ffffff' : '#eeeeee');
        echo '<tr bgcolor="' . $bg . '">
		<td align="left">' . $row[1] . '</td>
		<td align="left">' . $row[2] . '</td>
		<td align="left">' . $row[3] . '</td>
		<td align="left"><a href="editpatient.php?id=' . $row[0] . '">Edit</a></td>
		<td align="left"><a href="deletepatient.php?id=' . $row[0] . '">Delete</a></td>
	</tr>';
    }
    echo '</table>';
}
?>