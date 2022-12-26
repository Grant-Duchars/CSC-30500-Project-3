<?php
// Author: Grant Duchars

// Dont know why this script is failing.
// Its really hard to debug it because php is not sending the strings from the die() calls
// so I dont know at which point it is failing.

session_start();

$recipe_name = $_POST["recipe_name"];

// Connect to the mysql server
$conn = new mysqli(
    hostname: $_SESSION["hostname"],
    port: $_SESSION["port_num"],
    username: $_SESSION["username"],
    password: $_SESSION["password"],
    database: $_SESSION["database"],
);

if ($conn->connect_error) {
    die("Could not connect:" . mysqli_connect_error());
}

// Start the transaction to protect from other users and rollback if not enough quantity
$conn->begin_transaction();

// Check if any ingredients in recipe dont exist in inventory
$stmt = $conn->prepare("SELECT *
                        FROM Recipe
                        WHERE Recipe.RName = ? AND NOT EXISTS (
                            select *
                            from Inventory
                            where Inventory.Ingredient = Recipe.Ingredient
                            )");
$stmt->bind_param("s", $recipe_name);
$stmt->execute();
$stmt->bind_result($rname, $ingredient, $quantity);

while ($stmt->fetch()) {
    $conn->rollback();
    die("Unable to buy ingredients, some ingredients do not exist in inventory");
}

// Get all ingredients in the recipe and the quantity available in the inventory
$stmt = $conn->prepare("SELECT Recipe.RName, Recipe.Ingredient, Recipe.Quantity, Inventory.Quantity AS Available
                        FROM Recipe, Inventory
                        WHERE Recipe.RName = ? AND Recipe.Ingredient = Inventory.Ingredient");
$stmt->bind_param("s", $recipe_name);
$stmt->execute();
$stmt->bind_result($rname, $ingredient, $quantity, $available);

while ($stmt->fetch()) {
    // Check if buying this ingredient would make inventory go negative
    if ($available - $quantity < 0) {
        // If so rollback any changes from this transaction
        $conn->rollback();
        die("Unable to buy ingredients, some ingredients were not in stock");
    }
    //Otherwise remove the quantity needed from inventory
    $new_quantity = $available - $quantity;
    $conn->query("UPDATE Inventory
                  SET Quantity = $new_quantity
                  WHERE Ingredient = $ingredient");
}
$conn->commit();

$conn->close();

echo "<h2>Successfully bought all ingredients for $recipe_name</h2>";
