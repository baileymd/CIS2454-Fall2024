<?php 

$data_source_name = 'mysql:host=localhost;dbname=stock';
$user_name = 'stockuser';
$password = 'test';

try{
    
    $database = new PDO($data_source_name, $user_name, $password);
    echo "<p>Database connection successful</p>";
    
    $action = htmlspecialchars(filter_input(INPUT_POST, "action"));
    
    function fetchData($database, $query){
        $statement = $database->prepare($query);
        $statement->execute();
        $data = $statement->fetchAll();
        $statement->closeCursor();
        
        return $data;
    }
    
    
    //get initial data
    $stocks = fetchData($database, 'SELECT symbol, name, current_price, id FROM stocks');
    $users = fetchData($database, 'SELECT name, email_address, cash_balance, id FROM users');
    $transactions = fetchData($database, 'SELECT user_id, stock_id, quantity, price, timestamp, id FROM transaction');
    
    
    
    
    $symbol = htmlspecialchars(filter_input(INPUT_POST, "symbol"));
    $name = htmlspecialchars(filter_input(INPUT_POST, "name"));
    $current_price = filter_input(INPUT_POST, "current_price", FILTER_VALIDATE_FLOAT);
    
    $users_name = htmlspecialchars(filter_input(INPUT_POST, "users_name"));
    $email_address = htmlspecialchars(filter_input(INPUT_POST, "email_address"));
    $cash_balance = filter_input(INPUT_POST, "cash_balance", FILTER_VALIDATE_FLOAT);
    
    $user_id = htmlspecialchars(filter_input(INPUT_POST, "user_id"));
    $quantity = htmlspecialchars(filter_input(INPUT_POST, "quantity"));
    
    $transaction_id = htmlspecialchars(filter_input(INPUT_POST, "transaction_id"));
    
    
    if( $action == "insert" && $symbol != "" && $name != "" && $current_price != 0){
        /* bad way to do - SQL injection risk - don't plug values into query
        $query = "INSERT INTO stocks (symbol, name, current_price) "
         *      . "VALUES ($symbol, $name, $current_price)";
        */
        
        //use substitutions with colon
        $query = "INSERT INTO stocks (symbol, name, current_price) "
               . "VALUES (:symbol, :name, :current_price)";
        
        //bind values
        $statement = $database->prepare($query);
        $statement->bindValue(":symbol", $symbol);
        $statement->bindValue(":name", $name);
        $statement->bindValue(":current_price", $current_price);
        
        $statement->execute();
        
        $statement->closeCursor();
    }else if($action == "update" && $symbol != "" && $name != "" && $current_price != 0){
        $query = "update stocks set name = :name, current_price = :current_price "
               . "where symbol = :symbol";
        
        //bind values
        $statement = $database->prepare($query);
        $statement->bindValue(":symbol", $symbol);
        $statement->bindValue(":name", $name);
        $statement->bindValue(":current_price", $current_price);
        
        $statement->execute();
        
        $statement->closeCursor();
        
    }else if($action == "delete" && $symbol != "" ){
        $query = "delete from stocks "
               . "where symbol = :symbol";
        
        //bind values
        $statement = $database->prepare($query);
        $statement->bindValue(":symbol", $symbol);
        
        $statement->execute();
        
        $statement->closeCursor();
        
    }else if($action == "add_user" && $users_name != "" && $email_address != "" && $cash_balance != ""){
        $query_users = "INSERT INTO users ( name, email_address, cash_balance) "
               . "VALUES (:users_name, :email_address, :cash_balance)";
        
        //bind values
        $statement_users = $database->prepare($query_users);
        $statement_users->bindValue(":users_name", $users_name);
        $statement_users->bindValue(":email_address", $email_address);
        $statement_users->bindValue(":cash_balance", $cash_balance);
            
        $statement_users->execute();
        
        $statement_users->closeCursor();
        
    }else if($action == "buy" && $user_id != "" && $symbol != "" && $quantity != ""){    
        foreach($users as $user){
            if ($user['id'] == $user_id){
                foreach($stocks as $stock){
                    if($stock['symbol'] == $symbol){
                        if($user['cash_balance'] > ($stock['current_price'] * $quantity)){
                            $user_id = $user['id'];
                            $stock_id = $stock['id'];
                            $price = $stock['current_price'];
                            
                            //add row to transactions
                            $query = "INSERT INTO transaction ( user_id, stock_id, quantity, price) "
                                    . "VALUES (:user_id, :stock_id, :quantity, :price)";
                            
                            //bind values
                            $statement_users = $database->prepare($query);
                            $statement_users->bindValue(":user_id", $user_id);
                            $statement_users->bindValue(":stock_id", $stock_id);
                            $statement_users->bindValue(":quantity", $quantity);
                            $statement_users->bindValue(":price", $price);

                            if($statement_users->execute()){
                                $statement_users->closeCursor();

                                //calculate user's new cash balance
                                $cash_balance = $user['cash_balance'] - ($stock['current_price'] * $quantity);

                                //update user cash balance
                                $query = "update users set cash_balance = :cash_balance "
                                    . "where id = :user_id";

                                //value binding in PDO protects against SQL injection
                                $statement = $database->prepare($query);
                                $statement->bindValue(":user_id", $user_id);
                                $statement->bindValue(":cash_balance", $cash_balance);

                                if($statement->execute()){
                                    $statement->closeCursor();
                                }else {
                                    echo "<p>Error updating user's cash balance.</p>";
                                }
                            }else{
                                echo "<p>Error adding transaction.</p>";
                            }
                        }else {
                            echo "<p>Insufficient funds.</p>";
                        }
                    }
                }
            }            
        }
        
    }else if($action == "sell" && $transaction_id != "" ){
        foreach($transactions as $transaction){
            echo $transaction['id'];
            if ($transaction['id'] == $transaction_id){
                foreach($users as $user){
                    if ($transaction['user_id'] == $user['id']){
                        foreach($stocks as $stock){
                            if ($transaction['stock_id'] == $stock['id']){
                                $cash_balance = $user['cash_balance'] + ($stock['current_price'] * $transaction['quantity']);
                                
                                //update user cash balance
                                $query = "update users set cash_balance = :cash_balance "
                                    . "where id = :user_id";

                                //value binding in PDO protects against SQL injection
                                $statement = $database->prepare($query);
                                $statement->bindValue(":user_id", $user['id']);
                                $statement->bindValue(":cash_balance", $cash_balance);

                                if($statement->execute()){
                                    $statement->closeCursor();
                                }else {
                                    echo "<p>Error updating user's cash balance.</p>";
                                }
                            }else {
                                echo "<p>Error after stocks foreach</p>";
                            }
                        }
                    }else {
                        echo "<p>Error after users foreach</p>";
                    }
                }
            }else {
                echo "<p>Error after transactions foreach</p>";
            }  
        }
        
        $query = "delete from transaction "
               . "where id = :transaction_id";
        
        //bind values
        $statement = $database->prepare($query);
        $statement->bindValue(":transaction_id", $transaction_id);
        
        $statement->execute();
        
        $statement->closeCursor();
    } 
} catch (Exception $e) {
    $error_message = $e->getMessage();
    echo "<p>Error message: $error_message </p>";
}


    //get updated data
    $stocks = fetchData($database, 'SELECT symbol, name, current_price, id FROM stocks');
    $users = fetchData($database, 'SELECT name, email_address, cash_balance, id FROM users');
    $transactions = fetchData($database, 'SELECT user_id, stock_id, quantity, price, timestamp, id FROM transaction');
    

?>

<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <h2>Stock Database</h2>
        <table>
            <tr>
                <th>Name</th>
                <th>Symbol</th>
                <th>Current Price</th>
                <th>ID</th>
            </tr>
            <?php foreach($stocks as $stock) : ?>
            <tr>
                <td><?php echo $stock['symbol']; ?></td>
                <td><?php echo $stock['name']; ?></td>
                <td><?php echo $stock['current_price']; ?></td>
                <td><?php echo $stock['id']; ?></td>
            </tr>
            
            <?php endforeach; ?>
        </table>
        
        <h2>User Database</h2>
        <table>
            <tr>
                <th>Name</th>
                <th>Email Address</th>
                <th>Cash Balance</th>
                <th>ID</th>
            </tr>
            <?php foreach($users as $user) : ?>
            <tr>
                <td><?php echo $user['name']; ?></td>
                <td><?php echo $user['email_address']; ?></td>
                <td><?php echo $user['cash_balance']; ?></td>
                <td><?php echo $user['id']; ?></td>
            </tr>
            
            <?php endforeach; ?>
        </table>
        
        <h2>Transactions</h2>
        <table>
            <tr>
                <th>User ID</th>
                <th>Stock ID</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Timestamp</th>
                <th>Transaction ID</th>
            </tr>
            <?php foreach($transactions as $transaction) : ?>
            <tr>
                <td><?php echo $transaction['user_id']; ?></td>
                <td><?php echo $transaction['stock_id']; ?></td>
                <td><?php echo $transaction['quantity']; ?></td>
                <td><?php echo $transaction['price']; ?></td>
                <td><?php echo $transaction['timestamp']; ?></td>
                <td><?php echo $transaction['id']; ?></td>
            </tr>
            
            <?php endforeach; ?>
        </table>

        <br>
        <h2>Add Stock</h2>
        <form action="index.php" method="post">
            <label>Symbol:</label>
            <input type="text" name="symbol"/><br>
            <label>Name:</label>
            <input type="text" name="name"/><br>
            <label>Current Price:</label>
            <input type="text" name="current_price"/><br>
            <input type="hidden" name='action' value='insert'/>
            <label>&nbsp;</label>
            <input type="submit" value="Add Stock"/>
        </form>
        
        <h2>Update Stock</h2>
        <form action="index.php" method="post">
            <label>Symbol:</label>
            <input type="text" name="symbol"/><br>
            <label>Name:</label>
            <input type="text" name="name"/><br>
            <label>Current Price:</label>
            <input type="text" name="current_price"/><br>
            <input type="hidden" name='action' value='update'/>
            <label>&nbsp;</label>
            <input type="submit" value="Update Stock"/>
        </form>
        
        <h2>Delete Stock</h2>
        <form action="index.php" method="post">
            <label>Symbol:</label>
            <input type="text" name="symbol"/><br>
            <input type="hidden" name='action' value='delete'/>
            <label>&nbsp;</label>
            <input type="submit" value="Delete Stock"/>
        </form>
        
        <h2>Add User</h2>
        <form action="index.php" method="post">
            <label>Name:</label>
            <input type="text" name="users_name"/><br>
            <label>Email Address:</label>
            <input type="text" name="email_address"/><br>
            <label>Initial Deposit:</label>
            <input type="text" name="cash_balance"/><br>
            <input type="hidden" name='action' value='add_user'/>
            <label>&nbsp;</label>
            <input type="submit" value="Add user"/>
        </form>
        
        <h2>Add Transaction (Buy)</h2>
        <form action="index.php" method="post">
            <label>User ID:</label>
            <input type="text" name="user_id"/><br>
            <label>Symbol:</label>
            <input type="text" name="symbol"/><br>
            <label>Quantity:</label>
            <input type="text" name="quantity"/><br>
            <input type="hidden" name='action' value='buy'/>
            <label>&nbsp;</label>
            <input type="submit" value="Buy"/>
        </form>
        
        <h2>Delete Transaction (Sell)</h2>
        <form action="index.php" method="post">
            <label>Transaction ID:</label>
            <input type="text" name="transaction_id"/><br>
            <input type="hidden" name='action' value='sell'/>
            <label>&nbsp;</label>
            <input type="submit" value="Sell"/>
        </form>

    </body>
</html>
