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

            return $this->getHtmlPlacesRemaining($minTankSpotsLeft, $maxTankSpotsLeft, 'Tank', 'text-warning');
        }

        if ($role === Role::HEAL) {
            $minHealerSpotsLeft = $raid->getMinHeal() - $heal;
            $maxHealerSpotsLeft = $raid->getMaxHeal() - $heal;

            return $this->getHtmlPlacesRemaining($minHealerSpotsLeft, $maxHealerSpotsLeft, 'Healer', 'text-success');
        }

        // $role === Role::DPS
        $accepted = $tank + $heal + $dps;
        $expected = $raid->getExpectedAttendee() + 1;

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

        return $this->getHtmlPlacesRemaining($minDpsSpotsLeft, $maxDpsSpotsLeft, 'DPS', 'text-info');
    }

    private function getHtmlPlacesRemaining($min, $max, $role, $textColor)
    {
        $roles = ($role !== 'DPS') ? $role . 's' : $role;

        // Min and max not reached
        if ($min > 0 && $max > 0) {
            if ($role === 'DPS' && $min === $max) {
                $html = "<span class='$textColor font-weight-bold'>$min</span> $role slots left";
            } else {
                $html = "There are between <span class='$textColor font-weight-bold'>$min</span> 
                and <span class='$textColor font-weight-bold'>$max</span> $role slots left.";
            }
        }

        // Min reached but not max
        if ($min <= 0 && $max > 0) {
            $html = "Minimum amount of $roles reached. <br> ";
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
            $html = "There is no $role slot left.";
        }

        return $html;
    }
}
