<?php

function login($login_email, $login_password){
    
    global $database;

    $query = 'SELECT email_address, password_hash FROM users '
            . 'WHERE email_address = :login_email';
    
    $statement = $database->prepare($query);
    
    $statement->bindValue(":login_email", $login_email);
    
    $statement->execute();
    $user = $statement->fetch();
    $statement->closeCursor();
    
    if( $user == NULL){
        return false;
    }
    
    $password_hash = $user['password_hash'];
    
    return password_verify($login_password, $password_hash);
}
