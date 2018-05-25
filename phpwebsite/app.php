<?php
session_start();

$_SESSION['userid']  = session_id();
// Turn error reporting on during testing (not production)
error_reporting(1);
require('./includes/settings.php');
require('./includes/class_upload.php');

$params = parseUrl($_SERVER['REQUEST_URI']); 

$route = $params['route'];
if(array_key_exists("error",$params)){
    echo print_response($params);
    exit;
}
unset($params['route']); //remove route key from array

$db = new mysqli("localhost", $settings['username'], $settings['password'], $settings['dbname']);
// If we have an error connecting to the db, then exit page
if ($db->connect_errno) {
    echo 'hi';
    print_response(['success'=>false,"error"=>"Connect failed: ".$db->connect_error]);
}

// not all items are legit
$routes = ['routes'=>
    [
        ['type'=>'post','name'=>'register','params'=>[]],
        ['type'=>'post','name'=>'login','params'=>['email','']],
        ['type'=>'get','name'=>'categories','params'=>[]],    
        ['type'=>'get','name'=>'search','params'=>[]], 
        ['type'=>'get','name'=>'browse','params'=>[]],           
        ['type'=>'post','name'=>'fileUpload','params'=>[]]
    ]
];

$response = false;
$default_js_scripts = [

    "<script src=\"/chocolate/js/jquery.min.js?v=".rand()."\"></script>",

    "<script src=\"/chocolate/js/bootstrap.bundle.min.js?v=".rand()."\"></script>",

    "<script src=\"/chocolate/scripts/cookie.js?v=".rand()."\"></script>",

    "<script src=\"/chocolate/scripts/categories.js?v=".rand()."\"></script>"

];

$scripts = [];
//die($route);
switch($route){
    case 'browsePage':
        $view = include('views/browse.html');
        $scripts[] = '/chocolate/scripts/fetch_products.js?v='.rand();
        if(!array_key_exists('offset',$params)){
            $offset = 0;
        }else{
            $offset = $params['offset'];
        }
        if(!array_key_exists('size',$params)){
            $size = 10;
        }else{
            $size = $params['size'];
        }
        if(!array_key_exists('category',$params)){
            $category = "%";
        }else{
            $category = $params['category'];
        }
       // $data = browse($offset,$size,$category);
        echo render_view($view,$scripts);
        break;
    case 'mainPage':
         $view = file_get_contents('views/content.html');
         echo render_view($view);
         break;
    case 'aboutPage'://not sure
         $view = file_get_contents('views/about.html');
         echo render_view($view);
         break;
    case 'fileUpload'://not sure
         $response = doUpload($settings,$_FILES,'./uploads');
         break;
    case 'navigation'://legacy
         $response['data'] = getMenuItems($menu_id);
         break;
    case 'categories'://not implemented
         $response['data'] = getCategories();
        // echo '<pre>';
        // print_r($response);
        //die;
         break;
    case 'search':
        if(sizeof($params) > 0){
            $response['data'] = searchCandy($params);
        }else{
            $response['data'] = ['error'=>'No search params'];
        }
        break;
    case 'browse':
        
        if(array_key_exists('category',$params))
        {
            $category = $params['category'];
        }
        else{
            $category = 'all';
        }
        if(array_key_exists('page',$params))
        {
            $page = $params['page'];
        }
        else{
            $page = 1;
        }
        $response['data'] = browse($category,$page);
        break;   
    case 'addtoCart':
        if(array_key_exists('pid',$params)){
            $response['data'] = addcart($params['pid']);
            
        }
        break;
    case 'deleteCart':
        if(array_key_exists('pid',$params)){
            $response['data'] = deletecart($params['pid']);
        }
        break;
    case 'getCart':
            $response['data'] = getcart();
            break;
    default:
        $urls = [];
        foreach($routes['routes'] as $route){
            $urls[] = ['type'=>$route['type'],'url'=>'http://159.89.230.116'."/".$route['name']];
        }
        $response = $urls;
        $response['request_parts'] = $request_parts;
}


if($response !== false){
    
    $response['success']=true;
    logg($response);  
    echo json_encode($response);
}


function browse($category,$page){
    global $db;
    $category = utf8_decode(urldecode($category));

    //die($category);
    $page = ((int)$page*10)-9;
    if($category==='all'){
        $sql = "SELECT * FROM `products`  LIMIT $page, 10";
    }
    else{
        $sql = "SELECT * FROM `products` WHERE category='{$category}' LIMIT $page,10";
    }

    $result = $db->query($sql);
    
    while($row = $result->fetch_assoc()){
        $row['description'] = mb_convert_encoding($row['description'],'UTF-8','UTF-8');
        $items[] = $row;
    }
    return $items;
}

function getCategories(){
     global $db;
    //print_r($db);
    //die;
    
    $sql = "SELECT count(*)as each_category,category FROM `products` group by category order by category desc";
    //$sql = "SELECT *  FROM  `products` LIMIT 30";
    $count = 0;
    $result = $db->query($sql);
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
        $count +=$row['each_category'];
    }
    return [$items,$count]; 
}
function getcart()

{

    global $db;
    $items = [];
    $counts = [];
    $session = $_SESSION['userid'];
    if(isset($_SESSION['username'])){
        $uid = $_SESSION['username'];
        $sql = "SELECT id FROM `users` WHERE username='{$uid}'";
        $result = $db->query($sql);
        while ($row = $result->fetch_assoc()) {
            $item = $row;
    
        }
        $uid = $item['id'];
        $sql = "SELECT item_id,count FROM `shopping_cart` WHERE uid = '{$uid}'";
    }
    else{
        $sql = "SELECT item_id,count  FROM `shopping_cart` WHERE uid=-1 and  session_id='{$session}'";
    }
  
    $result = $db->query($sql);
    while ($row = $result->fetch_assoc()) {
       
        $counts[] = $row['count'];
       $items[] = $row['item_id'];
    }
    $items = implode("','", $items);
    $sql = "SELECT *  FROM `products` WHERE id in ('{$items}')";
    $result = $db->query($sql);
    while ($row = $result->fetch_assoc()) {
        $row['description'] = mb_convert_encoding($row['description'],'UTF-8','UTF-8');
       $products[] = $row;

    
    }
    return [$counts,$products]; 


}

function deletecart($item_id)

{

    global $db;;
    $session = $_SESSION['userid'];
    if(isset($_SESSION['username'])){
        $uid = $_SESSION['username'];
        $sql = "SELECT id FROM `users` WHERE username='{$uid}'";
        $result = $db->query($sql);
        while ($row = $result->fetch_assoc()) {
    
            $item = $row;
    
        }
        $uid = $item['id'];
        $sql = "DELETE FROM `shopping_cart` WHERE uid='{$uid}' and item_id='{$item_id}'";
    }
    else{
        $sql = "DELETE FROM `shopping_cart` WHERE uid=-1 and item_id='{$item_id}' and  session_id='{$session}'";
    }
    
    $result = $db->query($sql);
    if ($result === TRUE) {
        return true;// "Record deleted successfully";
    } else {
        return false;
    }


}




function addcart($params)

{

    global $db;



    $item_id = $params;


    $product_sql = "SELECT * FROM `products` WHERE id = '{$item_id}'";

    $product_result = $db->query($product_sql);
    while ($product_row = $product_result->fetch_assoc()) {

        $product = $product_row;

    }
    
    $price = $product['price'];
    $ip = $_SERVER['REMOTE_ADDR'];
  
    
    $session =  $_SESSION['userid'];
    if(!isset($_SESSION['username'])){
        $guest = 1;
        $uid = -1;
        $sql = "SELECT * FROM `shopping_cart` WHERE item_id= '{$item_id}' and  uid=-1 and  session_id='{$session}'";
       // $result = $db->query($sql);
       $result = $db->query($sql);
            if ($result->num_rows > 0) {
                
                $sql = "UPDATE `shopping_cart` SET count=count+1 WHERE item_id= '{$item_id}' and  uid=-1 and  session_id='{$session}'";
                $result =  $db->query($sql);
            }else{
                $sql = "INSERT INTO `shopping_cart`  (`uid`, `item_id`, `date_created`, `price`, `count`,
                `coupon_code`, `ip_address`, `guest`,`session_id`) 
              VALUES ('{$uid}', {$item_id}, now(), '{$price}', '1', '', '{$ip}', '{$guest}', '{$session}');";
          
              $result = $db->query($sql);
            }
    }
    else{
        $usename = $_SESSION['username'];
        $sql = "SELECT id FROM `users` WHERE username= '{$usename}'";
        $result = $db->query($sql);
        while ($row = $result->fetch_assoc()) {
    
            $item = $row;
    
        }
        $uid = $item['id'];
        $guest = 0;
        $sql = "SELECT * FROM `shopping_cart` WHERE item_id= '{$item_id}' and  uid='{$uid}'";
        // $result = $db->query($sql);
        $result = $db->query($sql);
             if ($result->num_rows > 0) {
                 
                 $sql = "UPDATE `shopping_cart` SET count=count+1 WHERE item_id= '{$item_id}' and   uid='{$uid}'";
                 $result =  $db->query($sql);
             }else{
                 $sql = "INSERT INTO `shopping_cart`  (`uid`, `item_id`, `date_created`, `price`, `count`,
                 `coupon_code`, `ip_address`, `guest`,`session_id`) 
               VALUES ('{$uid}', {$item_id}, now(), '{$price}', '1', '', '{$ip}', '{$guest}', '{$session}');";
           
               $result = $db->query($sql);
             }
    }
    



}

function searchCandy($params){
    global $db;
    $items = [];
    if(sizeof($params)==0)
    {
        return $items;
    }
    // Start empty where clause
    $where = "WHERE ";
    // Loop through array adding "key like value"
    // along with an "and" in case there are more than one filter pairs
    foreach($params as $k => $v)
    {
        $v = utf8_decode(urldecode($v));
        $where = $where." ".$k." LIKE '%".$v."%' AND" ;
    }
    // Add "1" for last and to make it work :) 
    $where .= " 1";
    $sql = "SELECT * FROM `products` {$where}";
    $result = $db->query($sql);
    while($row = $result->fetch_assoc())
    {
        $row['description'] = mb_convert_encoding($row['description'],'UTF-8','UTF-8');
        $items[] = $row;
    }
    return $items;
}

function getMenuItems($menu_id=1){
    
    if(!$menu_id){
        $menu_id = 1;
    }
    
    global $db;
    $items = [];
    $sql = "SELECT * from menu_items where mid = {$menu_id};";

    $result = $db->query($sql);
    
    while($row = $result->fetch_assoc()){
        $items[] = $row;
    }
    
    return $items;
}


function doUpload($settings,$files,$path){
    $uploader = new Upload($settings,$files,$path);
    return $uploader->doUploads();
}


/**
 * Function print_response($respoonse)
 * 
 * This function builds a response object for requests that need a json 
 * data object. 
 */
function print_response($response){
    if($response['data']){
        $response['data_size'] = sizeof($response['data']);
    }
    header('Content-Type: application/json');
    //print_r($response);
    echo json_encode($response);
    exit;
}

/**
 * Function render_view($content)
 * Params: 
 *    $content: html content to be included in a built page
 */
function render_view($page,$scripts=[]){
   //get access to the default scripts array

   global $default_js_scripts;

	

   //build page

   //$page = file_get_contents('views/header.html');

   //$page .= file_get_contents('views/navigation.html');

   //$page .= $html;

   //$page .= file_get_contents('views/footer.html');



   //add additional scripts to default array

   foreach($scripts as $s){

       $default_js_scripts[] = "<script src=\"{$s}\"></script>\n";
       
   }

   ///$default_js_scripts[] = "\n</body>\n";

   //$default_js_scripts[] = "</html>\n";



   //"build" the page by concatenating all parts

   //$page .= "\n".implode("\n",$default_js_scripts);

   

   echo $page;

   exit;
}

/**
 * This method turns a url of the format: 
 *     https://domain.com/routename/k1/v1/k2/v2/kn/vn 
 *     into an Associative Array: 
 *     $kvp = [
 *        'route' => 'routename',
 *        'k1' => 'v1',
 *        'k2' => 'v2',
 *        'kn' => 'vn'
 *     ];
 */
function parseUrl($url){
    $parts = explode('/', $_SERVER['REQUEST_URI']);

    $kvp = [];

    // find the index of "app.php" (this filename)
    $i = array_search('app.php',$parts);

    // The route name should be located right after.
    $kvp["route"] = $parts[$i+1];

    // remove all unnecessary entries (up till now)
    for($j = 0;$j<=$i+1;$j++){
        array_shift($parts);
    }

    //check to see if last item is empty
    if(trim($parts[sizeof($parts)-1]) == ""){
        array_pop($parts);
    }

    if(sizeof($parts) % 2 == 1){
        $kvp["error"] = "Key value pairs do not match up!";
        return $kvp;
    }
    for($j=0;$j<sizeof($parts);$j+=2){
        $kvp[$parts[$j]] = $parts[$j+1];
    }
    return $kvp;
    
}


