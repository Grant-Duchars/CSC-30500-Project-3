<?php
// Author: Grant Duchars

session_start();

$recipe_name = $_POST["recipe_name"];
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

// Why will php not work with the other functions described in the mysqli documentation?
// Who knows!

$stmt = $conn->prepare("INSERT INTO Recipe (RName, Ingredient, Quantity) VALUES (?, ?, ?)");
$stmt->bind_param("ssi", $recipe_name, $ingredient, $quantity);
$stmt->execute();

mysqli_close($conn);

header("Location: addRecipe.html");
