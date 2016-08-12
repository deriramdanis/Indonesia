<?php

namespace Laravolt\Indonesia\Seeds;

use Illuminate\Database\Seeder;

class DistrictsSeeder extends Seeder
{
    public function run()
    {
    	$Csv = new CsvtoArray;
        \DB::table('districts')->delete();
        $file = __DIR__. '/../../resources/csv/districts.csv';
        $data = $Csv->csv_to_array($file);
        \DB::table('districts')->insert($data);
    }
}
