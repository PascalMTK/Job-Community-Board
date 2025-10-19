<?php
//destroying all the current sessions data and then redirecting to the home page
session_start();

session_destroy();

header("Location: home.php");
exit();
?>
