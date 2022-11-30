<!DOCTYPE html>
<html>

<head>
    <style>
        table,
        th,
        td {
            min-width: 200px;
            border: 1px solid black;
        }
    </style>
    <title>Grocery List Creator</title>
</head>

<body>
    <?php

    session_start();
    $conn = new mysqli("mscsdb.uwstout.edu", "mealplanneruser8", "Spaghetti33?", "mealplanner8");

    class Ingredient
    {
        public $id;
        public $quantity;
        public $unitMeasurement;
        public $name;
        public $type;
    }

    class Recipe
    {
        public $id;
        public $name;
        public $ingredients;
    }

    class Meal
    {
        public $id;
        public $name;
        public $recipes;
    }

    class GroceryList
    {
        public $mealNames;
        public $ingredientsNeeded;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['addMeal']) && !empty($_POST['meal'])) {
            foreach ($_SESSION['allMeals'] as $dbMeal) {
                if ($dbMeal->name === $_POST['meal']) {
                    $_SESSION['groceryList']->mealNames[] = $dbMeal->name;
                    foreach ($dbMeal->recipes as $dbRecipe) {
                        foreach ($dbRecipe->ingredients as $dbIngredient) {
                            $inListAlready = false;
                            foreach ($_SESSION['groceryList']->ingredientsNeeded as $neededIngredient) {
                                if ($dbIngredient->name === $neededIngredient->name) {
                                    $inListAlready = true;
                                    $neededIngredient->quantity += $dbIngredient->quantity;
                                }
                            }
                            if (!$inListAlready) {
                                $_SESSION['groceryList']->ingredientsNeeded[] = $dbIngredient;
                            }
                        }
                    }
                }
            }
        } else if (isset($_POST['clearMeals'])) {
            $_SESSION['groceryList']->mealNames = array();
            $_SESSION['groceryList']->ingredientsNeeded = array();
        }
    } else {

        $_SESSION['allingredients'] = array();
        $allingredients = $conn->query("SELECT * FROM ingredient");
        while ($ingredient = $allingredients->fetch_assoc()) {
            $tempIngredient = new Ingredient();
            $tempIngredient->id = $ingredient["idIngredient"];
            $tempIngredient->name = $ingredient["name"];
            $tempIngredient->type = $ingredient["foodtype"];
            $_SESSION['allingredients'][] = $tempIngredient;
        }

        $_SESSION['groceryList'] = new GroceryList();
        $_SESSION['groceryList']->mealNames = array();
        $_SESSION['groceryList']->ingredientsNeeded = array();

        $_SESSION['allMeals'] = array();
        $meals = $conn->query("SELECT idMeal,name FROM meal");
        while ($meal = $meals->fetch_assoc()) {
            $tempMeal = new Meal();
            $tempMeal->id = $meal["idMeal"];
            $tempMeal->name = $meal["name"];
            $tempMeal->recipes = array();

            $mealrecipes = $conn->query("SELECT idRecipe FROM mealrecipe WHERE idMeal = " . $meal["idMeal"]);
            while ($mealrecipe = $mealrecipes->fetch_assoc()) {
                $tempRecipe = new Recipe();
                $tempRecipe->id = $mealrecipe["idRecipe"];
                $recipes = $conn->query("SELECT name FROM recipe WHERE idRecipe = " . $mealrecipe["idRecipe"]);
                $tempRecipe->name = $recipes->fetch_assoc()["name"];
                $tempRecipe->ingredients = array();

                $recipeingredients = $conn->query("SELECT idIngredient,quantity,unitMeasurement FROM recipeingredient WHERE idRecipe = " . $mealrecipe["idRecipe"]);
                while ($recipeingredient = $recipeingredients->fetch_assoc()) {
                    $tempIngredient = new Ingredient();

                    $tempIngredient->id = $recipeingredient["idIngredient"];
                    foreach ($_SESSION['allingredients'] as $dbIngredient) {
                        if ($tempIngredient->id === $dbIngredient->id) {
                            $tempIngredient->name = $dbIngredient->name;
                            $tempIngredient->type = $dbIngredient->type;
                        }
                    }
                    $tempIngredient->quantity = $recipeingredient["quantity"];
                    $tempIngredient->unitMeasurement = $recipeingredient["unitMeasurement"];

                    $tempRecipe->ingredients[] = $tempIngredient;
                }
                $tempMeal->recipes[] = $tempRecipe;
            }
            $_SESSION['allMeals'][] = $tempMeal;
        }
    }
    ?>
    <form method="post">
        <i>(Click the text box to display available options, or start typing!)</i><br>
        <input type="text" name="meal" list="meals" onmouseover='this.focus()'><br>
        <datalist id="meals">
            <?php
            foreach ($_SESSION['allMeals'] as $meal) {
                echo "<option label='" . $meal->name . "' value='" . $meal->name . "'>";
            }
            ?>
        </datalist>
        <input type="submit" name="addMeal" value="Add Meal to Grocery List!"><br>
        <input type="submit" name="clearMeals" value="Clear the Grocery List!">
        <h1>Grocery List:</h1>
        <i>Needed for making:<br>
            <ul> <?php
                    foreach ($_SESSION['groceryList']->mealNames as $meal) {
                        echo "<li>" . $meal . "</li>";
                    }
                    ?></ul>
        </i>
        <table>
            <tr>
                <th>Ingredient Name:</th>
                <th>Amount Needed:</th>
            </tr>
            <?php
            foreach ($_SESSION['groceryList']->ingredientsNeeded as $ingredient) {
                echo "<tr><td>" . $ingredient->name . "</td><td>" . $ingredient->quantity . " " . $ingredient->unitMeasurement . "</td></tr>";
            }
            ?>
        </table>
    </form>
</body>

</html>
