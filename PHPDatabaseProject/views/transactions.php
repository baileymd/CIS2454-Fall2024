<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <title>Stocks List</title>
    </head>
    <?php include 'views/topNavigation.php'; ?>
    <br>
    <body>
  
        <!--table of transactions-->
        <h2>Transactions</h2>
        <table>
            <tr>
                <th>User ID</th>
                <th>Stock ID</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Timestamp</th>
                <th>ID</th>
            </tr>
            <?php foreach($transactions as $transaction) : ?>
            <tr>
                <td><?php echo $transaction->get_user_id(); ?></td>
                <td><?php echo $transaction->get_stock_id(); ?></td>
                <td><?php echo $transaction->get_quantity(); ?></td>
                <td><?php echo $transaction->get_price(); ?></td>
                <td><?php echo $transaction->get_timestamp(); ?></td>
                <td><?php echo $transaction->get_id(); ?></td>
            </tr>
            
            <?php endforeach; ?>
        </table>
        
        
        
        <h2>Add Transaction (Buy)</h2>
        <form action="transactions.php" method="post">
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
        <form action="transactions.php" method="post">
            <label>Transaction ID:</label>
            <input type="text" name="transaction_id"/><br>
            <input type="hidden" name='action' value='sell'/>
            <label>&nbsp;</label>
            <input type="submit" value="Sell"/>
        </form>
        
        <h2>Update Transaction</h2>
        <form action="transactions.php" method="post">
            <label>Transaction ID:</label>
            <input type="text" name="transaction_update"/><br>
            <label>Symbol:</label>
            <input type="text" name="symbol_update"/><br>
            <label>Quantity:</label>
            <input type="text" name="quantity_update"/><br>
            <input type="hidden" name='action' value='update_transaction'/>
            <label>&nbsp;</label>
            <input type="submit" value="Update Transaction"/>
        </form>

    </body>
    <br>
    <?php include 'views/footer.php'; ?>
</html>
