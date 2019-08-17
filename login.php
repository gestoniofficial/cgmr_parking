<?php

$firstname = "";

if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400'); // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    }

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
        header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    }

    exit(0);
}

 $servername = "remotemysql.com";
    $username = "lh9TrA5lX0";
    $password = "jpgVJJ0BB9";
    $database = "lh9TrA5lX0";

    // Create connection
    $conn = mysqli_connect($servername, $username, $password, $database);
    $link = mysqli_connect($servername, $username, $password, $database);

    if($link === false){

        die("ERROR: Could not connect. " . mysqli_connect_error());

    }

$account_check = 0;

$data = file_get_contents('php://input');

if (isset($data)) {
    $request = json_decode($data,true);

    $user_identification = $request['user_identification'];
    $password = $request['password'];

 


    if ($user_identification == ""){
        if($password == ""){
            $account_check = -1;
        }
    }
}



$decrypted = crypt($password, '$2a$07$ChrisGesMitziRevaIsAChrString$');

$user_id = 0;

$sql_login_phone_number = "SELECT user_id FROM users WHERE contact_number = '".$user_identification."' and password = '".$decrypted."' "; 
$result_login_phone_number = mysqli_query($conn, $sql_login_phone_number);
while ($row_login_phone_number = mysqli_fetch_array($result_login_phone_number)){
        $account_check++;
        $user_id = $row_login_phone_number["user_id"];
}


if ($account_check == 0){
    $sql_login_email = "SELECT user_id FROM users WHERE email = '".$user_identification."' and password = '".$decrypted."' "; 
    $result_login_email = mysqli_query($conn, $sql_login_email);
    while ($row_login_email = mysqli_fetch_array($result_login_email)){
            $account_check++;
            $user_id = $row_login_email["user_id"];
    }
}


// If result matched $myusername and $mypassword, table row must be 1 row

if ($account_check > 0) {
 $sql_user_data = "SELECT * FROM users WHERE user_id = '".$user_id."' and password = '".$decrypted."' "; 
    $result_user_data = mysqli_query($conn, $sql_user_data);
    while ($row_user_data = mysqli_fetch_array($result_user_data)){
        $firstname = $row_user_data["first_name"];
        $gender = $row_user_data["gender"];
        $lastname = $row_user_data["last_name"];
        $birthdate = $row_user_data["birthdate"];
        $contact_number = $row_user_data["contact_number"];
        $account_type = $row_user_data["account_type"];
        $profile_pic_loc = $row_user_data["profile_pic_loc"];
        $address = $row_user_data["address"];
        $system_status = $row_user_data["system_status"];
        $user_status = $row_user_data["user_status"];
        $password = $row_user_data["password"];
    }

   $response = "Success".$user_id."©".$firstname."©".$gender."©".$lastname."©".$birthdate."©".$contact_number."©".$account_type."©".$profile_pic_loc."©".$address."©".$system_status."©".$user_status."©".$password;
} else {
    $response = "Login Failed";

}

echo json_encode($response);
