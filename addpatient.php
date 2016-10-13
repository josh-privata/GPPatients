<?php /* This page allows a user to add a patient record.
  * It creates a form and runs a query via functions.in.php.
  * Form validations are performed.
  */
// Initiate session and global variables
require('functions.inc.php');
session_start();
// Check for valid user session
if (!isset($_SESSION['username'])) {
    redirect_user('home.php');
}
// Set page title and include header
$page_title = 'Add Patient';
include ('header.html');
echo '<h1>Add New Patient</h1>';
// Try to add new patient
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // run query
    list ($check, $data) = add_patient($dbc, $_POST['first_name'], $_POST['last_name'],
        $_POST['date_of_birth'], $_POST['sex'],
        $_POST['address'], $_POST['city'],
        $_POST['phone']);
    // Success
    if ($check) {
        echo '<p>The patient has been added.</p>';
       $row=$data;
    // Fail
    } else {
        $errors = $data;
        }
    // Print any error messages
    if (isset($errors) && !empty($errors)) {
        echo '<p class="error">The patient could not be added due to the following error(s) :<br />';
        foreach ($errors as $msg) {
            echo " - $msg<br />\n";
        }
        echo '</p><p><i>Please try again.</i></p>';
    }
}
    // Display form
    print'<form action="addpatient.php" method="post">
        <p>First Name: <input type="text" name="first_name" size="20" maxlength="20"/></p>
        <p>Second Name: <input type="text" name="last_name" size="20" maxlength="20"/></p>
        <p>Date of Birth: <input type="date" name="date_of_birth" size="20" maxlength="20"/></p>
		<p>Sex:	<select name="sex">
		    <option value="M"/> M
            <option value="F"/> F
        </select></p>
        <p>Address: <input type="text" name="address" size="20" maxlength="60"/></p>
        <p>City: <input type="text" name="city" size="20" maxlength="20"/></p>
        <p>Contact Phone: <input type="text" name="phone" size="20"/></p>
        <p><input type="submit" name="add" value="Add" />
        <a href="home.php">
            <input type="button" name="cancel" value="Cancel" /></a></p>
    </form>';
include ('footer.php');
?>