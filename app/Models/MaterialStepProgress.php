<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialStepProgress extends Model
{
    protected $table = 'material_step_progress';

    protected $fillable = [
        'user_id',
        'material_id',
        'step',
        'is_completed',
        'completed_at'
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
