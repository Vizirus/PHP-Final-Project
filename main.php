<?php
require_once 'Password Generating.php';
require_once 'encryption.php';
require_once 'uesrOperations.php';
$link = mysqli_connect('localhost', 'root', '', "finalprojectdatabase");
$password = '';
$encoder = new AESEncryption();
$userClass = new UserAuthenitication();
function generatePassword($encoder, $link, $isUpdate = false) {
    $ServiceName =  $_POST['servicename'];
    $length = isset($_POST['length']) ? (int)$_POST['length'] : 12;
    $includeUppercase = isset($_POST['uppercase']);
    $includeLowercase = isset($_POST['lowercase']);
    $includeNumbers = isset($_POST['numberBox']);
    $includeSymbols = isset($_POST['symbols']);
    $amountOfUppercase = (int)$_POST['includeUpperCase'];
    $amountOfLowercase = (int)$_POST['amountOfLowerCase'];
    $amountOfNumbers = (int)$_POST['amountOfNumbers'];
    $amountOfSymbols = (int)$_POST['amountOfSymbols'];
    $generator = new passwordGenerator($length, $includeSymbols);
       
    if($amountOfLowercase == 0 && $amountOfUppercase == 0 && $amountOfNumbers == 0 && $amountOfSymbols == 0) {
        echo "<p>Please select at least one character type.</p>";
        exit;
    }
    else if ($length < 1) {
        echo "<p>Please enter a valid length.</p>";
        exit;
    }
    else if ($length > 100) {
        echo "<p>Please enter a length less than 100.</p>";
        exit;
    }
    else if ($amountOfUppercase < 0 || $amountOfLowercase < 0 || $amountOfNumbers < 0 || $amountOfSymbols < 0) {
        echo "<p>Please enter a valid amount for each character type.</p>";
        exit;
    }
    else if ($amountOfUppercase + $amountOfLowercase + $amountOfNumbers + $amountOfSymbols > $length) {
        echo "<p>The total amount of characters exceeds the specified length.</p>";
        exit;
    }
    else if ($amountOfUppercase + $amountOfLowercase + $amountOfNumbers + $amountOfSymbols < $length) {
        echo "<p>The total amount of characters is less than the specified length.</p>";
        exit;
    }
    else if($includeUppercase || $includeLowercase || $includeNumbers || $includeSymbols){
        echo "<p>Generating a password with the following parameters:</p>";
        echo "<ul>";
        echo "<li>Length: $length</li>";
        echo "<li>Uppercase letters: $amountOfUppercase</li>";
        echo "<li>Lowercase letters: $amountOfLowercase</li>";
        echo "<li>Numbers: $amountOfNumbers</li>";
        echo "<li>Special characters: $amountOfSymbols</li>";
        echo "</ul>";
        if ($includeUppercase) {
            $generator->appendNewAction('appendUpperCase', $amountOfUppercase);
        }
        if ($includeLowercase) {
            $generator->appendNewAction('appendLowerCase', $amountOfLowercase);
        }
        if ($includeNumbers) {
            $generator->appendNewAction('appendNumbers', $amountOfNumbers);
        }
        if ($includeSymbols) {
            $generator->appendNewAction('appendSpecialChars', $amountOfSymbols);
        }
        $password = $generator->generatePassword();
        $result = $encoder->encrypt($password);
        echo "<p>Generated password: $result</p>";
        $username = '';
        if(isset($_COOKIE['user'])) {
            $username = $_COOKIE['user'];
            if($isUpdate) {
                $queryResult = mysqli_query($link, "UPDATE passwordtable SET UserPassword = '{$result}', PassKey = '{$encoder->key}' WHERE UserService = '{$ServiceName}'");
            } else {
                $queryResult = mysqli_query($link, "INSERT INTO passwordtable (UserService, UserPassword, PassKey, UserName) VALUES ('{$ServiceName}', '{$result}', '{$encoder->key}', '{$username}'); ");
            }
            if($queryResult)
            {
                echo "<p>The password have been successfuly added/modified to the data base!</p>";
            }
            else{
                echo "<p>There was an error adding the password to the database.</p>";
                exit;
            }
        }
        else{
            echo "<p>Cookie not set.</p>";
            exit;
        }
    }
    else{
        echo "<p>Please select at least one character type.</p>";
        exit;
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formType = $_POST['form_type'];
    echo "<p>Form Type: $formType</p>";
    if($formType == 'login') {
        $cookie_name = "user";
        $cookie_value = "$userClass->username";
        setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/");
        $username = $_POST['username'];
        $password = $_POST['password'];
        $userClass->setUser($username, $password, "");
        if($userClass->loginUser($link)){
            echo "<div>
                <p>The user was logged in successfully!</p>
                <div>
                    <p>You can now generate a password!</p>
                    <a href='html/form.html'>Go to the password generation form</a>
                </div>
                <div>
                    <p>Or you can view your passwords!</p>
                    <a href='html/viewItems.html'>Go to the view passwords form</a>
                </div>
            </div>";
        }
    }
    else if($formType == 'generate') {
        generatePassword($encoder, $link, false);
        echo "
            <div>
                <p>Now you can view your passwords!</p>
                <a href='html/viewItems.html'>Go to the view passwords form</a>
            </div>";
    }
    else if($formType == 'viewItems') {
        $query = "SELECT * FROM passwordtable";
        $result = mysqli_query($link, $query);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $decryptedPassword = $encoder->decrypt($row['UserPassword'], $row['PassKey']);
                echo "<p>Service: {$row['UserService']}, Password: {$decryptedPassword}></p>";
            }
        } else {
            echo "<p>Error retrieving passwords from the database.</p>";
        }
    }
    else if($formType == 'create'){
        $username = $_POST['username'];
        $password = $_POST['password'];
        $email = $_POST['email'];
        $userClass->setUser($username, $password, $email);
        $result = $userClass->registerUser($link);
        if($result == true);
        {
            echo "<div>
                <p>The user was created successfully!</p>
                <div>
                    <p>Now you have to login in order to access the functionality!</p>
                    <a href='html/login.html'>Go to the login form</a>
                </div>
            </div>";
        }

    }
    else if($formType == 'changeItems'){
        generatePassword($encoder, $link, true);
        echo "
            <div>
                <p>Now you can view your passwords!</p>
                <a href='html/viewItems.html'>Go to the view passwords form</a>
            </div>";
    }
}
?>