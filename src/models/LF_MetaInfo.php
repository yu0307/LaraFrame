<?php

namespace feiron\felaraframe\models;

use Illuminate\Database\Eloquent\Model;

class LF_MetaInfo extends Model
{
    protected $table= 'lf_site_metainfo';

    protected $fillable=['meta_name', 'meta_value'];

    protected $casts = [
        'meta_value' => 'array'
    ];
}
