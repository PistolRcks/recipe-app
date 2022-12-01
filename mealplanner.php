<!DOCTYPE HTML>
<html lang = "en">
  <head>
    <meta charset="utf-8">
    <title>MyFlix Users</title>
    <link rel="stylesheet" type="text/css" href="users.css" />
  </head>

<body>
<h2>Meal Planning</h2>
<form action = "insert.php" method="POST">
Name: <input type="text" name="IngreditName" id="IngreditName" class="form-control">
Type: <input type="text" name="IngreditType" id="IngreditType" class="form-control">
<button type="submit" class="btn btn-primary">Submit</button>
</form>
<?php 
	
	// establish the database connection for your user
	//host: mscsdb.uwstout.edu:3306
	//user: meals    password:Spaghetti33?  schema: mealplanner
	$con = mysqli_connect("mscsdb.uwstout.edu:3306", "meals", "Spaghetti33?", "mealplanner");


	if(mysqli_connect_errno())
	{
		printf("Connect failed:  %s\n", mysqli_connect_error());
		exit();
	}
	
	// do a database query to get the information for each ingredient 
	$query = "SELECT * FROM ingredient;";
	
	$results = mysqli_query($con, $query);
	// makes sure result is not false/null; else prints error
	if (!$results) 
	{
		print("No results.");
		die("SQL error during query: " . mysqli_error());
		
	}
/**/	
	?>
	<!-- Create the table  -->
	
	<table border="1">
	
	<?php
			//Process the table of data 
	
	$counter = 0;		
	//Loop to process all rows of the table. Each $row is a row of table results.
	
	// get column metadata for table headers 
	$i = 0;
	while ($i < mysqli_num_fields($results)) 
	{
			 //print("in last php first while	");
			$meta = mysqli_fetch_field($results);
			if ($meta) 
			{
				if($i===0)
					echo "<tr>";
				
				echo "<th>$meta->name</th>";
				
				if($i=== mysqli_num_fields($results) -1)
					echo "</tr>";
			}//end if
			$i++;
	}//end while
		
	//loop through the table of SQL results and output in html table
	while($row = mysqli_fetch_array($results))
	{
		$cols = count($row)/2;
		//print "colums are $cols";
		print "  <tr>\n";
		for($i=0; $i<$cols; $i++)
		{
			$value = $row[$i];
			print "<td>$value</td>";
		}
		print "</tr>\n";
	}
	print "</table>";
/**/
	
	mysqli_close($con);
  
	
?>
		</tr>
	</table>
</body>
</html>
