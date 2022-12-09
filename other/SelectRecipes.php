<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Select Recipes</title>
</head>
<body>
<?php
	// function takes number of recipes as parameter, a database connection, and an array of
	// last weeks recipes
	function SelectRecipes($numRecipes, $conn, $lastWeekRecipes) {
		// create array to store selected recipes
		$selectedRecipes = [];
		// query the mealrecipe table for a recipe from each meal type
		$mealQuery = "SELECT idMeal FROM meal;";
		$mealResults = mysqli_query($conn, $mealQuery);
		$mealResultsArray = mysqli_fetch_all($mealResults);
		// convert results into array
		$mealArray = [];
		foreach($mealResultsArray as $meal) {
			array_push($mealArray, $meal[0]);
		}
		// string of last week recipes, used in query
		$lastWeekRecipeString = implode(',', array_map("intval", $lastWeekRecipes));
		
		// select numRecipes amount of random recipes
		for ($i = 0; $i < $numRecipes; $i++) {
			// number of selectible meals
			$numMeals = count($mealArray);
			// select random index for meal array
			$mealIndex = rand(0, $numMeals-1);
			// the randomly selected meal
			$selectedMeal = $mealArray[$mealIndex];
			// remove meal at mealIndex from mealArray
			unset($mealArray[$mealIndex]);
			$mealArray = array_values($mealArray);
			// string of meals for query
			$mealString = implode(',', array_map("intval", $selectedRecipes));
			// query string
			$recipeQuery = "";
			// if an array in the query is empty, the query returns no results
			// so only add array to query if count is more than zero
			if (count($selectedRecipes) > 0 && count($lastWeekRecipes) > 0) {
				$recipeQuery = "SELECT idRecipe FROM mealrecipe WHERE idMeal = ".$selectedMeal." AND idRecipe NOT IN(".$mealString.") AND idRecipe NOT IN(".$lastWeekRecipeString.");";
			}
			else if (count($selectedRecipes) > 0) {
				$recipeQuery = "SELECT idRecipe FROM mealrecipe WHERE idMeal = ".$selectedMeal." AND idRecipe NOT IN(".$mealString.");";
			}
			else if (count($lastWeekRecipes) > 0) {
				$recipeQuery = "SELECT idRecipe FROM mealrecipe WHERE idMeal = ".$selectedMeal." AND idRecipe NOT IN(".$lastWeekRecipeString.");";
			}
			else {
				$recipeQuery = "SELECT idRecipe FROM mealrecipe WHERE idMeal = ".$selectedMeal.";";
			}
			// query for recipe in selected meal
			$recipeResults = mysqli_query($conn, $recipeQuery);
			if ($recipeResults && mysqli_num_rows($recipeResults) > 0) {
				$recipeResultsArray = mysqli_fetch_all($recipeResults);
				$recipeIndex = rand(0, mysqli_num_rows($recipeResults)-1);
				// get the recipe id
				$recipeID = $recipeResultsArray[$recipeIndex][0];
				// add it to selectedRecipes
				array_push($selectedRecipes, $recipeID);
			}
			else {
				//print("No Results");
				if ($i < 20) {
					$i--;
				}
			}
			mysqli_free_result($recipeResults);
		}
		mysqli_free_result($mealResults);
		// return array of the chosen recipe ids
		return $selectedRecipes;
	}
	// example connection
	$con = mysqli_connect("mscsdb.uwstout.edu:3306", "mealplanneruser8", "Spaghetti33?", "mealplanner8");
	if(mysqli_connect_errno())
	{
		printf("Connect failed:  %s\n", mysqli_connect_error());
		exit();
	}
	// array for last weeks recipes
	$lweekRecipe = [4,5,6,7,8,9,10,11,12,13,14,15,16,17];
	// example function call
	print_r(SelectRecipes(7, $con, $lweekRecipe));
?>
</body>
</html>
