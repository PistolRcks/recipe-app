<!DOCTYPE html>
<html>

<head>
</head>

<body>
    <?php

    session_start();
    $conn = new mysqli("mscsdb.uwstout.edu", "mealplanneruser8", "Spaghetti33?", "mealplanner8");

    class Ingredient
    {
        public $id;
        public $name;
        public $type;
    }

    class Recipe {
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

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['addMeal']) && !empty($_POST['meal'])) {
            foreach ($_SESSION['meals'] as $meal) {
                if ($meal->id === $_POST['meal']) {
                    echo "M: " . $meal->name . "<br>";
                    foreach ($meal->recipes as $recipe) {
                        echo "R: ". $recipe->name . "<br>";
                        foreach ($recipe->ingredients as $ingredient) {
                            foreach ($_SESSION['ingredients'] as $ingredientdata) {
                                if ($ingredient === $ingredientdata->id) {
                                    echo "I: ". $ingredientdata->name . "<br>";
                                }
                            }
                        }
                    }
                }
            }
        } else if (isset($_POST['clearMeals'])) {
        }
    } else {
        $_SESSION['meals'] = array();
        $meals = $conn->query("SELECT idMeal,name FROM meal");
        while ($row = $meals->fetch_assoc()) {
            $tempMeal = new Meal();
            $tempMeal->id = $row["idMeal"];
            $tempMeal->name = $row["name"];
            $tempMeal->recipes = array();
            $mealrecipe = $conn->query("SELECT idRecipe FROM mealrecipe WHERE idMeal = " . $row["idMeal"]);
            while ($row = $mealrecipe->fetch_assoc()) {
                $tempRecipe = new Recipe();
                $tempRecipe->id = $row["idRecipe"];
                $recipe = $conn->query("SELECT name FROM recipe WHERE idRecipe = " . $row["idRecipe"]);
                while ($row2 = $recipe->fetch_assoc()) {
                    $tempRecipe->name = $row2["name"];
                }
                $tempRecipe->ingredients = array();
                $recipeingredient = $conn->query("SELECT idIngredient FROM recipeingredient WHERE idRecipe = " . $row["idRecipe"]);
                while ($row3 = $recipeingredient->fetch_assoc()) {
                    $tempRecipe->ingredients[] = $row3["idIngredient"];
                }
                $tempMeal->recipes[] = $tempRecipe;
            }
            $_SESSION['meals'][] = $tempMeal;
        }

        $_SESSION['ingredients'] = array();
        $ingredients = $conn->query("SELECT * FROM ingredient");
        while ($row = $ingredients->fetch_assoc()) {
            $tempIngredient = new Ingredient();
            $tempIngredient->id = $row["idIngredient"];
            $tempIngredient->name = $row["name"];
            $tempIngredient->type = $row["foodtype"];
            $_SESSION['ingredients'][] = $tempIngredient;
        }
    }
    ?>
    <form method="post">
        <input type="text" name="meal" list="meals">
        <datalist id="meals">
            <?php
            foreach ($_SESSION['meals'] as $meal) {
                echo "<option label='" . $meal->name . "' value='" . $meal->id . "'>";
            }
            ?>
        </datalist>
        <input type="submit" name="addMeal" value="Add Meal to Grocery List!">
        <input type="submit" name="clearMeals" value="Clear Grocery List!">
    </form>
</body>

</html>