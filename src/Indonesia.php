<?php

namespace Laravolt\Indonesia;

class Indonesia
{
    protected $model, $belongsTo;

    public function province()
    {
        $this->model = new Models\Province;
    	return $this;
    }

    public function regency(){
        $this->model = new Models\Regency;
        $this->belongsTo = new Models\Province;
        return $this;
    }

    public function district(){
        $this->model = new Models\District;
        $this->belongsTo = new Models\Regency;
        return $this;
    }

    public function village(){
    	$this->model = new Models\Village;
        $this->belongsTo = new Models\District;
        return $this;
    }

    public function all(){
        return $this->model->all();
    }

    public function byId($id){
        return $this->model->find($id);
    }

    public function byProvince($provinceId){
        return $this->belongsTo->where('id', $provinceId)->with('regencies')->get();
    }

    public function byRegency($regencyId){
        return $this->belongsTo->where('id', $regencyId)->with('districts')->get();
    }

    public function byDistrict($districtId){
        return $this->belongsTo->where('id', $districtId)->with('villages')->get();
    }

}

