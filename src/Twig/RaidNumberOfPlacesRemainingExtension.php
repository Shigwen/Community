<?php
namespace App\Twig;

use App\Entity\Raid;
use App\Entity\Role;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RaidNumberOfPlacesRemainingExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('number_of_places_remaining', [$this, 'numberOfPlacesRemaining']),
        ];
    }

    public function numberOfPlacesRemaining(Raid $raid, int $role)
    {
		$tank = 0;
		$heal = 0;
		$dps = 0;

		foreach ($raid->getRaidCharacters() as $raidCharacter) {
			if ($raidCharacter->getRole()->isTank()) {
				$tank++;
			} else if ($raidCharacter->getRole()->isHeal()) {
				$heal++;
			} else {
				$dps++;
			}
		}

		if ($role === Role::TANK) {
			$placesRemaining =  $raid->getMaxTank() - $tank;
		} else if ($role === Role::HEAL) {
			$placesRemaining =  $raid->getMaxTank() - $heal;
		} else {
			$maxDps = $raid->getExpectedAttendee() - ($raid->getMaxTank() + $raid->getMinHeal());
			$placesRemaining = $maxDps - $dps;
		}

        return $placesRemaining;
    }
}
