<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Untitled Document</title>
</head>
<?php
	$data = $_POST;
	$errors = [];
	foreach(['IngreditName', 'IngreditType']as $field)
		if(empty($data[$field])){
			$errors = sprintf('the %s is a required field.',$field);
			echo 'empty type.';
		}
	if (!empty($errors)){
		echo implode('<br />',$errors);
		exit;
	}
	//database connect
$con = mysqli_connect("mscsdb.uwstout.edu:3306", "meals", "Spaghetti33?", "mealplanner");
echo 'connected.';

	if(mysqli_connect_errno())
	{
		printf("Connect failed:  %s\n", mysqli_connect_error());
		exit();
	}
	echo 'query.';
	$name = $data['IngreditName'];
	$type = $data['IngreditType'];
	// do a database query to get the information for each ingredient 
	$query = "SELECT * FROM mealplanner8.ingredient WHERE name = '$name';";
	//$query->execute(['IngreditName' => $data['IngreditName']]);
	$results = mysqli_query($con, $query);
	echo 'finish';
	//if (!empty($results->fetch())) {
    //echo 'That food exists in this database.';
    //exit;
	//}
	echo 'test20.           -';
	//$query = "SELECT MAX(idIngredient) FROM mealplanner8.ingredient;";
	$number = 149;
	echo $number;
    $query = "INSERT INTO mealplanner8.ingredient (idIngredient, name, foodtype) VALUES ('$number', '$name', '$type');";
	//INSERT INTO mealplanner8.ingredient (idIngredient, name, foodtype) VALUES (140,"test","test");
	//$con->prepare($query)->execute([$number, $name, $type]);
	//$query->execute([
		//'idIngredient'=> $number++,
		//'name' => $data['IngreditName'],
		//'foodtype' => $data['IngreditType']
	//]);
	/*
	if ($conn->query($sql) === TRUE) {
  echo "New record created successfully";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}*/
    
if ($con->query($query) == TRUE) {
  echo "New record created successfully";
} else {
  echo "Error: " . $query . "<br>" . $conn->error;
}
	mysqli_close($con);
// check email SELECT * FROM mealplanner8.ingredient;
/*$statement = $pdo->prepare('SELECT * FROM mealplanner8.ingredient WHERE name = :IngreditName');
$statement->execute(['IngreditName' => $data['IngreditName']]);

if (!empty($statement->fetch())) {
    echo 'That food exists in this database.';
    exit;
}
//insert new user
$statement = $pdo->prepare(
    'INSERT INTO mealplanner8.ingredient (name, foodtype) VALUES (:name, :foodtype)'
);
$statement->execute([
    'name' => $data['IngreditName'],
    'foodtype' => $data['IngreditType'],
]);

echo 'The user has been successfully saved.';
	mysqli_close($con);*/
	?>
<body>
</body>
</html>