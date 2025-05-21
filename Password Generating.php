<?php //This is a PHP class that generates a random password   
class passwordGenerator {
    private $length;
    private $includeSpecialChars;
    private $actionsArray ;
    private $password;

    public function __construct($length = 12, $includeSpecialChars = true) {
        $this->length = $length;
        $this->includeSpecialChars = $includeSpecialChars;
        $this->actionsArray = array(    
        );
    }
    public function appendNewAction($action, $weight) {
        if (method_exists($this, $action)) {
            $this->actionsArray[$action] = $weight;
        } else {
            throw new Exception("Method $action does not exist.");
        }
    }
    public function generatePassword() {
        $this->password = '';
        $randNum = 0;
        while (true){
            $weight = $this->sumAmmounts();
            if ($weight == 0) {
                break;
            }
            $randNum = rand(0, count($this->actionsArray) - 1);
            $action = array_keys($this->actionsArray)[$randNum];
    
            switch ($action) {
                case 'appendLowerCase':
                    $this->appendLowerCase();
                    break;
                case 'appendUpperCase':
                    $this->appendUpperCase();
                    break;
                case 'appendNumbers':
                    $this->appendNumbers();
                    break;
                case 'appendSpecialChars':
                    $this->appendSpecialChars();
                    break;
            }
            $this->actionsArray[$action]--;
            if ($this->actionsArray[$action] == 0) {
                unset($this->actionsArray[$action]);
            }
        }
        return $this->password;
    }
    function sumAmmounts() {
        $sum = 0;
        foreach ($this->actionsArray as $action => $weight) {

            $sum += $weight;
        }
        return $sum;
    }
    function appendLowerCase() {
        $characters = 'abcdefghijklmnopqrstuvwxyz';
        $this->password .= $characters[rand(0, strlen($characters) - 1)];;
        return $this->password;
    }
    function appendUpperCase() {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $this->password .= $characters[rand(0, strlen($characters) - 1)];;
        return $this->password;
    }
    function appendNumbers() {
        $characters = '0123456789';
        $this->password .= $characters[rand(0, strlen($characters) - 1)];;
        return $this->password;
    }
    function appendSpecialChars() {
        $characters = '!@#$%^&*()_+-=[]{}|;:,.<>?';
        $this->password .= $characters[rand(0, strlen($characters) - 1)];;
        return $this->password;
    }
}

?>