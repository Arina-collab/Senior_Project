<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Opportunity extends Model
{
    protected $fillable = [
        'title',
        'category',
        'type',
        'description',
        'location',
        'deadline',
        'is_priority',
        'posted_by',
    ];

    public function publisher()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }
}