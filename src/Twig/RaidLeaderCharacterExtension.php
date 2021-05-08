<?php 
namespace App\Twig;

use App\Entity\Raid;
use App\Entity\User;
use App\Entity\Character;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RaidLeaderCharacterExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('is_raid_leader_character', [$this, 'isRaidLeaderCharacter']),
        ];
    }

    public function isRaidLeaderCharacter(User $user, Character $character, Raid $raid)
    {
        if ($raid->getUser() !== $user) {
            return false;
        }

        foreach ($user->getCharacters() as $userCharacter) {
            if ($character === $userCharacter) {
                return true;
            }
        }

        return false;
    }
}