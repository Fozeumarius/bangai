<?php
session_start();
session_unset();
session_destroy();

// Redirect to login page (adjust filename if needed)
header("Location: login.php"); 
exit();
?>
