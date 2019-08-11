<?php

header("Access-Control-Allow-Origin: *"); 

$DBhost = "localhost";
$DBuser = "username";
$DBpass = "passcode";
$DBname = "database name";

$DBconnect = new mysqli($DBhost, $DBuser, $DBpass, $DBname);

if($DBconnect->connect_errno) {
    die("ERROR : ->".$DBconnect->connect_error);
}

$tablename;
$text = null;
$table;
$answer;

if (isset($_POST['tablename'])) {
    $tablename = filter_var($_POST['tablename'], FILTER_SANITIZE_MAGIC_QUOTES);
}

if (isset($_POST['text'])){
    // $text = filter_var($_POST['text'], FILTER_SANITIZE_MAGIC_QUOTES);  //This is a security vulnerability, but using MAGIC_QUOTES was making weird quotes and content come through in the last 2 columns in the SQL table.
    $text = $_POST['text'];   //Needs something to replace FILTER_SANITIZE_MAGIC_QUOTES here. Another solution would be deleting the last 2 columns, if date and ip address are not needed. 
}

if (isset($_POST['table'])) {
    $table = filter_var($_POST['table'], FILTER_SANITIZE_STRING);
}

if (isset($_POST['userResponse'])) {
    $answer = filter_var($_POST['userResponse'], FILTER_SANITIZE_MAGIC_QUOTES);
}

if ($text == null){

    $ip_address = $_SERVER['REMOTE_ADDR'];
    $date = date("m/d/Y");
    
    $url = 'http://www.geoplugin.net/php.gp?ip=' . $ip_address;

    $data = unserialize(file_get_contents($url));
    
    $country = $data['geoplugin_countryName'];
    $city = $data['geoplugin_city'];
    
    // Save User Response
    $insert = "INSERT INTO $table (userResponse, ip, date, country, city) VALUES ('$answer',' $ip_address', '$date', '$country', '$city')";

    if ($DBconnect->query($insert)) {
        echo "success, data loaded!";
        $select = "SELECT * FROM $table"; 
        $query = $DBconnect->query($select);
        $row = $query->fetch_all(MYSQLI_ASSOC);
        echo json_encode($row);

    } else {
        echo "error: -> ($DBconnect->error)";
    }
    
} elseif($text!=null) {
    
    // Upload CSV file and created table
    
    $newFile = fopen("newfile.csv", "w") or die("Unable to open file!");
    fwrite($newFile, $text);
    fclose($newFile);

    $create = "CREATE TABLE $tablename LIKE NewQuiz";
    $loadData = "LOAD DATA LOCAL INFILE 'newfile.csv' INTO TABLE $tablename FIELDS TERMINATED BY ',' ENCLOSED BY '" . '"' . "' LINES TERMINATED BY '\\n' (id, question, country, city, @dummy, date, @dummy, @dummy, ip, response1, response2, response3, response4, response5, responseType)"; 
    // $loadData = "LOAD DATA LOCAL INFILE 'newfile.csv' INTO TABLE $tablename FIELDS TERMINATED BY ',' LINES TERMINATED BY '\n' (id, response, country, city, region, date, @dummy, @dummy, ip)"; 

    if ($DBconnect->query($create)) {
        echo "success, table created! \n";
    } else {
        echo "error: -> ($DBconnect->error)";
    }

    if ($DBconnect->query($loadData)) {
        echo "success, data loaded!";
    } else {
        echo "error: -> ($DBconnect->error)";
    }
}

$DBconnect->close();

?>    