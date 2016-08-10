<?php

namespace Laravolt\Indonesia\Seeds;

use Illuminate\Database\Seeder;

class VillagesSeeder extends Seeder
{
    public function run()
    {
		$file = __DIR__. '/../../resources/csv/villages.csv';
        \DB::table('villages')->delete();

        if(strpos($file, '\\') !== false)
        {
            $file = str_replace('\\', '/', $file);
        }
    $query = <<<eof
    LOAD DATA INFILE '$file'
     IGNORE INTO TABLE villages
     FIELDS TERMINATED BY ','
     LINES TERMINATED BY '\r\n'
    ;
eof;

        //echo $query;

        \DB::connection()->getpdo()->exec($query);
    }
}
