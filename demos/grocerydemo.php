<!DOCTYPE html>
<html>

<head>
	<style>
		body {
			height: 100vh;
			background-color: gainsboro;
		}
		div {
			background-color: white;
			border: 2px solid sienna;
			max-width: 460px;
		}
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

	// load session (data is stored like cookies) and create a connection to the database
	session_start();
	$conn = new mysqli("mscsdb.uwstout.edu", "mealplanneruser8", "Spaghetti33?", "mealplanner8");

	// create Objects for each type we need
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

	// check if we connected to the page by coming from the same page after pressing a button
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		// check if we pressed the addMeal button and the meal field is filled out
		if (isset($_POST['addMeal']) && !empty($_POST['meal'])) {
			// iterate through each meal until we find a match
			foreach ($_SESSION['allMeals'] as $dbMeal) {
				if ($dbMeal->name === $_POST['meal']) {
					$_SESSION['groceryList']->mealNames[] = $dbMeal->name;
					// iterate through each recipe in the meal, and each ingredient in a recipe
					foreach ($dbMeal->recipes as $dbRecipe) {
						foreach ($dbRecipe->ingredients as $dbIngredient) {
							$inListAlready = false;
							/* iterate through all the current grocery list ingredients to see if
							   it exists already in the list. if it does, just add to the amount */
							foreach ($_SESSION['groceryList']->ingredientsNeeded as $neededIngredient) {
								if ($dbIngredient->name === $neededIngredient->name) {
									$inListAlready = true;
									$neededIngredient->quantity += $dbIngredient->quantity;
								}
							}
							/* if the ingredient doesn't exist in the list, add the whole object, 
							   rather than just the quantity */
							if (!$inListAlready) {
								$_SESSION['groceryList']->ingredientsNeeded[] = $dbIngredient;
							}
						}
					}
				}
			}
		}
		/* if we did not press the addMeal button, it is likely that we pressed the clearMeals button,
		   but we will have this check just in case */ else if (isset($_POST['clearMeals'])) {
			$_SESSION['groceryList']->mealNames = array();
			$_SESSION['groceryList']->ingredientsNeeded = array();
		}
	}
	/* check if this is (presumably) the first time in this session that we visit this page. if so,
	   store the database in a session for the user to prevent redundant requests */ else {
		// stores all of the ingredients from the database
		$_SESSION['allingredients'] = array();

		$allingredients = $conn->query("SELECT * FROM ingredient");
		// iterate through all ingredients in the database and save the data in Ingredient objects
		while ($ingredient = $allingredients->fetch_assoc()) {
			$tempIngredient = new Ingredient();
			$tempIngredient->id = $ingredient["idIngredient"];
			$tempIngredient->name = $ingredient["name"];
			$tempIngredient->type = $ingredient["foodtype"];
			$_SESSION['allingredients'][] = $tempIngredient;
		}

		// initialize the grocery list and its subsequent variables
		$_SESSION['groceryList'] = new GroceryList();
		$_SESSION['groceryList']->mealNames = array();
		$_SESSION['groceryList']->ingredientsNeeded = array();

		// stores all of the meals from the database
		$_SESSION['allMeals'] = array();

		$meals = $conn->query("SELECT idMeal,name FROM meal");
		// iterate through all meals in the database and save the data in Meal objects
		while ($meal = $meals->fetch_assoc()) {
			$tempMeal = new Meal();
			$tempMeal->id = $meal["idMeal"];
			$tempMeal->name = $meal["name"];
			$tempMeal->recipes = array();

			/* because Meal objects also store Recipes, we will store recipe data for each
			   respective meal also */
			$mealrecipes = $conn->query("SELECT idRecipe FROM mealrecipe WHERE idMeal = " . $meal["idMeal"]);
			// iterate through all recipes in the database with a matching idMeal
			while ($mealrecipe = $mealrecipes->fetch_assoc()) {
				$tempRecipe = new Recipe();
				$tempRecipe->id = $mealrecipe["idRecipe"];
				$recipes = $conn->query("SELECT name FROM recipe WHERE idRecipe = " . $mealrecipe["idRecipe"]);
				$tempRecipe->name = $recipes->fetch_assoc()["name"];
				$tempRecipe->ingredients = array();

				/* because Recipe objects also store Ingredients, we will store recipe data for
				    each respective ingredient also */
				$recipeingredients = $conn->query("SELECT idIngredient,quantity,unitMeasurement FROM recipeingredient WHERE idRecipe = " . $mealrecipe["idRecipe"]);
				// iterate through all ingredients in the database with a matching idRecipe
				while ($recipeingredient = $recipeingredients->fetch_assoc()) {
					$tempIngredient = new Ingredient();

					$tempIngredient->id = $recipeingredient["idIngredient"];
					/* to minimize load on the SQL server, we can load ingredient data from our array
					   instead of making a SQL query. iterate through the array until we find match */
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

	// close the connection for safety
	$conn->close();
	?>
	<div>
		<form method="post" style="padding: 16px;">
			<i>(Click the text box to display available options, or start typing!)</i><br>
			<input type="text" name="meal" list="meals" onmouseover='this.focus()'><br>
			<datalist id="meals">
				<?php
				foreach ($_SESSION['allMeals'] as $meal) {
					echo "<option label='" . $meal->name . "' value='" . $meal->name . "'>";
				}
				?>
			</datalist><br>
			<input type="submit" name="addMeal" style="width: 200px" value="Add Meal to Grocery List!"><br><br>
			<input type="submit" name="clearMeals" style="width: 200px" value="Clear the Grocery List!">
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
	</div>
</body>

</html>