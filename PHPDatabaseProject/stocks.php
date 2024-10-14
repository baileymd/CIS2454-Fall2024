<?php 

try{
    
    require_once 'models/database.php';
    require_once 'models/stocks.php';
    
    $action = htmlspecialchars(filter_input(INPUT_POST, "action"));   
    
    //get initial data
    $stocks = list_stocks();
    
    $symbol = htmlspecialchars(filter_input(INPUT_POST, "symbol"));
    $name = htmlspecialchars(filter_input(INPUT_POST, "name"));
    $current_price = filter_input(INPUT_POST, "current_price", FILTER_VALIDATE_FLOAT);    
    
    if( $action == "insert_or_update" && $symbol != "" && $name != "" && $current_price != 0){
        $insert_or_update = filter_input(INPUT_POST, 'insert_or_update');
        
        $stock = new Stock($symbol, $name, $current_price);
        
        if($insert_or_update == 'insert'){
            insert_stock($stock);    
        }else if($insert_or_update == 'update'){
            update_stock($stock);
        }
        header("Location: stocks.php");
    }else if($action == "delete" && $symbol != "" ){
        $stock = new Stock($symbol, "", "");
        
        delete_stock($stock);
        header("Location: stocks.php");
    }else if ($action != ""){
        $error_message = "Missing symbol, name, or current price.";
        include('views/error.php');        
    }
      
} catch (Exception $e) {
    $error_message = $e->getMessage();
    include('views/error.php');
}


    //get updated data for tables
    $stocks = list_stocks();
    
    include('views/stocks.php');