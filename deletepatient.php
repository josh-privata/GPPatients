<?php /* This page allows a user to delete a patient record.
 * It creates a form and runs a query via functions.in.php
 */
// Initiate session and global variables
require('functions.inc.php');
session_start();
// Check for valid user session
if (!isset($_SESSION['username'])) {
    redirect_user('home.php');
}
// Set page title and include header
$page_title = 'Delete Patient';
include ('header.html');
echo '<h1>Delete a Patient</h1>';
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
// Try to delete patient
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // run query
    list ($check, $data) = delete_patient($dbc, $id);
    // Success
    if ($check) {
        $row=$data;
    // Fail
    } else {
        $errors = $data;
    }
    // Print any error messages
    if (isset($errors) && !empty($errors)) {
        echo '<p class="error">The patient could not be deleted due to the following error(s) :<br />';
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
    // Display form
    print'<form action="deletepatient.php?id=' . $row[7];
    print'" method="post">
        <p>First Name: <input readonly type="text" name="first_name" size="20" maxlength="20" value="' . $row[0];
    print'" /></p>
        <p>Second Name: <input readonly type="text" name="last_name" size="20" maxlength="20" value="' . $row[1];
    print'" /></p>
        <p>Date of Birth: <input readonly type="date" name="date_of_birth" size="20" maxlength="20" value="' . $row[2];
    print'" /></p>
        <p>Sex: <input readonly type="text" name="date_of_birth" size="20" maxlength="20" value="' . $row[3];
    print'" /></p>
        <p>Address: <input readonly type="text" name="address" size="20" maxlength="60" value="' . $row[4];
    print'" /></p>
        <p>City: <input readonly type="text" name="city" size="20" maxlength="20" value="' . $row[5];
    print'" /></p>
        <p>Contact Phone: <input readonly type="text" name="phone" size="20" maxlength="20" value="' . $row[6];
    print'" /></p>
        <p><input type="submit" name="delete" value="Delete" />
        <a href="home.php">
            <input type="button" name="cancel" value="Cancel" /></a></p>
    </form>';
// Fail
} else {
    $errors = $data;
}
include ('footer.php');
?>