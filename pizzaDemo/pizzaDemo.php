<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pizza Order Form</title>
</head>
<body>
    <h1>Place Order</h1>
    <form action="pizzaDemo.php" method="post">
        <h3>Select Size:</h3>
        <input type="radio" name="size" value="small" id="small" required>
        <label for="small">Small - $5</label><br>
        <input type="radio" name="size" value="medium" id="medium">
        <label for="medium">Medium - $7</label><br>
        <input type="radio" name="size" value="large" id="large">
        <label for="large">Large - $9</label><br>

        <h3>Select Toppings:</h3>
        <h5>Each topping $0.50 for small, $1 for medium, $1.50 for large</h5>
        <input type="checkbox" name="toppings[]" value="pepperoni"> Pepperoni<br>
        <input type="checkbox" name="toppings[]" value="mushrooms"> Mushrooms<br>
        <input type="checkbox" name="toppings[]" value="onions"> Onions<br>
        <input type="checkbox" name="toppings[]" value="sausage"> Sausage<br>
        <input type="checkbox" name="toppings[]" value="bacon"> Bacon<br>
        <input type="checkbox" name="toppings[]" value="extra cheese"> Extra Cheese<br>

        <br>
        <input type="submit" value="Order Pizza">
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        //get size of pizza and toppings from form
        $size = $_POST['size'];
        if (isset($_POST['toppings'])) {
            $toppings = $_POST['toppings'];
        } else {
            $toppings = [];
        }

        //calculate cost
        $totalCost = 0;
        if ($size == "small") {
            $totalCost += 5;
            $toppingCost = 0.50;
        } elseif ($size == "medium") {
            $totalCost += 7;
            $toppingCost = 1.00;
        } elseif ($size == "large") {
            $totalCost += 9;
            $toppingCost = 1.50;
        }

        //add topping costs
        foreach ($toppings as $topping) {
            $totalCost += $toppingCost;
        }

        //display order
        echo "<h2>Your Pizza Order</h2>";
        echo "<p>Size: " . $size . "</p>";
        echo "<p>Toppings: ";

        if (empty($toppings)) {
            echo "None"; //no toppings selected
        } else {
            $firstItem = true; //no comma before first item
            foreach ($toppings as $topping) {
                if (!$firstItem) {
                    echo ", "; //comma before each topping except the first
                }
                echo $topping; //display topping
                $firstItem = false; //set to false after the first item
            }
        }

        echo "</p>";
        echo "<p>Total Cost: $" . number_format($totalCost, 2) . "</p>";
    }
    ?>
</body>
</html>