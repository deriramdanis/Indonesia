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
        $header = array('id', 'province_id', 'name');
        $data = $Csv->csv_to_array($file, $header);
        \DB::table('regencies')->insert($data);
    }
}