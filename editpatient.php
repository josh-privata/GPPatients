<?php /* This page allows a user to edit a patient record.
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
$page_title = 'Edit Patient';
include ('header.html');
echo '<h1>Edit Patient Details</h1>';
// Check for a valid patient ID
if ( (isset($_GET['id'])) && (is_numeric($_GET['id'])) ) {
    $id = $_GET['id'];
} elseif ( (isset($_POST['id'])) && (is_numeric($_POST['id'])) ) {
    $id = $_POST['id'];
} else {
    echo '<p class="error">This page has been accessed in error.</p>';
    include ('footer.php');
    exit();
}
// Try to update patient
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // run query
    list ($check, $data) = update_patient($dbc, $_POST['first_name'], $_POST['last_name'],
                                             $_POST['date_of_birth'], $_POST['sex'],
                                             $_POST['address'], $_POST['city'],
                                             $_POST['phone'], $id);
    // Success
    if ($check) {
        echo '<p>The patient has been edited.</p>';
        $row=$data;
    // Fail
    } else {
        $errors = $data;
    }
    // Print any error messages
    if (isset($errors) && !empty($errors)) {
        echo '<p class="error">The patient could not be edited due to the following error(s) :<br />';
        foreach ($errors as $msg) {
            echo " - $msg<br />\n";
        }
        echo '</p><p><i>Please try again.</i></p>';
    }
}
// Retrieve patient
list ($check, $data) = get_patient_by_id($dbc, $id);
// Success
if ($check) {
    $row=$data;
// Fail
} else {
    $errors = $data;
}
    // Display form
    print'<form action="editpatient.php?id=' . $row[7] . '" method="post">
        <p>First Name: <input type="text" name="first_name" size="20" maxlength="20" value="' . $row[0];
    print'" /></p>
        <p>Second Name: <input type="text" name="last_name" size="20" maxlength="20" value="' . $row[1];
    print'" /></p>
        <p>Date of Birth: <input type="date" name="date_of_birth" size="20" maxlength="20" value="' . $row[2];
    print'" /></p>
        <p>Sex: <select name="sex">
		<option value="M"';
    if ($row[3] == 'M' OR $row[3] == 'm') echo' selected="selected"';
    print'/> M
        <option value="F"';
    if ($row[3] == 'F' OR $row[3] == 'f') echo' selected="selected"';
    print'/> F';
    print'</select></p>
        <p>Address: <input type="text" name="address" size="20" maxlength="60" value="' . $row[4];
        print'" /></p>
        <p>City: <input type="text" name="city" size="20" maxlength="20" value="' . $row[5];
        print'" /></p>
        <p>Contact Phone: <input type="text" name="phone" size="20" maxlength="20" value="' . $row[6];
        print'" /></p>
        <p><input type="submit" name="update" value="Update" />
        <a href="home.php">
            <input type="button" name="cancel" value="Cancel" /></a></p>
    </form>';
include ('footer.php');
?>