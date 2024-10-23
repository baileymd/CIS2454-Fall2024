<?php

class User {
    private $name, $email_address, $cash_balance, $id;
    
    //constructor for new User object
    public function __construct($name, $email_address, $cash_balance, $id = 0){
        $this->set_name($name);
        $this->set_email_address($email_address);
        $this->set_cash_balance($cash_balance);
        $this->set_id($id);
    }
    
    //getters and setters for User object
    public function get_name() {
        return $this->name;
    }

    public function get_email_address() {
        return $this->email_address;
    }

    public function get_cash_balance() {
        return $this->cash_balance;
    }

    public function get_id() {
        return $this->id;
    }

    public function set_name($name){
        $this->name = $name;
    }

    public function set_email_address($email_address) {
        $this->email_address = $email_address;
    }

    public function set_cash_balance($cash_balance) {
        $this->cash_balance = $cash_balance;
    }

    public function set_id($id) {
        $this->id = $id;
    }
}

function list_users(){
    
    global $database;

    $query = 'SELECT name, email_address, cash_balance, id FROM users';
    
    $statement = $database->prepare($query);
    $statement->execute();
    $users = $statement->fetchAll();
    $statement->closeCursor();

    $users_array =  array();
    
    foreach ($users as $user){
        $users_array[] = new User($user['name'], $user['email_address'], $user['cash_balance'], $user['id']);
    }

    return $users_array;
}

function insert_user($user){
    
    global $database;
    
    $query_users = "INSERT INTO users ( name, email_address, cash_balance) VALUES (:users_name, :email_address, :cash_balance)";
        
    //bind values
    $statement = $database->prepare($query_users);
    $statement->bindValue(":users_name", $user->get_name());
    $statement->bindValue(":email_address", $user->get_email_address());
    $statement->bindValue(":cash_balance", $user->get_cash_balance());

    if($statement->execute()){
        $statement->closeCursor();
    }else{
        $error_message = "<p>Error adding user.</p>";
        include('views/error.php');
    }
}

function update_user($user){
    
    global $database;
    
    $query = "update users set name = :name_update, email_address = :email_update "
               . "where name = :name_update || email_address = :email_update";
        
        //bind values
        $statement = $database->prepare($query);
        $statement->bindValue(":name_update", $user->get_name());
        $statement->bindValue(":email_update", $user->get_email_address());
        
        if($statement->execute()){
            $statement->closeCursor();
        }else{
            $error_message = "<p>Error updating user.</p>";
            include('views/error.php');
        }
}

function delete_user($user){
    
    global $database;
    
    $query = "delete from users where name = :name_delete && email_address = :email_delete";
        
        //bind values
        $statement = $database->prepare($query);
        $statement->bindValue(":name_delete", $user->get_name());
        $statement->bindValue(":email_delete", $user->get_email_address());
        
        if($statement->execute()){
            $statement->closeCursor();
        }else{
            $error_message = "<p>Error deleting stock.</p>";
            include('views/error.php');
        }
}