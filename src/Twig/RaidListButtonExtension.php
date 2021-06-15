<?php

namespace App\Twig;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

class RaidListButtonExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('get_array_button', [$this, 'getArrayButton']),
        ];
    }

    /**
     * Returns a paginated button array
     */
    public function getArrayButton($nbrOfPages, $currentPage)
    {
        $buttons = [];

        // Generates an array of all the not sorted buttons
        for ($i = 0; $i <= $nbrOfPages; $i++) {
            $class = ($i === $currentPage) ? 'btn-info current' : 'btn-primary';
            $pageNumber = $i + 1;
            $buttons[$i] = '<button data-page="' . $i . '" class="btn btn-sm rounded-pill mx-1 page ' . $class . '"> ' . $pageNumber . ' </button>';
        }

        // If there is less than 9 pages, we do not paginate
        if ($nbrOfPages < 8) {
            return $buttons;
        }

        // Show the 6 first buttons and truncate the last buttons (except THE last)
        if ($currentPage < 5) {
            $buttonsShow = array_slice($buttons, 0, 6);
            $buttonsShow[] = '...';
            $buttonsShow = array_merge($buttonsShow, array_slice($buttons, -1, 1));

        // Show the 6 last buttons and truncate the first buttons (except THE first)
        } else if ($currentPage > ($nbrOfPages - 5)) {
            $buttonsShow = array_slice($buttons, 0, 1);
            $buttonsShow[] = '...';
            $buttonsShow = array_merge($buttonsShow, array_slice($buttons, -6, 6));

        // Show the 5 middle buttons (2 before current, the current, 2 after current) and truncate the start and end buttons (except THE first and THE last)
        } else {
            $cursor = $currentPage - 2;

            $buttonsShow = array_slice($buttons, 0, 1);
            $buttonsShow[] = '...';
            $buttonsShow = array_merge($buttonsShow, array_slice($buttons, $cursor, 5));
            $buttonsShow[] = '...';
            $buttonsShow = array_merge($buttonsShow, array_slice($buttons, -1, 1));
        }

        return $buttonsShow;
    }
}
