<?php 
session_start();
unset($_SESSION['account']);
header('location: home-02.php')
?>