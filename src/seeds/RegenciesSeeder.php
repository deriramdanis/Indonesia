<?php

namespace Laravolt\Indonesia\Seeds;

use Illuminate\Database\Seeder;

class RegenciesSeeder extends Seeder
{
    public function run()
    {
    	$Csv = new CsvtoArray;
        \DB::table('regencies')->delete();
        $file = __DIR__. '/../../resources/csv/regencies.csv';
        $data = $Csv->csv_to_array($file);
        \DB::table('regencies')->insert($data);
    }
}