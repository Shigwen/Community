<?php
namespace App\Twig;

use App\Entity\Raid;
use App\Entity\RaidCharacter;
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
			if ($raidCharacter->isAccept()) {
				if ($raidCharacter->getRole()->isTank()) {
					$tank++;
				} else if ($raidCharacter->getRole()->isHeal()) {
					$heal++;
				} else {
					$dps++;
				}
			}
		}

		//	Raid. Maximum attendees : 10.     Max de tanks : 3.        Min Tank : 2.    En ce moment : 2 tanks.

		if ($role === Role::TANK) {
			$maxTankSpotsLeft = $raid->getMaxTank() - $tank; // 3 - 1 = 2
			$minTankSpotsLeft = $raid->getMinTank() - $tank; // 2 - 1 = 1

			if ($maxTankSpotsLeft <= 0) {
				$placesRemaining = "There is no Tank spot left.";
			}

			if ($maxTankSpotsLeft > $minTankSpotsLeft && $minTankSpotsLeft === 0 && $maxTankSpotsLeft > 1) {
				$placesRemaining = "Minimum amount of Tanks reached. $maxTankSpotsLeft spots left before reaching the maximum set up.";
			}

			if ($maxTankSpotsLeft > $minTankSpotsLeft && $maxTankSpotsLeft === 1) {
				$placesRemaining = "Minimum amount of Tanks reached. Only 1 slot left before reaching the maximum set up.";
			}

			if ($maxTankSpotsLeft === $minTankSpotsLeft && $minTankSpotsLeft === 1) {
			$placesRemaining = "Only 1 Tank spot left.";
			}

			if ($maxTankSpotsLeft === $minTankSpotsLeft && $minTankSpotsLeft > 1) {
			$placesRemaining = "$minTankSpotsLeft Tank spots left.";
			}

			if ($maxTankSpotsLeft > $minTankSpotsLeft && $minTankSpotsLeft >= 1) {
			$placesRemaining = "There are between $minTankSpotsLeft and $maxTankSpotsLeft Tank spots left.";
			}

			// dump($minTankSpotsLeft);
			// dump($maxTankSpotsLeft);
			// die();

		} else if ($role === Role::HEAL) {
			$maxHealerSpotsLeft = $raid->getMaxHeal() - $heal;
			$minHealerSpotsLeft = $raid->getMaxHeal() - $heal;

			if ($maxHealerSpotsLeft <= 0) {
				$placesRemaining = "There is no Healer spot left.";
			}

			if ($maxHealerSpotsLeft > $minHealerSpotsLeft && $minHealerSpotsLeft === 0 && $maxHealerSpotsLeft > 1) {
				$placesRemaining = "Minimum amount of Healers reached. $maxHealerSpotsLeft spots left before reaching the maximum set up.";
			}

			if ($maxHealerSpotsLeft > $minHealerSpotsLeft && $maxHealerSpotsLeft === 1) {
				$placesRemaining = "Minimum amount of Healers reached. Only 1 slot left before reaching the maximum set up.";
			}
			if ($maxHealerSpotsLeft === $minHealerSpotsLeft && $minHealerSpotsLeft === 1) {
			$placesRemaining = "Only 1 Healer spot left.";
			}
			if ($maxHealerSpotsLeft === $minHealerSpotsLeft && $minHealerSpotsLeft > 1) {
			$placesRemaining = "$minHealerSpotsLeft Healer spots left.";
			}
			if ($maxHealerSpotsLeft > $minHealerSpotsLeft && $minHealerSpotsLeft >= 1) {
			$placesRemaining = "There are between $minHealerSpotsLeft and $maxHealerSpotsLeft Healer spots left.";
			}

		} else {
			$attendeesAlreadyAccepted = $tank + $heal + $dps;   // 5 raiders acceptés
			$totalSpotsLeft = $raid->getExpectedAttendee() - $attendeesAlreadyAccepted + 1; // 10 - 5 = 5 places restantes (tout confondu).

			// S'il faut encore des tanks, on doit leur réserver leur place.
			$slotsReservedForTanks = $raid->getMinTank() - $tank;
			// Si le résultat est supérieur à 0, c'est qu'on manquait de tanks pour le minimum.
			// Si on avait plus de tanks que le minimum, on va réduire ce résultat à 0 pour ne pas avoir un chiffre négatif.
			if ($slotsReservedForTanks < 0) {
				$slotsReservedForTanks = 0;
			}
			//Maintenant, pareil pour les healers.
			$slotsReservedForHealers = $raid->getMinHeal() - $heal;

			if ($slotsReservedForHealers < 0) {
				$slotsReservedForHealers = 0;
			}

			// Nous avons les places restantes dans le raid tout confondu, et nos places à réserver au minimum :
			$maxDPSSpotsLeft = $totalSpotsLeft - ($slotsReservedForTanks + $slotsReservedForHealers); // 5 - (X + X) = ?

			// On calcule maintenant les slots qui seront "peut-être" pris par un autre tank ou healer :
			$slotsPotentiallyContestedByTanks = $raid->getMaxTank() - $tank;

			if ($slotsPotentiallyContestedByTanks < 0) {
				$slotsPotentiallyContestedByTanks = 0;
			}

			$slotsPotentiallyContestedByHealers = $raid->getMaxHeal() - $tank;

			if ($slotsPotentiallyContestedByHealers < 0) {
				$slotsPotentiallyContestedByHealers = 0;
			}

			// Même si ces slots étaient pris par un tank ou un healer, il resterait quand même :
			$minDPSSpotsLeft = $totalSpotsLeft - ($slotsPotentiallyContestedByTanks + $slotsPotentiallyContestedByHealers); // 10 - 3 - 3 = 4

			if ($maxDPSSpotsLeft <= 0) {
				$placesRemaining = "There is no DPS spot left.";
			}

			if ($maxDPSSpotsLeft > $minDPSSpotsLeft && $minDPSSpotsLeft === 0 && $maxDPSSpotsLeft > 1) {
				$placesRemaining = "Minimum amount of DPS reached. $maxDPSSpotsLeft spots left before reaching the maximum set up.";
			}

			if ($maxDPSSpotsLeft > $minDPSSpotsLeft && $maxDPSSpotsLeft === 1) {
				$placesRemaining = "Minimum amount of DPS reached. Only 1 slot left before reaching the maximum set up.";
			}
			if ($maxDPSSpotsLeft === $minDPSSpotsLeft && $minDPSSpotsLeft === 1) {
				$placesRemaining = "Only 1 DPS spot left.";
			}

			if ($maxDPSSpotsLeft === $minDPSSpotsLeft && $minDPSSpotsLeft > 1) {
			$placesRemaining = "$minDPSSpotsLeft DPS spots left.";
			}

			if ($maxDPSSpotsLeft > $minDPSSpotsLeft && $minDPSSpotsLeft >= 1) {
			$placesRemaining = "There are between $minDPSSpotsLeft and $maxDPSSpotsLeft DPS spots left.";
			}
		}

        return $placesRemaining;
    }
}