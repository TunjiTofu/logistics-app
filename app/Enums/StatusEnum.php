<?php

namespace App\Enums;

enum StatusEnum: string
{
    case SUCCESS = 'success';
    case FAILED = 'failed';
    case ERROR = 'error';
}
