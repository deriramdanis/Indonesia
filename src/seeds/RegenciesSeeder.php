<?php

namespace Laravolt\Indonesia\Seeds;

use Illuminate\Database\Seeder;

class RegenciesSeeder extends Seeder
{
    public function run()
    {
        $file = __DIR__. '/../../resources/csv/regencies.csv';
        \DB::table('regencies')->delete();

        if(strpos($file, '\\') !== false)
        {
            $file = str_replace('\\', '/', $file);
        }
    $query = <<<eof
    LOAD DATA INFILE '$file'
     IGNORE INTO TABLE regencies
     FIELDS TERMINATED BY ','
     LINES TERMINATED BY '\r\n'
    ;
eof;

        \DB::connection()->getpdo()->exec($query);
    }
}