<?php
session_start();
session_destroy(); // Hancurkan gelang/session
header("location:login.php");
?>