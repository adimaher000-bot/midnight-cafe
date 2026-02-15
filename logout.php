<?php
// logout.php
require 'includes/functions.php';
start_session_safe();

session_unset();
session_destroy();
redirect('index.php');
?>