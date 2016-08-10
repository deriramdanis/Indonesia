<?php

namespace Laravolt\Indonesia\Seeds;

use Illuminate\Database\Seeder;

class ProvincesSeeder extends Seeder
{
    public function run()
    {
        $file = __DIR__. '/../../resources/csv/provinces.csv';
        \DB::table('provinces')->delete();

        if(strpos($file, '\\') !== false)
        {
            $file = str_replace('\\', '/', $file);
        }
    $query = <<<eof
    LOAD DATA INFILE '$file'
     IGNORE INTO TABLE provinces
     FIELDS TERMINATED BY ','
     LINES TERMINATED BY '\r\n'
    ;
eof;

        //echo $query;

        \DB::connection()->getpdo()->exec($query);
    }
}
