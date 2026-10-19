<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

enum TypeRoles: string
{
    case COMMERCIAL = 'commercial';
    case PRODUCTION = 'production';
    case FINANCE = 'finance';
    case ADMIN = 'admin';
}
