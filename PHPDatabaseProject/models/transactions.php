<?php
require_once 'models/database.php';
require_once 'models/stocks.php';
require_once 'models/users.php';

class Transaction {
    private $user_id, $stock_id, $quantity, $price, $timestamp, $id;
    
    //constructor for new Transaction object
    public function __construct($user_id, $stock_id, $quantity, $price, $timestamp = 0, $id = 0){
        $this->set_user_id($user_id);
        $this->set_stock_id($stock_id);
        $this->set_quantity($quantity);
        $this->set_price($price);
        $this->set_timestamp($timestamp);
        $this->set_id($id);
    }
    
    
    //getters and setters for Transaction object
    public function get_user_id() {
        return $this->user_id;
    }

    public function get_stock_id() {
        return $this->stock_id;
    }

    public function get_quantity() {
        return $this->quantity;
    }

    public function get_price() {
        return $this->price;
    }

    public function get_timestamp() {
        return $this->timestamp;
    }

    public function get_id() {
        return $this->id;
    }

    public function set_user_id($user_id) {
        $this->user_id = $user_id;
    }

    public function set_stock_id($stock_id) {
        $this->stock_id = $stock_id;
    }

    public function set_quantity($quantity) {
        $this->quantity = $quantity;
    }

    public function set_price($price) {
        $this->price = $price;
    }

    public function set_timestamp($timestamp) {
        $this->timestamp = $timestamp;
    }

    public function set_id($id) {
        $this->id = $id;
    }   
}    

function list_transactions(){
    
    global $database;
            
    $query = 'SELECT user_id, stock_id, quantity, price, timestamp, id FROM transaction';
    
    $statement = $database->prepare($query);
    $statement->execute();
    $transactions = $statement->fetchAll();
    $statement->closeCursor();

    $transactions_array =  array();
    
    //add each transaction to array
    foreach ($transactions as $transaction){
        $transactions_array[] = new Transaction($transaction['user_id'], $transaction['stock_id'], 
                $transaction['quantity'], $transaction['price'], $transaction['timestamp'], $transaction['id']);
    }

    return $transactions_array;
}

function insert_transaction($transaction){
    
    global $database;
    
    $stocks = list_stocks();
    $users = list_users();
    
    //find user from transaction
    foreach($users as $user){
        if ($user->get_id() == $transaction->get_user_id()){
            $found_user = $user;
        }            
    }
    
    //find stock from transaction
    foreach($stocks as $stock){
        if($stock->get_id() == $transaction->get_stock_id()){
            $found_stock = $stock;
        }
    }
    
    //check users cash balance, add transaction if enough
    if($found_user->get_cash_balance() > ($found_stock->get_current_price() * $transaction->get_quantity())){
        $query = "INSERT INTO transaction ( user_id, stock_id, quantity, price) "
                                    . "VALUES (:user_id, :stock_id, :quantity, :price)";
        
        $statement = $database->prepare($query);
        $statement->bindValue(":user_id", $transaction->get_user_id());
        $statement->bindValue(":stock_id", $transaction->get_stock_id());
        $statement->bindValue(":quantity", $transaction->get_quantity());
        $statement->bindValue(":price", $transaction->get_price());
        
        if($statement->execute()){
            $statement->closeCursor();

            //calculate user's new cash balance
            $updated_cash_balance = $found_user->get_cash_balance() - ($found_stock->get_current_price() * $transaction->get_quantity());

            //update user's cash balance
            $query = "update users set cash_balance = :cash_balance where id = :user_id";

            $statement = $database->prepare($query);
            $statement->bindValue(":user_id", $found_user->get_id());
            $statement->bindValue(":cash_balance", $updated_cash_balance);

            if($statement->execute()){
                $statement->closeCursor();
            }else {
                $error_message = "<p>Error updating user's cash balance.</p>";
                include('views/error.php');
            }
        }else{
            $error_message = "<p>Error adding transaction.</p>";
            include('views/error.php');
        }
    }else {
        $error_message = "<p>Insufficient funds.</p>";
        include('views/error.php');
    }                       
}

function delete_transaction($transaction){
    
    global $database;
    
    $users = list_users();
    $stocks = list_stocks();
    
    //user id of transaction matches id in user database
    foreach($users as $user){
        if ($transaction->get_user_id() == $user->get_id()){    
            $found_user = $user;
        }
    }
    
    //stock id of transaction matches id in stock database
    foreach($stocks as $stock){
        if($transaction->get_stock_id() == $stock->get_id()){
            $found_stock = $stock;
        }
    }

    //calculate user's new cash balance
    $updated_cash_balance = $found_user->get_cash_balance() + ($found_stock->get_current_price() * $transaction->get_quantity());
    
    //update user's cash balance
    $query = "update users set cash_balance = :cash_balance where id = :user_id";

    //bind values
    $statement = $database->prepare($query);
    $statement->bindValue(":user_id", $found_user->get_id());
    $statement->bindValue(":cash_balance", $updated_cash_balance);

    if($statement->execute()){
        $statement->closeCursor();

        $query = "delete from transaction where id = :transaction_id";  //remove transaction

        //bind values
        $statement = $database->prepare($query);
        $statement->bindValue(":transaction_id", $transaction->get_id());

        $statement->execute();

        $statement->closeCursor();
    }else {
        $error_message = "<p>Error updating user's cash balance.</p>";
        include('views/error.php');
    }
    
}

function update_transaction($transaction){
    
    global $database;
    
    $query = "update transaction set stock_id = :id_update, quantity = :quantity_update where id = :transaction_update ";

    //bind values
    $statement = $database->prepare($query);
    $statement->bindValue(":id_update", $transaction->get_stock_id());
    $statement->bindValue(":quantity_update", $transaction->get_quantity());
    $statement->bindValue(":transaction_update", $transaction->get_id());

    if($statement->execute()){
        $statement->closeCursor();
    }else{
        $error_message = "<p>Error updating transaction.</p>";
        include('views/error.php');
    }
}
