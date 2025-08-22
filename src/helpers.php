<?php

function getDifficultyClass($value): string
{
    if (is_numeric($value)) {
        if ($value >= 250) { return 'difficulty-expert'; }
        if ($value >= 100) { return 'difficulty-hard'; }
        if ($value >= 50) { return 'difficulty-medium'; }
        return 'difficulty-easy';
    } else {
        switch (strtolower($value)) {
            case 'initié':  return 'difficulty-medium';
            case 'avancé':  return 'difficulty-hard';
            case 'expert':  return 'difficulty-expert';
            case 'débutant':
            default:        return 'difficulty-easy';
        }
    }
}