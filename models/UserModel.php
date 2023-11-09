<?php
require_once "/Applications/XAMPP/xamppfiles/htdocs/models/db.php";
require "/Applications/XAMPP/xamppfiles/htdocs/inc/bootstrap.php";

class UserModel extends Database
{
    public function getUserByUsername($username) {
        return $this->select("SELECT username, password FROM users WHERE username = ?", ["s", $username]);
    }
    public function registerAction($userid , $hashed_password) {
        $sql = "INSERT INTO users (username, password) VALUES (?,?)";
        $stmt = mysqli_prepare($this->connection, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $userid, $hashed_password);
        try {
            return (mysqli_stmt_execute($stmt) === TRUE) ;
        }
        catch (Exception $e) {
            return FALSE;
        }
    }
}
