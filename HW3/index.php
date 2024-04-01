<?php
require 'form.php';
require 'pass.php';
global $username, $password, $dbname;


$frm = new form();
$errors = $frm->checkForm();
if ($errors === TRUE) {
    echo ' aborted due to mistakes';
    exit();
}

$frm->loadToDB($dbname, $username, $password);

exit();
