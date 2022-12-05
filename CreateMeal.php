<!doctype html>
<html>
	<head>
		<title>Create Meal</title>
<script type="text/javascript">
// gross global variables
var selectedRecipeIds = [];

// update the recipe list at the bottom
function updateList() {
	var recipeId = document.getElementById("recipe").value;
	var list = document.getElementById("selected_recipes");

	var recipeName = "";
	try {
		recipeName = document.getElementById("recipe_" + recipeId).label;
	} catch {
		alert("You must enter something into the text box!");
		return;
	}

	// don't include duplicates
	if (!selectedRecipeIds.includes(recipeId)) {
		if (list.innerHTML === "You haven't selected any recipes!") {
			// selected recipe name is recipe_{id}
			list.innerHTML = "<p>" + recipeName;
		} else {
			// append if the standard text is not there
			list.innerHTML += "<p>" + recipeName;
		}
		list.innerHTML += "</p>";
		
		selectedRecipeIds.push(recipeId);
	} else {
		alert("You can't insert a recipe more than once!");
	}

	// remove text from textbox to make it easier
	document.getElementById("recipe").value = "";
}
</script>
	</head>
	<body>
		<h1>Create a Meal</h1>
		<!-- TODO: Beautify this later -->
		<form action="">
			<label for="name">Meal Name: </label>
			<input type="text" name="name" placeholder="Enter meal name..."><br>

			<label for="ethnic">Ethnicity: </label>
			<input type='text' name='ethnic' list='ethnic_dl' onmouseover='this.focus()' placeholder="Search ethnicities, or make one...">
			<datalist id="ethnic_dl">
<?php
$conn = new mysqli("mscsdb.uwstout.edu", "mealplanneruser8", "Spaghetti33?", "mealplanner8");
$result = $conn->query("SELECT ethnicGroup FROM meal");

// Make sure there are no duplicates first
$ethniclist = array();
while ($row = $result->fetch_assoc()) {
	// remove trailing whitespace
	$group = preg_replace("/\s+$/", "", $row["ethnicGroup"]);
	if (!in_array($group, $ethniclist)) {
		$ethniclist[] = $group; // Weird how php does pushes
	}
}

// *Then* add options
$ethnicnames = "";
foreach ($ethniclist as $ethnic) {
	$ethnicnames .= "<option label='" . $ethnic . "' value='" . $ethnic . "'>";
}

echo $ethnicnames;
?>
			</datalist><br><br>

			<label for="recipe">Select Recipes</label><br>
			<input id="recipe" type='text' name='recipe' list='recipe_dl' onmouseover='this.focus()' placeholder="Search recipes...">
			<!-- Shamelessly steal -->
            <datalist id='recipe_dl'>
<?php
// Fill dropdown with recipes
$result = $conn->query("SELECT name, idRecipe FROM recipe");

$recipenames = "";
while ($row = $result->fetch_assoc()) {
	$recipenames .= "<option id='recipe_" . $row["idRecipe"] . "' label='" . $row["name"] . "' value='" . $row["idRecipe"] . "'>";
}

echo $recipenames;

// close the connection for safety
$conn->close();
?>

			</datalist>
			<!-- TODO: Button should add selected idRecipe to the sql ADD ROW op 
				and add the name to the list
			-->
			<input id="recipe_add" type="button" value="Add Recipe" onclick="updateList()"><br>
			<h2>Selected Recipes:</h2>
			<p id="selected_recipes">You haven't selected any recipes!</p><br>

			<!-- TODO: Make modal -->
			<input id="create" type="button" value="Create Meal" name="create">
		</form>
	</body>
</html>
