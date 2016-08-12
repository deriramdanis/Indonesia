<?php

namespace Laravolt\Indonesia\Seeds;

use Illuminate\Database\Seeder;

class ProvincesSeeder extends Seeder
{
    public function run()
    {
		$Csv = new CsvtoArray;
        \DB::table('provinces')->delete();
        $file = __DIR__. '/../../resources/csv/provinces.csv';
        $data = $Csv->csv_to_array($file);
        \DB::table('provinces')->insert($data);
    }
}
