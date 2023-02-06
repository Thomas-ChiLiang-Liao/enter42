<?php
session_start();
session_unset();
session_destroy();
header( "Location: https://$_SERVER[SERVER_NAME]" . dirname($_SERVER['SCRIPT_NAME']) .'/../' );
?>