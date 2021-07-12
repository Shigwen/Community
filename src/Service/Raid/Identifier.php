<?php

namespace App\Service\Raid;

class Identifier
{
    public function generate($nbrCharacter, $number = true, $specialCharacters = true)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        if ($number) {
            $characters .= '0123456789';
        }

        if ($specialCharacters) {
            $characters .= '&$%?/!@=+#[]*|';
        }

        $lenght = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $nbrCharacter; $i++) {
            $randomString .= $characters[rand(0, $lenght - 1)];
        }

        return $randomString;
    }
}
