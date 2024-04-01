<?php
require 'form.php';
$servername = "localhost";
$username = "u67283";
$password = "5460525";
$dbname = "u67283";

$frm = new form($_POST);
$errors = $frm->checkForm();
if ($errors === TRUE) {
    echo ' aborted due to mistakes';
    exit();
}

$frm->loadToDB($dbname, $username, $password);


?> 