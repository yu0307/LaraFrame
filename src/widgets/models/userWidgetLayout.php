<?php

namespace feiron\felaraframe\widgets\models;

use Illuminate\Database\Eloquent\Model;

class userWidgetLayout extends Model
{
    protected $table = 'user_widget_layout';

    protected $fillable = ['widget_name', 'settings', 'layoutable_id', 'layoutable_type','order'];
    /**
     * Get the owning model.
     */
    public function layoutable()
    {
        return $this->morphTo();
    }
}
