<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Preference extends Model
{
    protected $fillable = ['user_id', 'selected_sources', 'selected_categories', 'selected_authors'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
