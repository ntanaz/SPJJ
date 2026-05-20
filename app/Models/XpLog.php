<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class XpLog extends Model
{
    protected $table = 'xp_logs';

    protected $fillable = [
        'user_id',
        'module_id',
        'activity_type',
        'xp_earned',
        'description',
        'reference_type',
        'reference_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}
