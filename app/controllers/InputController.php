<?php

# Le but de cette classe est de fournir des fonctions qui permettent la validation des entrées utilisateurs

class InputController
{
        
    // Email validation
    public static function valide_mail($email)
    {   
        if(!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    # Password complexity validation des contraintes de complexités
    public static function valide_password($password)
    {   
        $minLength = 8;
        $maxLength = 25;
    
        # Verify password has at least one letter
        $containsLetter = preg_match('/[a-zA-Z]/', $password);

        # Verify password has at least one digit
        $containsDigit = preg_match('/[0-9]/', $password);
    
        # Verify password has at least one special char (not letter, not digit)
        $containsSpecialChar = preg_match('/[^a-zA-Z0-9]/', $password);
    
        #  Verify length, letter ,digit and special char
        if (strlen($password) < $minLength || strlen($password) > $maxLength || !$containsLetter || !$containsSpecialChar || !$containsDigit)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    # Same passwords validation
    public static function confirm_password($password,$confirmPassword)
    {   
        if($password !== $confirmPassword)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    // folder name validation
    public static function valide_folder_name($foldername)
    {   
        // folder name length
        if (strlen($foldername) > 50) {
            return false;
        }

    // Allowed char validation ( min,maj,-_ and prominent characters utf-8)
        if (preg_match('/^[a-zA-Z \p{L}0-9-_]+$/u', $foldername)) 
        {
            return true;
        } 
        else
        {
            return false;
        }
    }

    # return false if empty string, else return string without invisibles specials htlm characters
    public static function cleanInput($data): false|string {
        if (!isset($data) || empty($data)){
            return false ;
    }
        return htmlspecialchars(trim($data));
    }


}