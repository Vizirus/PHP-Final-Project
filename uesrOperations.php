<?php
    class UserAuthenitication {
        private $password;
        public $username;
        private $email;
        public function setUser($username, $password, $email) {
            $this->username = $username;
            $this->password = $password;
            $this->email = $email;
        }
        public function registerUser($link) {
            $query = "SELECT * FROM usertable WHERE UserName = '{$this->username}'";
            $result = mysqli_query($link, $query);
            if(!$result){
                echo"<div><p>User already exists!</p></div>";
                return false;
            }
            else{
                $hashedPassword = password_hash($this->password, PASSWORD_BCRYPT);
                $query = "INSERT INTO usertable (UserName, UserPass, UserEmail) VALUES ('{$this->username}', '{$hashedPassword}', '{$this->email}')";
                $result = mysqli_query($link, $query);
                if($result){
                    echo "<div><p>User registered successfully!</p></div>";
                }
                else{
                    echo "<div><p>There was an error registering the user.</p></div>";
                }
                return true;
    
            }      
        }
        public function loginUser($link) {
            $query = "SELECT UserPass FROM usertable WHERE UserName = '{$this->username}'";
            $result = mysqli_query($link, $query);
            if($result && mysqli_num_rows($result) > 0){
                $getPassword = mysqli_fetch_assoc($result)['UserPass'];
                if(password_verify($this->password, $getPassword)){
                    echo "<div><p>Login successful!</p></div>";
                }
                else{
                    echo "<div><p>Invalid password.</p></div>";
                    return false;
                }
                setcookie("user", $this->username, time() + (86400 * 30), "/");
            }
            else{
                echo "<div><p>Invalid username or password.</p></div>";
                return false;
            }
            return true;
        }
    }

?>