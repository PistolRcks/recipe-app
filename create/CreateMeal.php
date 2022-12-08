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
		// update cookie for php
		document.cookie = "selectedRecipeIds=" + JSON.stringify(selectedRecipeIds);
	} else {
		alert("You can't insert a recipe more than once!");
	}

	// remove text from textbox to make it easier
	document.getElementById("recipe").value = "";
}
</script>
	</head>
	<body>

<!-- Perform requests first, ask for data later -->
<?php
$conn = new mysqli("mscsdb.uwstout.edu", "mealplanneruser8", "Spaghetti33?", "mealplanner8");
$result = $conn->query("SELECT ethnicGroup FROM meal");

// Fill datalist with ethnicities //
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

// Fill datalist with recipes //
$result = $conn->query("SELECT name, idRecipe FROM recipe");

$recipenames = "";
while ($row = $result->fetch_assoc()) {
	$recipenames .= "<option id='recipe_" . $row["idRecipe"] . "' label='" . $row["name"] . "' value='" . $row["idRecipe"] . "'>";
}

// Handle POST requests //
$postresult = "";

// Don't put anything in if we don't need to!
if ($_POST["name"] != "" && $_POST["ethnic"] && isset($_POST["name"]) && isset($_POST["ethnic"])) {
	// Handle input into MealPlanner
	$mealname = $_POST["name"];
	$mealethnic = $_POST["ethnic"];

	// 0) Get new idMeal because apparently it doesn't make those
	$result = $conn->query("SELECT MAX(idMeal) FROM meal");
	if (!$result) {
		die("Meal creation failed! Please try again.");
	}

	$mealid = $result->fetch_row()[0] + 1;

	// 1) Insert into `meal`
	$result = $conn->query("INSERT INTO meal (idMeal, name, ethnicGroup) VALUES ('$mealid', '$mealname', '$mealethnic')");
	if (!$result) {
		die("Meal creation failed! Please try again.");
	}

	// 2) Insert FK's into `mealrecipe`
	$recipeids = json_decode($_COOKIE["selectedRecipeIds"]);
	foreach ($recipeids as $recipeid) {
		$result = $conn->query("INSERT INTO mealrecipe (idMeal, idRecipe) VALUES ($mealid, $recipeid)");
		if (!$result) {
			die("Meal creation failed! Please try again.");
		}
	}

	$postresult = "<p>Meal '$mealname' successfully created!</p>";
}

// close the connection for safety
$conn->close();
echo $recipenames;
echo $ethnicnames;
?>

		<h1>Create a Meal</h1>
		<!-- TODO: Beautify this later -->
		<form method="post">
			<!-- Name input -->
			<label for="name">Meal Name: </label>
			<input type="text" name="name" placeholder="Enter meal name..." required><br>

			<!-- Ethnicity input -->
			<label for="ethnic">Ethnicity: </label>
			<input type='text' name='ethnic' list='ethnic_dl' placeholder="Search ethnicities, or make one..." required>
			<datalist id="ethnic_dl">
				<?php echo $ethnicnames; ?>
			</datalist><br><br>

			<!-- Recipe input -->
			<label for="recipe">Select Recipes</label><br>
			<input id="recipe" type='text' name='recipe' list='recipe_dl' placeholder="Search recipes...">
			<datalist id='recipe_dl'>
				<?php echo $recipenames; ?>
			</datalist>
			<input id="recipe_add" type="button" value="Add Recipe" onclick="updateList()"><br>
			<h2>Selected Recipes:</h2>
			<p id="selected_recipes">You haven't selected any recipes!</p><br>

			<!-- Submit button and result from POST -->
			<!-- TODO: Make modal -->
			<input id="create" type="submit" value="Create Meal">
			<?php echo $postresult; ?>
		</form>
	</body>
</html>
