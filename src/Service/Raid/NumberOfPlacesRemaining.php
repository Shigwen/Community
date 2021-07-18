<?php

namespace App\Service\Raid;

use App\Entity\Raid;
use App\Entity\Role;
use App\Entity\RaidCharacter;

class NumberOfPlacesRemaining
{
    public function getStatusOfCharacterByPlacesRemaining(Raid $raid, Role $role)
    {
        $minAndMax = $this->calculMinAndMaxRemainingForRole($raid, $role->getId());
        $max = $minAndMax['max'];

        // Max not reached
        if ($max > 0) {
            $status = RaidCharacter::ACCEPT;

            // Max reached
        } else {
            $status = RaidCharacter::WAITING_CONFIRMATION;
        }

        return $status;
    }

    public function getHtmlPlacesRemaining(Raid $raid, int $role)
    {
        $minAndMax = $this->calculMinAndMaxRemainingForRole($raid, $role);
        $min = $minAndMax['min'];
        $max = $minAndMax['max'];

        if ($role === Role::TANK) {
            $roleName = 'Tank';
            $roleNames = 'Tanks';
            $textColor = 'text-warning';
        } elseif ($role === Role::HEAL) {
            $roleName = 'Healer';
            $roleNames = 'Healers';
            $textColor = 'text-success';
        } else {
            $roleName = $roleNames = 'DPS';
            $textColor = 'text-info';
        }

        // Min not reached
        if ($min > 0 && $max > 0) {
            if ($min === $max) {
                $html = "<span class='$textColor font-weight-bold'>$min</span> $roleName slots left";
            } else {
                $html = "There are between <span class='$textColor font-weight-bold'>$min</span>
                and <span class='$textColor font-weight-bold'>$max</span> $roleName slots left.";
            }
        }

        // Min reached but not max
        if ($min <= 0 && $max > 0) {
            $html = "Minimum amount of $roleNames reached. <br> ";
            if ($max !== 1) {
                $html .= "<span class='$textColor font-weight-bold'>$max</span>
                    slots left before reaching the maximum set up.";
            } else {
                $html .= "Only <span class='$textColor font-weight-bold'>1</span>
                    slot left before reaching the maximum set up.";
            }
        }

        // Max reached
        if ($max <= 0) {
            $html = "There is no $roleName slot left.";
        }

        return $html;
    }

    public function calculMinAndMaxRemainingForRole(Raid $raid, $role)
    {
        $tank = 0;
        $heal = 0;
        $dps = 0;

        foreach ($raid->getRaidCharacters() as $raidCharacter) {
            if ($raidCharacter->getUserCharacter()->getUser() === $raid->getUser()) {
                continue;
            }

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

        if ($role === Role::TANK) {
            $minTankSpotsLeft = $raid->getMinTank() - $tank;
            $maxTankSpotsLeft = $raid->getMaxTank() - $tank;

            return ['min' => $minTankSpotsLeft, 'max' => $maxTankSpotsLeft];
        }

        if ($role === Role::HEAL) {
            $minHealerSpotsLeft = $raid->getMinHeal() - $heal;
            $maxHealerSpotsLeft = $raid->getMaxHeal() - $heal;

            return ['min' => $minHealerSpotsLeft, 'max' => $maxHealerSpotsLeft];
        }

        // $role === Role::DPS
        $accepted = $tank + $heal + $dps;
        $expected = $raid->getExpectedAttendee();

        $totalSpotsLeft = $expected - $accepted;

        // Slot for TANKS
        $slotsReservedForTanks = $raid->getMinTank() - $tank;
        $slotsReservedForTanks = ($slotsReservedForTanks < 0) ? 0 : $slotsReservedForTanks;

        $slotsPotentiallyContestedByTanks = $raid->getMaxTank() - $tank;
        $slotsPotentiallyContestedByTanks = ($slotsPotentiallyContestedByTanks < 0) ? 0 : $slotsPotentiallyContestedByTanks;

        // Slot for HEALERS
        $slotsReservedForHealers = $raid->getMinHeal() - $heal;
        $slotsReservedForHealers = ($slotsReservedForHealers < 0) ? 0 : $slotsReservedForHealers;

        $slotsPotentiallyContestedByHealers = $raid->getMaxHeal() - $heal;
        $slotsPotentiallyContestedByHealers = ($slotsPotentiallyContestedByHealers < 0) ? 0 : $slotsPotentiallyContestedByHealers;

        $minDpsSpotsLeft = $totalSpotsLeft - ($slotsPotentiallyContestedByTanks + $slotsPotentiallyContestedByHealers);
        $maxDpsSpotsLeft = $totalSpotsLeft - ($slotsReservedForTanks + $slotsReservedForHealers);

        return ['min' => $minDpsSpotsLeft, 'max' => $maxDpsSpotsLeft];
    }
}
