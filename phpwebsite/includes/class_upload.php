<?php
/*
(
    [name] => Array
        (
            [0] => delete_from_binary_tree.png
        )
    [type] => Array
        (
            [0] => image/png
        )
    [tmp_name] => Array
        (
            [0] => /tmp/phpeS8bFp
        )
    [error] => Array
        (
            [0] => 0
        )
    [size] => Array
        (
            [0] => 395950
        )
)
*/


class Upload{
    private $ufiles;
    private $targetDir;

    public function __construct($settings,$infiles=[],$target_dir=''){
        $this->ufiles = $this->fixFileArray($infiles);
        $this->ufiles = $infiles;
        logg($this->ufiles);
        $this->targetDir = trim($target_dir,"/")."/";
        $this->targetThumbs = $this->targetDir.'thumbs/';
        logg("IN THE CLASS...\n");
        
        $this->db = new mysqli("localhost", $settings['username'], $settings['password'], $settings['dbname']);
        // If we have an error connecting to the db, then exit page
        if ($this->db->connect_errno) {
            print_response(['success'=>false,"error"=>"Connect failed: ".$this->db->connect_error]);
        }
    }

    public function fixFileArray($infiles){
        $temp = [];
        
        for($i=0;$i<sizeof($infiles['files']['name']);$i++){
            $temp[] = [
                'name' => $infiles['files']['name'][$i],
                'type' => $infiles['files']['type'][$i], 
                'tmp_name' => $infiles['files']['tmp_name'][$i], 
                'error' => $infiles['files']['error'][$i],
                'size' => $infiles['files']['size'][$i]
            ];
        }
        return $temp;
    }

    public function addFiles($infiles){
        $this->ufiles = $this->fixFileArray($infiles);
    }

    public function targetDir($target_dir){
        $this->targetDir = trim($target_dir,"/")."/";
    }

    public function doUploads($infiles=[],$target_dir=""){
        if(sizeof($infiles)){
            $this->ufiles = $this->fixFileArray($infiles);
        }
        if($target_dir != ""){
            $this->targetDir = $target_dir;
        }
        
        $time = time();

        
        foreach($this->ufiles as $upfile){
            logg($upfile);

            //$target_file = $this->targetDir  . basename($this->files[$i]["name"]);

            $imageFileType = strtolower(pathinfo($upfile["name"],PATHINFO_EXTENSION));
            $target_file = $this->targetDir. $time.'.'.$imageFileType;
            $check = getimagesize($upfile["tmp_name"]);
            
            if (move_uploaded_file($upfile["tmp_name"], $target_file)) {
                logg('SUCCESS');
                $result = $this->add_to_db($time,$imageFileType);
                if($result['success']){
                    $result['file_name'] = $upfile["name"];
                    $result['time'] = $time;
                }

                $this->generate_images($time,$imageFileType);
                return $result;
            } else {
                $result = ['success'=>false];
            }
        }
        return $result;
    }
    
    private function add_to_db($time,$type,$uid=0){
        $dir = $this->targetDir;
        $thumbs = $this->targetThumbs;
        $ip = $_SERVER['REMOTE_ADDR'];
        $sql = "INSERT INTO `images` VALUES (NULL, {$uid}, '{$time}', '{$type}', '{$time}', '{$dir}', '{$thumbs}', '', '{$ip}');";
        if(!$this->db->query($sql)){
            return ['success'=>false,'error'=>mysqli_error($link)];
        }

        if($result){
            ['success'=>true];
        }

        return $result;
    }

    public function generate_images($time,$type){
        
        $cmd = "convert -resize 150x {$this->targetDir}{$time}.{$type} {$this->targetDir}thumbs/{$time}.{$type}";
        exec($cmd);
        // need to get success
    }

}

function logg($data,$append=true){
    $doappend = 0;
    if($append){
        $doappend = FILE_APPEND;
    }
    file_put_contents('log.txt',print_r($data,true),$doappend);
    file_put_contents('log.txt',"\n\n",$doappend);
}

//$uploader = new Upload($_FILES,'./uploads');
//$uploader->doUploads();


/*
file_put_contents('log.txt',print_r('',true));
$debug = [];

$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["files"]["name"]);

$debug['target_file'] = $target_file;
$debug['base_name'] = $_FILES["files"]["name"];


$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

$debug['imageFileType'] = $imageFileType;
$debug['_FILES'] = $_FILES;

$time = time();

file_put_contents('log.txt',print_r($debug,true),FILE_APPEND);

$target_file = $target_dir . $time.'.'.$imageFileType;
// Check if image file is a actual image or fake image

$check = getimagesize($_FILES["files"]["tmp_name"]);
file_put_contents('log.txt',print_r($check ,true),FILE_APPEND);


if (move_uploaded_file($_FILES["files"]["tmp_name"], $target_file)) {
    file_put_contents('log.txt',print_r('SUCCESS',true),FILE_APPEND);
    file_put_contents('log.txt',print_r($target_file,true),FILE_APPEND);
    generate_images($target_dir,$time,$imageFileType);
} else {
    echo "Sorry, there was an error uploading your file.";
}
*/
?>