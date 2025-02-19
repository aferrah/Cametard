<?php

namespace App\Enum;

enum CamionTypeEnum: string
{
    case FRIGO = 'frigo';
    case CITERNE = 'citerne';
    case PALETTE = 'palette';
    case PLATEAU = 'plateau';
}
