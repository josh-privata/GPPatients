<?php /* This page lets the user logout.
 * Uses sessions to control user status.
 */
// Initiate session and global variables
require('functions.inc.php');
session_start();
// Set page title and include header
$page_title = 'Logged Out!';
include('header.html');
// Check is user session exists
if (!isset($_SESSION['username'])) {
    // If not, redirect to home page
    redirect_user("home.php");
    ob_end_clean();
    header("Location:$url");
    exit();
} else {
    // If so, cancel session and display message
    session_unset();
    session_destroy();
    setcookie('PHPSESSID', '', time() - 3600, '/', '', 0, 0); // Destroy the cookie.
    echo "<h1>Logged Out!</h1>
          <p>You are now logged out!</p>";
    include('footer.php');
}
?>