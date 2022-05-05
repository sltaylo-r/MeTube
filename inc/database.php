<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

class User {
    public $userId;
    public $username;
    public $firstName;
    public $lastName;
    public $email;
    public $pwd;

    public function __construct(array $row = null) {
        if(!$row) return;

        $this->userId = $row['userid'];
        $this->username = $row['username'];
        $this->firstName = $row['fname'];
        $this->lastName = $row['lname'];
        $this->email = $row['email'];
        $this->pwd = $row['password'];
    }
}

function getUser($userId) {
    global $mysqli;
    $stmt = $mysqli->prepare(
        "SELECT * FROM User 
        WHERE u.userid = ? OR u.username = ?"
    );
    $stmt->bind_param($userid_entry, $username_entry, $firstname_entry, $lastname_entry, $email_entry, $pwd_entry);
    $stmt->execute();

    $res = $stmt->get_result();
    // $row = $res->fetch_assoc();
    $stmt->close();

    // returns all user data if the username/userid matches
    return isset($row) ? new User($row) : null;

}

?>
