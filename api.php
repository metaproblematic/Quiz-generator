<?php

$DBhost = "localhost";
$DBuser = "username";
$DBpass = "passcode";
$DBname = "databasename";

$DBconnect = new mysqli($DBhost, $DBuser, $DBpass, $DBname);

if($DBconnect->connect_errno) {
    die("ERROR : ->".$DBconnect->connect_error);
}

$table;
if (isset($_GET['table'])) {
    $table = filter_var($_GET['table'], FILTER_SANITIZE_STRING);
}

if (isset($_GET['answer'])) {
    $answer = filter_var($_GET['answer'], FILTER_SANITIZE_STRING);
}

$select = "SELECT * FROM $table WHERE id = 1"; 
$query = $DBconnect->query($select);

if ($query) {
    echo "<script>console.log('success, table found!')</script>";
} else {
    echo "error: -> ($DBconnect->error)";
}

$row = $query->fetch_array();
$question = '<h3 id="question">' . $row['question'] . '</h3>';
$title = '<h1 id="title">Parent Poll:' . $row['title'] . '</h1>';

echo $title;
echo $question;

$responseType = $row['responseType'];

$responseCount = intval($row['responseCount']) + 1;

for ($x = 1; $x < $responseCount; $x++){
    
    $column = 'response' . $x;
    $response = $row[$column];
    echo '<label><input type="'.$responseType.'" data-type="response" name="response" value="'.$response.'">' . $response . '</label><br>';
}

$submit = "<br><p><button onclick='saveData(\"$table\")'>Submit</button></p>";

echo $submit;
include('script.js');
$DBconnect->close();

?>

