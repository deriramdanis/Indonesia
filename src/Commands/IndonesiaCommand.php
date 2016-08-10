<?php

namespace Laravolt\Indonesia\Commands;

use Illuminate\Console\Command;

class IndonesiaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravolt:indonesia:crawl';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Will crawl wikipedia to get logo image file';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->line('Wait a minute....');
        try {
            $db_tables = array('provinces', 'regencies');
            foreach ($db_tables as $table_) {
                if (!\Schema::hasColumn($table_, 'logo')) {
                    \Schema::table($table_, function($table) {
                        $table->string('logo', 255);
                    });
                }
            }

            $doc = new \DOMDocument();
            $html = file_get_contents('https://id.wikipedia.org/wiki/Daftar_kabupaten_dan_kota_di_Indonesia');
            libxml_use_internal_errors(TRUE);
            $provinces = $this->get_all_provinces($html);

            if (!empty($html)) {
                $doc->loadHTML($html);
                libxml_clear_errors();

                $tables = $doc->getElementsByTagName('table');

                $count = 0;
                $id_province = 0;
                $c_table = 0;
                foreach ($tables as $table) {
                    $trs = $table->getElementsByTagName('tr');
                    $c_tr = 0;
                        foreach ($trs as $tr) {
                            $tds = $tr->getElementsByTagName('td');
                            $imgs = $tr->getElementsByTagName('img');

                            if ($tds->length > 1 && $imgs->length != 0) {
                                $td = $tds->item(1)->childNodes;

                                if ($td->length != 3) {
                                    $a = $tds->item(1)->nodeValue;
                                } else {
                                    $a = $tds->item(2)->nodeValue;
                                }

                                if ($a != null) {
                                    foreach ($imgs as $img) {
                                        $img_parent = $img->parentNode;
                                        $div_parent = $img_parent->parentNode;

                                        if ($div_parent->nodeName == 'div') {
                                            $link = $img_parent->getattribute('href');
                                            
                                            //$regency = get_row($db_tables[1], 'name', $a);
                                            $regency = \DB::table($db_tables[1])->where('name', $a)->first();

                                            if (is_null($regency)) {
                                                $sql = "select * from ".$db_tables[1]." where `province_id` = ".$id_province." and SOUNDEX(`name`) = SOUNDEX('".$a."') limit 1";
                                                //$regency = get_row_regience_soundex($id_province, $a);
                                                //$regency = DB::table($db_tables[1])->where("SOUNDEX(name)", "SOUNDEX('$a')")->first();
                                                $regency = \DB::select($sql);
                                                

                                                if (count($regency) == 1 ) {
                                                    $regency = $regency[0];
                                                    
                                                }
                                                else{
                                                    $a_array = explode(" ", $a);
                                                    $count_a = count($a_array);
                                                    for ($x = $count_a - 1; $x > 0; $x--) {
                                                        if ($x == $count_a - 1) {
                                                            $string_a = $a_array[$x];
                                                        } else {
                                                            $string_a = substr_replace($string_a, $a_array[$x] . " ", 0, 0);
                                                        }

                                                        //$regencies = get_row_regience_like($id_province, $string_a);
                                                        $regencies = \DB::select("select * from ".$db_tables[1]." where `province_id` = ".$id_province." and `name` like '%".$string_a."%' ");
                                                        if (count($regencies) == 1) {
                                                            break;
                                                        }
                                                    }
                                                    if (count($regencies) == 1) {
                                                        $regency = $regencies[0];
                                                    }
                                                }
                                                 
                                            }

                                            if ($regency->logo == '') {
                                                $name = $this->save_image($this->get_url_image('https://id.wikipedia.org' . $link));
                                                \DB::table($db_tables[1])
                                                    ->where('id', $regency->id)
                                                    ->update(['logo' => $name]);
                                                // insert_logo($db_tables[1], $name, $regency[$keys[0]]);
                                                //$regency = get_row($db_tables[1], 'id', $regency[$keys[0]]);
                                                $regency = \DB::table($db_tables[1])->where('id', $regency->id)->first();
                                                $c_tr++;
                                            } 
                                        }
                                    }
                                }
                            } else if ($imgs->length != 0) {
                                $img_parent = $imgs->item(0)->parentNode;
                                if ($img_parent->hasAttribute('class')) {
                                    if ($img_parent->getAttribute('class') == 'image') {
                                        //$province = get_row($db_tables[0], 'name', $provinces[$count]);
                                        $province = \DB::table($db_tables[0])->where('name', $provinces[$count])->first();
                                        if ($province=== null) {
                                            $name_provinces = explode(" ", $provinces[$count]);
                                            $cnt = count($name_provinces) - 1;
                                            //$province = get_row_like($db_tables[0], 'name', $name_provinces[$cnt]);
                                            $province = \DB::select("select * from ".$db_tables[0]." where name like '%".$name_provinces[$cnt]."%' ");
                                            $province = $province[0];
                                        }

                                        $id_province = $province->id;
                                        if ($province->logo == '') {
                                            $link_province = $img_parent->getattribute('href');
                                            $name = $this->save_image($this->get_url_image('https://id.wikipedia.org' . $link_province));
                                            //insert_logo($db_tables[0], $name, $province[0]);
                                            \DB::table($db_tables[0])
                                                    ->where('id', $province->id)
                                                    ->update(['logo' => $name]);
                                            //$province = get_row($db_tables[0], 'id', $id_province);
                                            $province = \DB::table($db_tables[0])->where('id', $id_province)->first();
                                        }
                                        $count++;
                                        $c_table++;
                                        $c_tr++;
                                    }
                                }
                            }
                        }
                }
            }
            
        } catch (Exception $e) {
            $this->error('Something wrong');
            return;
        }
        $this->info('Done...');
        
    }

    function get_all_provinces($html) {
        $doc = new \DOMDocument();
        libxml_use_internal_errors(TRUE);

        if (!empty($html)) {
            $doc->loadHTML($html);
            libxml_clear_errors();
            
            $h3s = $doc->getElementsByTagName('h3');
            $provinces = array();
            foreach ($h3s as $h3){
                $span = $h3->getElementsByTagName('span');
                if($span->length!=0){
                    if($span->item(0)->hasAttribute('class')){
                        if($span->item(0)->getAttribute('class')=='mw-headline'){
                            $provinces[] = $span->item(0)->nodeValue;
                        }
                    }
                }
            }
            return $provinces;
        }
    }

    function get_url_image($url) {
        $html = file_get_contents($url);

        $doc = new \DOMDocument();
        libxml_use_internal_errors(TRUE);

        if (!empty($html)) { 
            $doc->loadHTML($html);
            libxml_clear_errors();

            $xpath = new \DOMXPath($doc);
            
            $div = $xpath->query('//div[@id="file"]');
            $link = 'https:'. $div->item(0)->getElementsByTagName('img')->item(0)->getattribute('src');

            return $link;
        }
    }

    function save_image($url) {
        $name = basename($url);
        $path = public_path().'/images';
        
        if (stripos($name, "%") !== false){
            $name = str_replace("%", "", $name);
        }

        if(!\File::exists($path)) {
            \File::makeDirectory($path, $mode = 0777, true, true);
        }

        $file = file_get_contents($url);
        $filename = $path.'/'.$name;
        if(!\File::exists($filename)){
            file_put_contents($filename, $file);
        }
        return $name;
    }
}
