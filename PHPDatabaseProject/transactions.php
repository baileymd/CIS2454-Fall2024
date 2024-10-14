<?php

try{
    
    require_once 'models/database.php';
    require_once 'models/transactions.php';
    require_once 'models/users.php';
    require_once 'models/stocks.php';
    
    $action = htmlspecialchars(filter_input(INPUT_POST, "action"));
    
    $transactions = list_transactions();
    $stocks = list_stocks();
    
    $user_id = htmlspecialchars(filter_input(INPUT_POST, "user_id"));
    $symbol = htmlspecialchars(filter_input(INPUT_POST, "symbol"));
    $quantity = htmlspecialchars(filter_input(INPUT_POST, "quantity"));
    
    $transaction_id = htmlspecialchars(filter_input(INPUT_POST, "transaction_id"));
    
    $transaction_update = htmlspecialchars(filter_input(INPUT_POST, "transaction_update"));
    $symbol_update = htmlspecialchars(filter_input(INPUT_POST, "symbol_update"));
    $quantity_update = htmlspecialchars(filter_input(INPUT_POST, "quantity_update"));
    
    $found_transaction = false; 
    
    if($action == "buy" && $user_id != "" && $symbol != "" && $quantity != ""){
        foreach($stocks as $stock){
            if($stock->get_symbol() == $symbol){
                $stock_id = $stock->get_id();
                $stock_price = $stock->get_current_price();
            }
        }
        
        $transaction = new Transaction($user_id, $stock_id, $quantity, $stock_price);
        
        insert_transaction($transaction);
        header("Location: transactions.php");
    }else if($action == "sell" && $transaction_id != "" ){
        foreach($transactions as $transaction){
            if($transaction->get_id() == $transaction_id){
               $found_transaction = $transaction;
            }
        }
                
        delete_transaction($found_transaction);
        header("Location: transactions.php");
    
    }else if($action == "update_transaction" && $transaction_update != "" && $symbol_update != "" && $quantity_update != ""){
        foreach($transactions as $transaction){
            if($transaction->get_id() == $transaction_update){
               $found_transaction = $transaction;
            }
        }
        
        foreach($stocks as $stock){
            if($stock->get_symbol() == $symbol_update){
                $found_stock = $stock;
            }
        }
        
        $updated_transaction = new Transaction($found_transaction->get_user_id(), $found_stock->get_id(), $quantity_update, $found_stock->get_current_price(), "", $found_transaction->get_id());
        
        update_transaction($updated_transaction);
        header("Location: transactions.php");
    } 
    
}catch (Exception $e) {
    $error_message = $e->getMessage();
    include('views/error.php');
}

    $transactions = list_transactions();
    
    include('views/transactions.php');