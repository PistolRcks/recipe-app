<!doctype html>
<html>
	<head>
		<title>Create Menu</title>
	</head>
	<body>
		<form action="">
			<input type='text' name='meal' list='meal' onmouseover='this.focus()'><br><br>
			<!-- Shamelessly steal -->
            <datalist id='meal'>
<?php
$conn = new mysqli("mscsdb.uwstout.edu", "mealplanneruser8", "Spaghetti33?", "mealplanner8");
$result = $conn->query("SELECT name FROM meal");
$mealnames = "";
while ($row = $result->fetch_assoc()) {
	$mealnames .= "<option label='" . $row["name"] . "' value='" . $row["names"] . "'>";
}
echo $mealnames;
?>
            </datalist>
		</form>
	</body>
</html>
