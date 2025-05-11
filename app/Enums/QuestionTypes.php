<?php

namespace App\Enums;

enum QuestionTypes: string 
{
    case SIMPLE       = 'simple';
    case CHOICE       = 'choice';
    case MULTY_CHOICE = 'multy_choice';
}