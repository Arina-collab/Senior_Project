<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthorizedStaff extends Model
{
    protected $table = 'authorized_staff';

    protected $fillable = [
        'email',
        'role',
    ];
}