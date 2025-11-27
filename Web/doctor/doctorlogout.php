<?php
session_start();
session_destroy();
header("Location: doctorlogin.php");
exit;