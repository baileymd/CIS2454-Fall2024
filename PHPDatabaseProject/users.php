<?php

try{
    
    require_once 'utility/ensure_logged_in.php';
    require_once 'models/database.php';
    require_once 'models/users.php';
    
    $action = htmlspecialchars(filter_input(INPUT_POST, "action"));
    
    $users = list_users();
    
    $users_name = htmlspecialchars(filter_input(INPUT_POST, "users_name"));
    $email_address = htmlspecialchars(filter_input(INPUT_POST, "email_address"));
    $cash_balance = filter_input(INPUT_POST, "cash_balance", FILTER_VALIDATE_FLOAT);
    
    $name_update = htmlspecialchars(filter_input(INPUT_POST, "name_update"));
    $email_update = htmlspecialchars(filter_input(INPUT_POST, "email_update"));
    
    $name_delete = htmlspecialchars(filter_input(INPUT_POST, "name_delete"));
    $email_delete = htmlspecialchars(filter_input(INPUT_POST, "email_delete"));
    
    if($action == "add_user" && $users_name != "" && $email_address != "" && $cash_balance != ""){
        $user = new User($users_name, $email_address, $cash_balance);
        
        insert_user($user);   
        header("Location: users.php");
    }else if($action == "update_user" && $name_update != "" && $email_update != ""){
        $user = new User($name_update, $email_update, "");
        
        update_user($user);
        header("Location: users.php");
    }else if($action == "delete_user" && $name_delete != "" && $email_delete != "" ){
        $user = new User($name_delete, $email_delete, "");
        
        delete_user($user);
        header("Location: users.php");
    }else if ($action != ""){
        $error_message = "Missing name, email address, or cash balance.";
        include('views/error.php');        
    }     
    
} catch (Exception $e) {
    $error_message = $e->getMessage();
    include('views/error.php');
}

    $users = list_users();

    include('views/users.php');