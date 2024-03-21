<?php
$servername = "localhost";
$username = "u67283";
$password = "5460525";
$dbname = "u67283";


$fio = $phone = $email = $birthdate = $gender = '';
$fio = $_POST['fio'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$birthdate = $_POST['birthdate'];
$gender = $_POST['gender'];
$bio = $_POST['bio'];
$langs = $_POST['progLang'];
$langs_check = ['c', 'c++', 'js', 'java', 'clojure', 'pascal', 'python', 'haskel', 'scala', 'php', 'prolog'];
function checkLangs($langs, $langs_check) {
    for ($i = 0; $i < count($langs); $i++) {
        $isTrue = FALSE;
        for ($j = 0; $j < count($langs_check); $j++) {
            if ($langs[$i] === $langs_check[$j]) {
                $isTrue = TRUE;
                break;
            }
        }
        if ($isTrue === FALSE) return FALSE;
    }
    return TRUE;
}

// ? почему ошибки до сих пор на другой странице
// ? как запентестить свою валидацию
// ? что если htmlspecialchars убрать вручную

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo 'This script only works with POST queries';
    exit();
}

$errors = FALSE;

if (empty($fio) || !preg_match('/^[A-Za-z]+$/', $fio)) {
    $errors = TRUE;
}

if (empty($phone) || !preg_match('/^[0-9+]+$/', $phone)) {
    $errors = TRUE;
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors = TRUE;
}


$dateObject = DateTime::createFromFormat('Y-m-d', $birthdate);
if ($dateObject === false || $dateObject->format('Y-m-d') !== $birthdate) {
    $errors = TRUE;
}

if ($gender != 'male' && $gender != 'female') {
    $errors = TRUE;
}

if (!checkLangs($langs, $langs_check)) {
    $errors = TRUE;
}

if ($errors === TRUE) {
    echo 'mistake';
    exit();
}

try {
    $conn = new PDO("mysql:host=localhost;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully ";
    $sql = "INSERT INTO request (fio, phone, email, birthdate, gender, bio) 
VALUES ('$fio', '$phone', '$email', '$birthdate', '$gender', '$bio')";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $lastId = $conn->lastInsertId();

    for ($i = 0; $i < count($langs); $i++) {
        $sql = "SELECT lang_id FROM progLang WHERE lang_name = :langName";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':langName', $langs[$i]);
        $stmt->execute();
        $result = $stmt->fetch();
        $lang_id = $result['lang_id'];
        $sql = "INSERT INTO requestToLang (id, lang_id) VALUES ($lastId, $lang_id)";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
    }
    echo nl2br("\nNew record created successfully");
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
$conn = null;
?> 
