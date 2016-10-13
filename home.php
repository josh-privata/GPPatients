<!-- js/php script to hide GET parameters in URL bar. Added for neatness -->
<script>
    if(typeof window.history.pushState == 'function') {
        window.history.pushState({}, "Hide", '<?php echo $_SERVER['PHP_SELF'];?>');
    }
</script>
<?php /* This file generates the default page for the website.
 * It checks whether a user is logged in using session data. If
 * a user has not logged in the log on page will be shown otherwise
 * the user will be shown search option and the patient data table.
 */
// Initiate session and global variables
require('functions.inc.php');
session_start();
$ermsg = '';
$lastname = NULL;
// Authenticate login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    list ($check, $data) = check_login($dbc, $_POST['username'], $_POST['pass']);
    if ($check) {
        // If okay, set the session data:
        $_SESSION['username'] = $data[0];
        $_SESSION['agent'] = md5($_SERVER['HTTP_USER_AGENT']);
    } else {
        // If not okay, assign errors to string
        $errors = $data;
        if (!empty($errors)) {
            $ermsg = '<p class="error">The following error(s) occurred :</p> ';
            foreach ($errors as $msg) {
                $ermsg .= '<p class="error"> - ' . $msg . '</p> ';
            }
        }
    }
}
// Create page
/* If user has not logged on
 * Set page title, show header, print error messages and display login form
 */
if (!isset($_SESSION['username'])) {
    $page_title = 'Login';
    include('header.html');
    print'<h1>Login</h1>';
    // Print error messages
    if ($ermsg) {
        print $ermsg;
    }
    login();
}
/* If the user has logged on
 * Set page title, show header, welcome message and patient table
 */
else {
    $page_title = 'Logged In';
    include('header.html');
    echo "<p><h3>Welcome, {$_SESSION['username']}!</h3></p>";
    // Show search (search uses GET)
    print'<form action="home.php" method="GET">';
    print'<p>Patient Last Name Search : <input type="text" name="lastname" size="20" maxlength="60" /> ';
    print'<input type="submit" value="Search"/>';
    ?>
    <!--    Show results of search otherwise show all patients-->
    <?php // Hides undefined 'lastname' notice
    error_reporting(0);
    $q = getall_patients($dbc);
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        // Check for search query sent via GET
        if ($_GET['lastname']) {
            $lastname = "'" . sanitise_input($dbc,$_GET['lastname']) . "'";
            echo'<input type="submit" value="Clear results"/>';
            // No results found
            if (!count(@mysqli_fetch_array(get_patient_by_lastname($dbc, $lastname), MYSQLI_NUM))) {
                echo '</p></form><p><h3 class="error">No patients found. Please check your spelling and try again</h3></p>';
                include('footer.php');
                exit;
            } // Results found
            else {
                $q = get_patient_by_lastname($dbc, $lastname);
            }
        }
    }
    echo'</p></form>';
    show_patient_table($q);
    include('footer.php');
}
?>