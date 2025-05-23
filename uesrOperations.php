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
                echo"<p>User already exists!</p>";
                return false;
            }
            else{
                $hashedPassword = password_hash($this->password, PASSWORD_BCRYPT);
                $query = "INSERT INTO usertable (UserName, UserPass, UserEmail) VALUES ('{$this->username}', '{$hashedPassword}', '{$this->email}')";
                $result = mysqli_query($link, $query);
                if($result){
                    echo "<p>User registered successfully!</p>";
                }
                else{
                    echo "<p>There was an error registering the user.</p>";
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
                    echo "<p>Login successful!</p>";
                }
                else{
                    echo "<p>Invalid password.</p>";
                    return false;
                }
                setcookie("user", $this->username, time() + (86400 * 30), "/");
            }
            else{
                echo "<p>Invalid username or password.</p>";
                return false;
            }
            return true;
        }
    }

?>