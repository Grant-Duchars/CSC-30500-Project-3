<?php
// Author: Grant Duchars

session_start();

$_SESSION["hostname"] = $_POST["hostname"];
$_SESSION["port_num"] = $_POST["port_num"];
$_SESSION["username"] = $_POST["username"];
$_SESSION["password"] = $_POST["password"];
$_SESSION["database"] = $_POST["database"];

// Try to connect to database using provided information
$conn = mysqli_connect(
    hostname: $_SESSION["hostname"],
    port: $_SESSION["port_num"],
    username: $_SESSION["username"],
    password: $_SESSION["password"],
    database: $_SESSION["database"],
);
// Kill page if connection fails
if (mysqli_connect_errno()) {
    die("Could not connect:" . mysqli_connect_error());
}

// Prepare query to create table if needed
$queryString = "CREATE TABLE IF NOT EXISTS Recipe (
                    RName nvarchar(200) not null, 
                    Ingredient nvarchar(200) not null, 
                    Quantity integer not null, 
                    PRIMARY KEY(RName, Ingredient)
                )";

// Execute query and check for errors
if (!mysqli_query($conn, $queryString)) {
    die("Error creating table: " . mysqli_error($conn));
}

// Prepare query to create table if needed
$queryString = "CREATE TABLE IF NOT EXISTS Inventory (
                    Ingredient nvarchar(200) not null, 
                    Quantity integer not null, 
                    PRIMARY KEY(Ingredient)
                )";

// Execute query and check for errors
if (!mysqli_query($conn, $queryString)) {
    die("Error creating table: " . mysqli_error($conn));
}

mysqli_close($conn);

// Redirect to home page
header("Location: home.html");
