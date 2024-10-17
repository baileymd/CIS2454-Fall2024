<?php

session_start();

try{
    
    require_once 'models/database.php';
    require_once 'models/login.php';
    
    $message = "";

    $login_email = htmlspecialchars(filter_input(INPUT_POST, "login_email"));
    $login_password = htmlspecialchars(filter_input(INPUT_POST, "login_password"));
    $password_hash = password_hash($login_password, PASSWORD_DEFAULT);
    $action = htmlspecialchars(filter_input(INPUT_GET, "action"));
    
    if($action == "logout"){
        $_SESSION = array();
        session_destroy();
    }

    if( $login_email != "" && $login_password != ""){
        if(login($login_email, $login_password)){
            $_SESSION['is_logged_in'] = true;
        }else {
            $message = "Login failed. Try again.";
        }
    }

    include 'views/login.php';
    
} catch (Exception $e) {
    $error_message = $e->getMessage();
    include('views/error.php');
}