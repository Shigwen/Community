<?php

namespace App\Twig;

use App\Entity\Raid;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use App\Service\Raid\NumberOfPlacesRemaining;

class RaidNumberOfPlacesRemainingExtension extends AbstractExtension
{
    private $nbrOfPlaces;

    public function __construct(NumberOfPlacesRemaining $nbrOfPlaces)
    {
        $this->nbrOfPlaces = $nbrOfPlaces;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('number_of_places_remaining', [$this, 'numberOfPlacesRemaining']),
        ];
    }

    public function numberOfPlacesRemaining(Raid $raid, int $role)
    {
        return $this->nbrOfPlaces->getHtmlPlacesRemaining($raid, $role);
    }
}
