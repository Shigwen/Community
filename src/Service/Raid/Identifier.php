<?php

namespace App\Service\Raid;

class Identifier {

	public function __construct()
    {

    }

	public function generate($nbrCharacter)
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ&$%?/!@=+#[]*|';
		$lenght = strlen($characters);
		$randomString = '';

		for ($i = 0; $i < $nbrCharacter ; $i++) {
			$randomString .= $characters[rand(0, $lenght - 1)];
		}

		return $randomString;
	}
}
