<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Recipe Creation</title>
	<script type="text/javascript">
// gross global variables
var selectedRecipeIds = [];
var amountOfIngredient = [];
var unitsOfMeasure = [];		
// update the recipe list at the bot =tom
function updateList() {
	var recipeId = document.getElementById("recipe").value;
	var list = document.getElementById("selected_recipes");
	
	var quantity = document.getElementById("quanity").value;
	var measure = document.getElementById("measurement").value;
	
	var recipeName = "";
	try {
		recipeName = document.getElementById("recipe_" + recipeId).label;
	} catch {
		alert("You must enter something into the text box!");
		return;
	}
	// don't include duplicates
	if (!selectedRecipeIds.includes(recipeId)) {
		if (list.innerHTML === "You haven't selected any Ingredients!") {
			// selected recipe name is recipe_{id}
			//ingrediant name
			list.innerHTML = "<p>" + recipeName;
			let par = document.createElement("text");
			//amount label
			par.innerHTML = "Quantity: " + quantity;
			list.appendChild(par);
			//measurement label
			let para = document.createElement("text");
			para.innerHTML = "<br>Unit of Measure: " + measure;
			list.appendChild(para);
		} else {
			// append if the standard text is not there
			//ingrediant name
			list.innerHTML += "<p>" + recipeName;
			let par = document.createElement("text");
			//amount label
			par.innerHTML = "Quantity: " + quantity;
			list.appendChild(par);
			//measurement label
			let para = document.createElement("text");
			para.innerHTML = "<br>Unit of Measure: " + measure;
			list.appendChild(para);
		}
		list.innerHTML += "</p>";
		//works
		selectedRecipeIds.push(recipeId);
		amountOfIngredient.push(quantity);
		unitsOfMeasure.push(measure);
		//alert(selectedRecipeIds);
		// update cookie for php
		document.cookie = "selectedRecipeIds=" + JSON.stringify(selectedRecipeIds);
		document.cookie = "amountOfIngredient=" + JSON.stringify(amountOfIngredient);
		document.cookie = "unitsOfMeasure=" + JSON.stringify(unitsOfMeasure);
	} else {
		alert("You can't insert a Ingredient more than once!");
	}

	// remove text from textbox to make it easier
	document.getElementById("recipe").value = "";
}


</script>
</head>
	<body>
	<!-- Main form to input data -->
		<form action = "recipeCreation.php" method="POST">

			Recipe Title: <input type="text" name="recipeName" id="recipeName" class="form-control">
			<br>
			Prep time: <input type="number" name="prepTime" id="prepTime" class="form-control" value = 0>
			<br>
			Cook time: <input type="number" name="cookTime" id="cookTime" class="form-control" value = 0>
			<br>
			Number of Serving: <input type="number" name="serviceSize" id="serviceSize" class="form-control" value = 1>
			<br>
			Description: <input type="text" name="Description" id="Description" class="form-control">
			<br>
			<label for="recipe">Select Ingredients</label> 
		  	<br>
			<input id="recipe" type='text' name='recipe' list='recipe_dl' placeholder="Search ingreidents...">
			
            <datalist id='recipe_dl'>
<?php
	$conn = new mysqli("mscsdb.uwstout.edu", "mealplanneruser8", "Spaghetti33?", "mealplanner8");
	// Fill dropdown with ingredients
	$result = $conn->query("SELECT name, idIngredient FROM mealplanner8.ingredient");
	$recipenames = "";
	while ($row = $result->fetch_assoc()) {
		$recipenames .= "<option id='recipe_" . $row["idIngredient"] . "' label='" . $row["name"] . "' value='" . $row["idIngredient"] . "'>";
	}
	echo $recipenames;
?>
		  </datalist>
			<label for="quanity"> Amount</label>
		  
			<input id="quanity" type='number' name='recipe' value = 1>
			<label for="measurement">Units of measure</label>
		  <input id="measurement" type='text' name='recipe' value = 'pinch'>
			<input id="recipe_add" type="button" value="Add Ingredient" onclick="updateList()"><br>
			<h2>Selected 
			  <label for="recipe2">Ingredients</label>
              :</h2>
			<p id="selected_recipes">You haven't selected any Ingredients!</p><br>
			<br>
			<button type="submit" class="btn btn-primary">Submit</button>
		</form>
	
</body>
<?php
		if ($_POST){
					$data = $_POST;

					$errors = [];
					foreach(['recipeName', 'serviceSize']as $field)
						if(empty($data[$field])){
							echo 'the '; 
							echo $field;
							echo ' is a required field.';
							die(' Please insert the proper fields');
						}
					if (!empty($errors)){
						echo implode('<br />',$errors);
						exit;
					}
					//database connect
				$con = mysqli_connect("mscsdb.uwstout.edu:3306", "mealplanneruser8", "Spaghetti33?", "mealplanner8");
				//echo 'connected.';

					if(mysqli_connect_errno())
					{
						printf("Connect failed:  %s\n", mysqli_connect_error());
						exit();
					}
					$name = $data['recipeName'];
					$prep = $data['prepTime'];
					$cook = $data['cookTime'];
					$service = $data['serviceSize'];
					$desc = $data['Description'];
					$ingredients = $data['ingredients'];
					// do a database query to get the information for each ingredient 
					$query = "SELECT * FROM mealplanner8.recipe WHERE name = '$name';";
					$results = mysqli_query($con, $query);
					if ($row = mysqli_fetch_array($results)) 
					{
						echo "This recipe already exists with in the systems";
						//echo $number;
					}
					else {
						$query = "SELECT MAX(idRecipe) FROM mealplanner8.recipe;";
						$results2 = mysqli_query($con, $query);
						if (!$results2) 
						{
							print("No results.");
							die("SQL error during query: " . mysqli_error());
						}
						$row = mysqli_fetch_array($results2);
						$number = $row[0];
						$number++;
						$query = "INSERT INTO mealplanner8.recipe (idRecipe, name, prepTime, cookTime, numServings, directions) VALUES ($number, '$name', $prep, $cook, $service, '$desc');";
						if ($con->query($query) == TRUE) {
						  echo "New recipe created successfully";
						} else {
						  echo "Error: " . $query . "error code:" . $con->error;
						}
					}
					mysqli_close($con);
			$recipeids = json_decode($_COOKIE["selectedRecipeIds"]);
			$measure = json_decode($_COOKIE["unitsOfMeasure"]);
			$quanitity = json_decode($_COOKIE["amountOfIngredient"]);
			$i = 0;
			foreach ($recipeids as $recipeid) {
				//echo " no idea what happening";
				$result = $conn->query("INSERT INTO mealplanner8.recipeingredient (idRecipe, idIngredient, quantity, unitMeasurement) VALUES ($number, $recipeid, $quanitity[$i], '$measure[$i]')");
				$i++;
				if (!$result) {
					die("<br>recipe/ingredient connection creation failed! Please try again.");
				}
				else {
				}
			}
		}
?>

</html>