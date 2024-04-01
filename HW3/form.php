<?php
require 'pass.php';
global $_POST;
class form
{
    private $fio;
    private $phone;
    private $email;
    private $birthdate;
    private $gender;
    private $bio;
    private $langs;
    private $langs_check = ['c', 'c++', 'js', 'java', 'clojure', 'pascal', 'python', 'haskel', 'scala', 'php', 'prolog'];

    function __construct() {
        $this->fio = $_POST['fio'];
        $this->phone = $_POST['phone'];
        $this->email = $_POST['email'];
        $this->birthdate = $_POST['birthdate'];
        $this->gender = $_POST['gender'];
        $this->bio = $_POST['bio'];
        $this->langs = $_POST['progLang'];
    }
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

    function loadToDB($dbname, $username, $password) {
        global $servername;
        $fio = $this->getFio();
        $phone = $this->getPhone();
        $email = $this->getEmail();
        $birthdate = $this->getBirthdate();
        $gender = $this->getGender();
        $bio = $this->getBio();
        $langs = $this->getLangs();
        $langs_check = $this->getLangsCheck();
        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "Connected successfully ";

            $sql = "INSERT INTO request (fio, phone, email, birthdate, gender, bio) 
        VALUES (:fio, :phone, :email, :birthdate, :gender, :bio)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':fio', $fio);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':birthdate', $birthdate);
            $stmt->bindParam(':gender', $gender);
            $stmt->bindParam(':bio', $bio);
            $stmt->execute();
            $lastId = $conn->lastInsertId();

            for ($i = 0; $i < count($langs); $i++) {
                $sql = "SELECT lang_id FROM progLang WHERE lang_name = :langName";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':langName', $langs[$i]);
                $stmt->execute();
                $result = $stmt->fetch();
                $lang_id = $result['lang_id'];

                $sql = "INSERT INTO requestToLang (id, lang_id) VALUES (:lastId, :langId)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':lastId', $lastId);
                $stmt->bindParam(':langId', $lang_id);
                $stmt->execute();
            }

            echo nl2br("\nNew record created successfully");
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
        $conn = null;
    }

    function checkForm() {
        $fio = $this->getFio();
        $phone = $this->getPhone();
        $email = $this->getEmail();
        $birthdate = $this->getBirthdate();
        $gender = $this->getGender();
        $bio = $this->getBio();
        $langs = $this->getLangs();
        $langs_check = $this->getLangsCheck();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo 'This script only works with POST queries';
            exit();
        }

        $errors = FALSE;

        if (empty($fio) || !preg_match('/^[A-Za-z]+$/', $fio) || strlen($fio) >= 100) {
            $errors = TRUE;
            echo nl2br("fio doesn't match the conditions\n");
        }

        if (empty($phone) || !preg_match('/^[0-9+]+$/', $phone) || (strlen($phone)!= 11 && strlen($phone)!= 12)) {
            $errors = TRUE;
            echo nl2br(" phone doesn't match the conditions\n");
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors = TRUE;
            echo nl2br(" email doesn't match the conditions\n");
        }


        $dateObject = DateTime::createFromFormat('Y-m-d', $birthdate);
        if ($dateObject === false || $dateObject->format('Y-m-d') !== $birthdate) {
            $errors = TRUE;
            echo nl2br(" birthdate doesn't match the conditions\n");
        }

        if ($gender != 'male' && $gender != 'female') {
            $errors = TRUE;
            echo nl2br(" gender doesn't match the conditions\n");
        }

        if (!$this->checkLangs($langs, $langs_check)) {
            $errors = TRUE;
            echo nl2br(" langs do not match the conditions\n");
        }

        if (empty($bio) || preg_match("/<[^>]*>/", $bio)) {
            $errors = TRUE;
            echo nl2br(" bio shouldn't contain html tags\n");
        }

        if ($errors === TRUE) {
            echo ' aborted due to mistakes';
            exit();
        }
        return $errors;
    }

    /**
     * @return mixed
     */
    public function getFio()
    {
        return $this->fio;
    }

    /**
     * @param mixed $fio
     */
    public function setFio($fio)
    {
        $this->fio = $fio;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getBirthdate()
    {
        return $this->birthdate;
    }

    /**
     * @param mixed $birthdate
     */
    public function setBirthdate($birthdate)
    {
        $this->birthdate = $birthdate;
    }

    /**
     * @return mixed
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param mixed $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * @return mixed
     */
    public function getBio()
    {
        return $this->bio;
    }

    /**
     * @param mixed $bio
     */
    public function setBio($bio)
    {
        $this->bio = $bio;
    }

    /**
     * @return mixed
     */
    public function getLangs()
    {
        return $this->langs;
    }

    /**
     * @param mixed $langs
     */
    public function setLangs($langs)
    {
        $this->langs = $langs;
    }

    /**
     * @return string[]
     */
    public function getLangsCheck()
    {
        return $this->langs_check;
    }

    /**
     * @param string[] $langs_check
     */
    public function setLangsCheck($langs_check)
    {
        $this->langs_check = $langs_check;
    }


}

