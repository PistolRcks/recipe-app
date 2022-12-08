<!DOCTYPE html>
<html>

<head>
	<style>
		* {
			margin: 0;
			padding: 0;
		}

		body {
			height: 100vh;
			background-color: gainsboro;
			/* alignment */
			display: flex;
			flex-direction: column;
			justify-content: center;
			align-items: center;
		}

		div {
			background: linear-gradient(90deg, sienna 50%, transparent 50%),
				linear-gradient(90deg, sienna 50%, transparent 50%),
				linear-gradient(0deg, sienna 50%, transparent 50%),
				linear-gradient(0deg, sienna 50%, transparent 50%);
			background-repeat: repeat-x, repeat-x, repeat-y, repeat-y;
			background-size: 16px 2px, 16px 2px, 2px 16px, 2px 16px;
			background-position: 0 0, calc(100%) calc(100%), 0 calc(100%), calc(100%) 0;
			background-color: white;
			animation: border-spin 6s infinite linear;
			max-width: 230px;
		}

		@keyframes border-spin {
			0% {
				background-position: 0 0, calc(104%) calc(100%), 0 calc(108%), calc(100%) 0;
			}

			100% {
				background-position: calc(104%) 0, calc(0%) calc(100%), 0 calc(0%), calc(100%) calc(108%);
			}
		}

		p {
			text-align: center;
		}
	</style>
</head>

<body>
	<h1>Ingredient Creator</h1>
	<span style="height: 16px;"></span>
	<div>
		<form method="post" style="padding: 16px;">
			<?php
			// create connection to database
			$conn = new mysqli("mscsdb.uwstout.edu", "mealplanneruser8", "Spaghetti33?", "mealplanner8");

			if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addIngredient'])) {
				$result = $conn->query("SELECT * FROM ingredient");
				$ingredientExists = false;
				while ($row = $result->fetch_assoc()) {
					if (strtolower($_POST['name']) === strtolower($row["name"]) && strtolower($_POST['foodtype']) === strtolower($row["foodtype"])) {
						$ingredientExists = true;
					}
				}
				if (!$ingredientExists) {
					$numIngredients = mysqli_fetch_assoc($conn->query("SELECT COUNT(*) FROM ingredient"))['COUNT(*)'];
					if ($conn->query("INSERT INTO ingredient (idIngredient,name,foodtype) VALUES (" . ++$numIngredients. ",'". $_POST['name'] . "','" . $_POST['foodtype'] . "')") === TRUE) {
						echo "<p style='color: green;'><b>Success!</b> Ingredient added to database.</p><br>";
					} else {
						echo "<p style='color: red;'><b>Error!</b> " . $conn->error . "</p><br>";
					}
				} else {
					echo "<p style='color: red;'><b>Error!</b> Ingredient already exists!</p><br>";
				}
			}
			?>
			<p><b>Ingredient Name:</b></p><br>
			<input type='text' name='name' onmouseover='this.focus()' required><br><br>
			<p><b>Ingredient Type:</b></p>
			<p style="font-size: 9px;"><i>(click the text box to display pre-existing types, or add a new one if it does not exist)</i></p><br>
			<input type='text' name='foodtype' list='foodtype' onmouseover='this.focus()' required><br><br>
			<datalist id='foodtype'>
				<?php
				// send query to find all ingredient types
				$result = $conn->query("SELECT DISTINCT foodtype FROM ingredient");
				// iterate through each type and add it to a datalist
				while ($row = $result->fetch_assoc()) {
					$foodtypes .= "<option label='" . $row["foodtype"] . "' value='" . $row["foodtype"] . "'>";
				}
				echo $foodtypes;

				// close the connection for safety
				$conn->close();
				?>
			</datalist>
			<input style="width: 100%;" name="addIngredient" type="submit" value="Add Ingredient!">
		</form>
	</div>
</body>

</html>