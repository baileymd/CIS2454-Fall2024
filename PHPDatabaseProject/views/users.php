<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <title>Users List</title>
    </head>
    <?php include ('views/topNavigation.php'); ?>
    <br>
    <body>
        
        <!--table of users-->
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
        <h2>Add User</h2>
        <form action="users.php" method="post">
            <label>Name:</label>
            <input type="text" name="users_name"/><br>
            <label>Email Address:</label>
            <input type="text" name="email_address"/><br>
            <label>Initial Deposit:</label>
            <input type="text" name="cash_balance"/><br>
            <input type="hidden" name='action' value='add_user'/>
            <label>&nbsp;</label>
            <input type="submit" value="Add User"/>
        </form>
        
        <h2>Update User</h2>
        <form action="users.php" method="post">
            <label>Name:</label>
            <input type="text" name="name_update"/><br>
            <label>Email Address:</label>
            <input type="text" name="email_update"/><br>
            <input type="hidden" name='action' value='update_user'/>
            <label>&nbsp;</label>
            <input type="submit" value="Update User"/>
        </form>
        
        <h2>Delete User</h2>
        <form action="users.php" method="post">
            <label>Name:</label>
            <input type="text" name="name_delete"/><br>
            <label>Email:</label>
            <input type="text" name="email_delete"/><br>
            <input type="hidden" name='action' value='delete_user'/>
            <label>&nbsp;</label>
            <input type="submit" value="Delete User"/>
        </form>
    <br>
    <?php include ('views/footer.php'); ?>
</html>