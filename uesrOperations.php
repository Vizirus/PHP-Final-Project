<?php
    class UserAuthenitication {
        private $password;
        protected $username;
        private $email;

        public function __construct($password, $username, $email) {
            $this->password = $password;
            $this->username = $username;
            $this->email = $email;
        }

        public function registerUser($link) {
            $query = "SELECT * FROM users WHERE username = '{$this->username}'";
            $result = mysqli_query($link, $query);
            if($result){
                echo"<p>User already exists!</p>";
                echo '<a href="login.html">We would like you to go by this link to login form!</a>';
            }
            else{
                $query = "INSERT INTO users (username, password, email) VALUES ('{$this->username}', '{$this->password}', '{$this->email}')";
                $result = mysqli_query($link, $query);
                if($result){
                    echo "<p>User registered successfully!</p>";
                    echo '<a href="login.html">We would like you to go by this link to login form!</a>';
                }
                else{
                    echo "<p>There was an error registering the user.</p>";
                }
            }
        }
        public function loginUser($link) {
            $query = "SELECT * FROM users WHERE username = '{$this->username}' AND password = '{$this->password}'";
            $result = mysqli_query($link, $query);
            if($result && mysqli_num_rows($result) > 0){
                echo "<p>Login successful!</p>";
                setcookie("user", $this->username, time() + (86400 * 30), "/");
            }
            else{
                echo "<p>Invalid username or password.</p>";
            }
        }
    }

?>