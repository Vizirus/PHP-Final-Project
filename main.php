<?php
require_once 'Password Generating.php';

$password = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $length = isset($_POST['length']) ? (int)$_POST['length'] : 12;
    $includeUppercase = isset($_POST['uppercase']);
    $includeLowercase = isset($_POST['lowercase']);
    $includeNumbers = isset($_POST['numbers']);
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
        echo "<p>Your generated password is: <strong>$password</strong></p>";
    }
    else{
        echo "<p>Please select at least one character type.</p>";
        exit;
    }
    
}
?>