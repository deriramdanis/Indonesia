<?php

namespace Laravolt\Indonesia\Models;

use Illuminate\Database\Eloquent\Model;

class Regency extends Model
{
    public function province()
	{
	    return $this->belongsTo('Laravolt\Indonesia\Models\Province', 'foreign_key');
	}

	public function districts()
    {
        return $this->hasMany('Laravolt\Indonesia\Models\District');
    }
}
