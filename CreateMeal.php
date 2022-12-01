<!doctype html>
<html>
	<head>
		<title>Create Meal</title>
	</head>
	<body>
		<h1>Create a Meal</h1>
		<!-- TODO: Beautify this later -->
		<form action="">
			<label for="recipe">Select Recipes</label><br>
			<input type='text' name='recipe' list='recipe' onmouseover='this.focus()' placeholder="Search recipes...">
			<!-- Shamelessly steal -->
            <datalist id='recipe'>
<?php
// Fill dropdown with recipes
$conn = new mysqli("mscsdb.uwstout.edu", "mealplanneruser8", "Spaghetti33?", "mealplanner8");
$result = $conn->query("SELECT name, idRecipe FROM recipe");

$recipenames = "";
while ($row = $result->fetch_assoc()) {
	$recipenames .= "<option label='" . $row["name"] . "' value='" . $row["idRecipe"] . "'>";
}

echo $recipenames;
?>
			</datalist>
			<!-- TODO: Button should add selected idRecipe to the sql ADD ROW op 
				and add the name to the list
			-->
			<input id="recipe_add" type="button" value="Add Recipe"><br><br>

			<label for="ethnic">Ethnicity: </label>
			<input type='text' name='ethnic' list='ethnic' onmouseover='this.focus()' placeholder="Search ethnicities, or make one...">
			<datalist id="ethnic">
<?php
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

// close the connection for safety
$conn->close();
?>
			</datalist>
			<h2>Selected Recipes:</h2>
			<!-- TODO: Added recipes will go here
				Also this header should probably be dynamically created
			-->
			<p id="selected_recipes">You haven't selected any recipes!</p><br>

			<!-- TODO: Make modal -->
			<input id="create" type="button" value="Create Meal" name="create">
		</form>
	</body>
</html>
