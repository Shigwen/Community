<?php

namespace App\Twig;

use App\Entity\Raid;
use App\Entity\User;
use Twig\TwigFunction;
use App\Entity\RaidCharacter;
use DateTimeZone;
use Twig\Extension\AbstractExtension;

class RaidDateWithTimezoneExtension extends AbstractExtension
{

    public function getFunctions()
    {
        return [
            new TwigFunction('get_date', [$this, 'getDate']),
        ];
    }

    public function getDate(Raid $raid, RaidCharacter $raidCharacter, User $user = null, bool $isForTemplate = false)
    {
        $nameOfServerTimezone = $raidCharacter->getUserCharacter()->getServer()->getTimezone()->getName();
        $start = clone $raid->getStartAt();
        $end = clone $raid->getEndAt();

        $start->setTimezone(new DateTimeZone($nameOfServerTimezone));
        $end->setTimezone(new DateTimeZone($nameOfServerTimezone));

        if (!$user) {
            return $isForTemplate
                ? 'Server : ' . $start->format('l') . ' from ' . $start->format('H:i') . ' to ' . $end->format('H:i')
                : 'Server : ' . $start->format('d/m/Y') . ' from ' . $start->format('H:i') . ' to ' . $end->format('H:i');
        }

        $nameOfUserTimezone = $user->getTimezone()->getName();
        $strDateServer = $start->format('d/m/Y');
        $strTimeServer = ' from ' . $start->format('H:i') . ' to ' . $end->format('H:i');

        $start->setTimezone(new DateTimeZone($nameOfUserTimezone));
        $end->setTimezone(new DateTimeZone($nameOfUserTimezone));

        $strDateLocal = $start->format('d/m/Y');
        $strTimeLocal = ' from ' . $start->format('H:i') . ' to ' . $end->format('H:i');

        if ($strDateServer === $strDateLocal && $strTimeServer === $strTimeLocal) {
            return $isForTemplate
                ? 'Server : ' . $start->format('l') .  $strTimeServer
                : 'Server : ' . $strDateServer . $strTimeServer;
        }

        return $isForTemplate
            ? 'Server : ' . $start->format('l') .  $strTimeServer . '<br> Local : ' . $start->format('l') . $strTimeLocal
            : 'Server : ' . $strDateServer . $strTimeServer . '<br> Local : ' . $strDateLocal . $strTimeLocal;
    }
}
