<?php

namespace FeIron\LaraFrame\models;

use Illuminate\Database\Eloquent\Model;

class LF_notes extends Model
{
    protected $table='lf_notes';

    protected $fillable=['notes'];
    /**
     * Get the owning model.
     */
    public function notable()
    {
        return $this->morphTo();
    }
}
