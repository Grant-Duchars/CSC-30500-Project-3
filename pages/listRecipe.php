<!-- Author: Grant Duchars -->
<!DOCTYPE html>
<html>

<head>
    <title>List Recipes</title>
    <meta charset="UTF-8" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/dark.css" />
</head>

<body>
    <h1>List a Recipe</h1>

    <table border=1>
        <tr>
            <th>Recipe Name</th>
            <th>Ingredient</th>
            <th>Quantity</th>
        </tr>

        <?php

        session_start();

        $recipe_name = $_POST["recipe_name"];

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

        $stmt = $conn->prepare("SELECT * FROM Recipe WHERE RName = ?");
        $stmt->bind_param("s", $recipe_name);
        $stmt->execute();

        $stmt->bind_result($rname, $ingredient, $quantity);

        while ($stmt->fetch()) {
            echo "<tr> <td>" . $rname . "</td> <td>" . $ingredient . "</td> <td>" . $quantity . "</td> </tr>";
        }
        mysqli_close($conn);
        ?>

    </table>
</body>

</html>