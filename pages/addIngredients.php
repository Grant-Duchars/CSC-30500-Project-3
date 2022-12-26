<?php
// Author: Grant Duchars

session_start();

$ingredient = $_POST["ingredient"];
$quantity = filter_input(INPUT_POST, "quantity", FILTER_VALIDATE_INT);

$conn = mysqli_connect(
    hostname: $_SESSION["hostname"],
    port: $_SESSION["port_num"],
    username: $_SESSION["username"],
    password: $_SESSION["password"],
    database: $_SESSION["database"],
);

if (mysqli_connect_errno()) {
    die("Could not connect:" . mysqli_connect_error());
}

$stmt = $conn->prepare("SELECT * FROM Inventory WHERE Ingredient = ?");
$stmt->bind_param("s", $ingredient);
$stmt->execute();
$stmt->bind_result($ingredient, $available);

$exists = false;
while ($stmt->fetch()) {
    $exists = true;
}

if (!$exists) {
    $stmt = $conn->prepare("INSERT INTO Inventory (Ingredient, Quantity) VALUES (?, ?)");
    $stmt->bind_param("si", $ingredient, $quantity);
    $stmt->execute();
} else {
    $new_quantity = $available + $quantity;
    $stmt = $conn->prepare("UPDATE Inventory
                            SET Quantity = $new_quantity
                            WHERE Ingredient = ?");
    $stmt->bind_param("s", $ingredient);
    $stmt->execute();
}

mysqli_close($conn);

header("Location: addIngredients.html");
