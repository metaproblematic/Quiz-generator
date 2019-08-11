 <?php
 
 $ip_address = $_SERVER['REMOTE_ADDR'];
 $url = 'http://www.geoplugin.net/php.gp?ip=' . $ip_address;
    
$data = unserialize(file_get_contents($url));
echo $data['geoplugin_city']; 

?>