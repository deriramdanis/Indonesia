<?php

namespace Laravolt\Indonesia\Seeds;

use Illuminate\Database\Seeder;

class DistrictsSeeder extends Seeder
{
    public function run()
    {
        $file = __DIR__. '/../../resources/csv/districts.csv';
        \DB::table('districts')->delete();

        if(strpos($file, '\\') !== false)
        {
            $file = str_replace('\\', '/', $file);
        }
    $query = <<<eof
    LOAD DATA INFILE '$file'
     IGNORE INTO TABLE districts
     FIELDS TERMINATED BY ','
     LINES TERMINATED BY '\r\n'
    ;
eof;

        //echo $query;

        \DB::connection()->getpdo()->exec($query);
    }
}
