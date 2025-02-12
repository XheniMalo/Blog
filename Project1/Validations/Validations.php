<?php
class Validations
{
    private $errors = [];

    public function required($field, $value)
    {
        if (empty(trim($value))) {
            $this->errors[$field] = ucfirst($field) . " is requried";
        }
    }

    public function passwordValidation($field, $value)
{
    if (strlen($value) < 8) {
        $this->errors[$field] = "Password must be at least 8 characters.";
    } elseif (!preg_match('/[A-Z]/', $value)) {
        $this->errors[$field] = "Password must contain at least one uppercase letter.";
    } elseif (!preg_match('/[\d\W]/', $value)) {
        $this->errors[$field] = "Password must contain at least one number or special character.";
    }
}

    public function email($field, $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = "Invalid email format.";
        }
    }

    public function validateBirthday(string $birthday): bool
    {
        $date = DateTime::createFromFormat('Y-m-d', $birthday);
        if (!$date)
            return false;

        $today = new DateTime();
        return $date <= $today && $today->diff($date)->y <= 120;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function addError($field, $message) {
        $this->errors[$field] = $message;
    }


    public function isValid()
    {
        return empty($this->errors);
    }
}
?>