<!doctype html>
<html>

<head>
	<style>
		body {
			background-color: gainsboro;
		}

		.menu {
			background-color: white;
			border: 2px solid sienna;
			padding: 16px;
			max-width: 460px;
		}
	</style>
	<title>Recipe App Team 8</title>
</head>

<body>
	<h1>Main Menu</h1>
	<div class="menu">
		<div>
			<!-- redirect buttons that call modal before redirecting -->
			<!-- modal that pops up on screen to confirm or cancel action -->
			<?php
			$modalText = "Are you sure you want to leave this page?";

			$modalId = "1";
			$modalAction = "create/CreateMeal.php";
			$buttonText = "Create a Meal";
			include "includes/Modal.php";

			$modalId = "2";
			$modalAction = "create/CreateRecipe.php";
			$buttonText = "Create a Recipe";
			include "includes/Modal.php";

			$modalId = "3";
			$modalAction = "create/CreateIngredient.php";
			$buttonText = "Create an Ingredient";
			include "includes/Modal.php";

			$modalId = "4";
			$modalAction = "other/GroceryList.php";
			$buttonText = "Create a Grocery List";
			include "includes/Modal.php";

			$modalId = "5";
			$modalAction = "search/SearchRecipe.php";
			$buttonText = "Search for Recipes";
			include "includes/Modal.php";

			$modalId = "6";
			$modalAction = "search/SearchMeal.php";
			$buttonText = "Search for Meals";
			include "includes/Modal.php";

			$modalId = "7";
			$modalAction = "other/CompareRecipes.php";
			$buttonText = "Compare Recipes";
			include "includes/Modal.php";

			?>
		</div>
		<br>
		<div>
			<!-- serving size dropdown and display -->
			<label for="servingSize">Serving Size:</label>
			<select name="servingSize" id="servingSize">
				<option value="1">Normal</option>
				<option value="0.5">Half</option>
				<option value="2">Double</option>
			</select>
			<label id="servingSizeDisplay">
				Serving Size: Normal, 1
			</label>

			<!-- update servingSize modifier and text and update page information upon changing serving size dropdown -->
			<script>
				var e = document.getElementById("servingSize");
				var servingSize = e.value;
				var servingSizeText = e.options[e.selectedIndex].text;

				function onChange() {
					servingSize = e.value;
					servingSizeText = e.options[e.selectedIndex].text;
					console.log(servingSize, servingSizeText);
					document.getElementById('servingSizeDisplay').innerHTML = "Serving Size: " + servingSizeText + ", " + servingSize;
				}
				e.onchange = onChange;
				onChange();
			</script>
		</div>
	</div>
</body>

</html>