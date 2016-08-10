<?php

namespace Laravolt\Indonesia\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    public function regency()
	{
	    return $this->belongsTo('Laravolt\Indonesia\Models\Regency', 'foreign_key');
	}

	public function villages()
    {
        return $this->hasMany('Laravolt\Indonesia\Models\Village');
    }
}
