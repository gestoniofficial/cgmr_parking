    <?php

   header("Access-Control-Allow-Methods", "DELETE, POST, GET, OPTIONS");
header("Access-Control-Allow-Headers", "Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Origin", "*");

    if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
         
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
            
        }
     
        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
     
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
     
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
      
        $data = json_decode(file_get_contents("php://input"));
        $user_type = mysqli_real_escape_string($conn, $data->user_type); 
        $firstname = mysqli_real_escape_string($conn, $data->firstname); 
        $lastname = mysqli_real_escape_string($conn, $data->lastname); 
        $email = mysqli_real_escape_string($conn, $data->email); 
        $phone_number = mysqli_real_escape_string($conn, $data->phone_number); 
        $password = mysqli_real_escape_string($conn, $data->password);
        $gender = mysqli_real_escape_string($conn, $data->gender);
        $address = mysqli_real_escape_string($conn, $data->address);
        $birthdate = mysqli_real_escape_string($conn, $data->birthdate);
        
        $encrypted = crypt($password, '$2a$07$ChrisGesMitziRevaIsAChrString$');

        $phone_number_check = 0;
        $email_check = 0;
        $valid_email_check = 0;
        $valid_phone_number_check = 0;
        $valid_phone_number_digits_check = 0;

        $sql_email_check = "SELECT email FROM users WHERE email = '".$email."' "; 
        $result_email_check = mysqli_query($conn, $sql_email_check);
        while ($row_email_check = mysqli_fetch_array($result_email_check)){
            $email_check++;
        }


        $sql_phone_number_check = "SELECT contact_number FROM users WHERE contact_number = '".$phone_number."' "; 
        $result_phone_number_check = mysqli_query($conn, $sql_phone_number_check);
        while ($row_phone_number_check = mysqli_fetch_array($result_phone_number_check)){
            $phone_number_check++;
        }

        if (!filter_var($phone_number, FILTER_VALIDATE_INT)){
            $valid_phone_number_check++;
        }

        // COBAR
        // if FILTER_VALIDATE_INT doesn't work, use is_numeric()

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $valid_email_check++;
        }

        if (strlen($phone_number) != 12){
            $valid_phone_number_digits_check++;
        }

        

        if ($email_check != 0){
            $response = "Invalid email";
        } else if ($phone_number_check != 0){
            $response = "Invalid phone number";
        } else if ($valid_email_check !=0){
            $response = "Invalid email syntax";
        } else if ($valid_phone_number_check !=0){
            $response = "Invalid phone number syntax";
        } else if ($valid_phone_number_digits_check !=0){
            $response = "Invalid phone number digits syntax";
        } else {
            $sql = "INSERT INTO users (account_type,first_name,last_name,email,contact_number,password,profile_pic_loc,gender,address,birthdate)
            VALUES ('".$user_type."','".$firstname."','".$lastname."','".$email."','".$phone_number."','".$encrypted."','assets/images/app-elements/default-profile-picture.png','".$gender."','".$address."','".$birthdate."')";

            if ($conn->query($sql) === TRUE) {
                $last_id = $conn->insert_id;
                             $sql_user_data = "SELECT * FROM users WHERE user_id = '".$last_id."' "; 
                    $result_user_data = mysqli_query($conn, $sql_user_data);
                    while ($row_user_data = mysqli_fetch_array($result_user_data)){
                        $display_firstname = $row_user_data["first_name"];
                        $display_gender = $row_user_data["gender"];
                        $display_lastname = $row_user_data["last_name"];
                        $display_birthdate = $row_user_data["birthdate"];
                        $display_contact_number = $row_user_data["contact_number"];
                        $display_account_type = $row_user_data["account_type"];
                        $display_profile_pic_loc = $row_user_data["profile_pic_loc"];
                        $display_address = $row_user_data["address"];
                        $display_system_status = $row_user_data["system_status"];
                        $display_user_status = $row_user_data["user_status"];
                        $display_password = $row_user_data["password"];
                    }


                $response = "Success".$last_id."©".$display_firstname."©".$display_gender."©".$display_lastname."©".$display_birthdate."©".$display_contact_number."©".$display_account_type."©".$display_profile_pic_loc."©".$display_address."©".$display_system_status."©".$display_user_status."©".$display_password;
               
            } else {
               $response= "Error: " . $sql . "<br>" . $database->error;
            }
        }

        echo json_encode($response);
     
    ?>
