<?php

include 'models/database.php';
include 'models/stocks.php';
include 'models/users.php';
include 'models/transactions.php';

$stocks = list_stocks();
$users = list_users();
$transactions = list_transactions();

?>

<!DOCTYPE html>

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
                <td><?php echo $stock->get_symbol(); ?></td>
                <td><?php echo $stock->get_name(); ?></td>
                <td><?php echo $stock->get_current_price(); ?></td>
                <td><?php echo $stock->get_id(); ?></td>
            </tr>
            
            <?php endforeach; ?>
        </table>
<br>
<br>
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
                <td><?php echo $user->get_name(); ?></td>
                <td><?php echo $user->get_email_address(); ?></td>
                <td><?php echo $user->get_cash_balance(); ?></td>
                <td><?php echo $user->get_id(); ?></td>
            </tr>
            
            <?php endforeach; ?>
        </table>
<br>
<br>
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
<br>
<br>