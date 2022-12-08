<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Compare Recipes</title>
</head>

<body>
	<?php
	// array of recipe ids, as ints for parameter, and database connection
	function compareRecipes(array $recipeIDs, $conn) {
		// create array of id's for recipes that have same ingredients in that query
		$matchingRecipes = [];
		// query for ingredients to get number of ingredients in database
		$myquery = "SELECT distinct idIngredient FROM recipeingredient";
		$myresults = mysqli_query($conn, $myquery);
		if (!$myresults) 
		{
			print("No results.");
			exit();
		}
		// number of ingredients
		$numIngredients = mysqli_num_rows($myresults);
		// convert all values in array to ints
		$recipeString = implode(',', array_map("intval", $recipeIDs));
		
		// loop through query results and check if recipe's ingredients matches any other
		for ($i = 1; $i < $numIngredients; $i++) {
			// query for all recipes in recipe array that have specified ingredient
			$searchquery = "SELECT distinct idRecipe FROM recipeingredient WHERE idIngredient = ".$i." AND idRecipe IN(".$recipeString.")";
			$searchresults = mysqli_query($conn, $searchquery);
			if (!$searchresults) {
				print("no results");
			}
			// check if search results is valid and has more than one recipe
			if ($searchresults && mysqli_num_rows($searchresults) > 1) {
				// valid and ingredient is in multiple recipes
				// loop through search results
				while ($myfetch = mysqli_fetch_array($searchresults)) {
					// loop through matching recipes array
					$alreadyAdded = false;
					for($j = 0; $j < count($matchingRecipes); $j++) {
						if ($matchingRecipes[$j] == $myfetch[0]) {
							$alreadyAdded = true;
						}
					}
					// if not add them
					if (!$alreadyAdded) {
						array_push($matchingRecipes, $myfetch[0]);
					}
				}
				mysqli_free_result($searchresults);
			}
		}
		mysqli_free_result($myresults);
		// return array
		return $matchingRecipes;
	}
	
	// example connection
	$con = mysqli_connect("mscsdb.uwstout.edu:3306", "mealplanneruser8", "Spaghetti33?", "mealplanner8");
    
	if(mysqli_connect_errno())
	{
		printf("Connect failed:  %s\n", mysqli_connect_error());
		exit();
	}
	// example input (recipes 1-9)
	$testarray = [1,2,3,4,5,6,7,8,9];
	// example function call
	$resultArray = compareRecipes($testarray, $con);
	// prints out the array of recipes with matching ingredients
	for ($x = 0; $x < count($resultArray); $x++) {
		print($resultArray[$x]);
	}
	
	mysqli_close($con);
?>
</body>
</html>
