<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'domain',
        'notes',
        'is_active',
        'trial_period_end_date',
        'src', // verzija aplikacije
        'db_host',
        'db_username',
        'db_password',
        'redis_host',
        'word_to_pdf_worker',
        'timezone',
        // 'user_uid',
        // 'user_gid',
    ];


}
