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
        <form action='https://www.w3schools.com/action_page.php' style="padding: 16px;">
            <p><b>Ingredient Name:</b></p><br>
            <input type='text' name='name' onmouseover='this.focus()'><br><br>
            <p><b>Ingredient Type:</b></p>
            <p style="font-size: 9px;"><i>(click the text box to display pre-existing types, or add a new one if it does not exist)</i></p><br>
            <input type='text' name='foodtype' list='foodtype' onmouseover='this.focus()'><br><br>
            <datalist id='foodtype'>
                <?php
                $conn = new mysqli("DATABASE", "USERNAME", "PASSWORD", "SCHEMA");
                $result = $conn->query("SELECT DISTINCT foodtype FROM ingredient");
                while ($row = $result->fetch_assoc()) {
                    $foodtypes .= "<option label='" . $row["foodtype"] . "' value='" . $row["foodtype"] . "'>";
                }
				echo $foodtypes;

				// close the connection for safety
				$conn->close();
                ?>
            </datalist>
            <input style="width: 100%;" type="submit" value="Add Ingredient!">
        </form>
    </div>
</body>

</html>
