<?php
//require './scripts/mongo_helper.php';
//$mh = new mongoHelper('candystore','products');
$data = json_decode(file_get_contents('../json_files/candy_search2.json'),true);
$i = 0;
$types = [];
$old_type = "";

$fp = fopen("wgets.sh","w");

$path = '../candy_images_2/';
foreach ($data as $candy) {
    $candy['type'] = str_replace(' ','_',$candy['type']);
    if (!array_key_exists($candy['type'], $types)) {
        $types[$candy['type']] = 0;
        if(!is_dir($path.$old_type."/")){
            mkdir($path.$old_type."/");
        }
    }
    $types[$candy['type']]++;

    if($old_type != $candy['type']){
        $old_type = $candy['type'];
        echo"\n{$old_type}\n";

    }

    echo $candy['img']."\n";

    $img_names = get_image_names($candy['img']);

    //save_candy_image($path.$old_type."/",$img_names,true);

    fwrite($fp,"wget {$img_names['small_url']} -O \"{$path}{$old_type}/{$img_names['small_name']}\"\nsleep .3\n");
    
    fwrite($fp,"wget {$img_names['large_url']} -O \"{$path}{$old_type}/{$img_names['name']}\"\nsleep .4\n");

    $i++;
    if ($i % 100 == 0) {
        flush();
        ob_flush();
        echo"Count: {$i} , NoImage: {$noImage}\n";
    }

}


function get_image_names($img){
    $parts = explode('/', $img);
    $small_name = $parts[sizeof($parts) - 1];
    $name = $parts[sizeof($parts) - 1];
    $ext = substr($name, strlen(trim($name)) - 3, 3);

    if($ext == 'peg'){
        $ext = 'jpg';
    }
    $small_name = substr($name, 0, strlen($name) - 4) . '_small' . ".{$ext}";
    $small_url = $img;
    $large_url = $img;
    $large_url = str_replace("200x/", "", $large_url);
    $large_url = str_replace("small_image", "image", $large_url);

    $name = str_replace("-", "_", $name);
    $small_name = str_replace("-", "_", $small_name);
    return ['name'=>$name,'small_name'=>$small_name,'large_url'=>$large_url,'small_url'=>$small_url];
}

function save_candy_image($path,$img_data, $save_file = false)
{
    global $noImage;

    $small_name = $img_data['small_name'];
    $name = $img_data['name'];
    $small_url = $img_data['small_url'];
    $large_url = $img_data['large_url'];

    echo "small: ".$small_url."\n";
    echo "large: ".$large_url."\n";

    if ($save_file) {
        if(!file_put_contents($path . $small_name, file_get_contents($small_url))){
            $noImage++;
        }
        sleep(.3);
        if(!file_put_contents($path . $name, file_get_contents($large_url))){
            $noImage++;
        }
        sleep(.3);
    }
    
}