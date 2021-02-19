<!DOCTYPE HTML>
<html>

<head>

    <!-- CSS STYLE SHEET -->

    <style>
        body {
            background-color: powderblue;
        }

        h2 {
            color: blue;
        }

        p {
            color: red;
        }

        th {
            color: red;
            background-color: black;
            padding: 10px;
        }

        div {
            margin: 0 auto;
            text-align: center;
        }

        table,
        td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 4px;
        }

        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>
</head>

<!-- HTML -->

<body>
    <h2>Student Records</h2>
    <p>This database displays the student records for students that attend Ryerson.
        <br>Everytime you insert a values and save record, the updated record will display.
    </p>

    <form method="post" action="/lab2part2.php">

        <label for="snumber">
            <h4>Student Number</h4>
        </label>
        <input type="number" id="snumber" name="snumber" size="25" maxlength="9" placeholder="Enter your student number here">

        <label for="fname">
            <h4>First Name</h4>
        </label>
        <input type="text" id="fname" name="fname" size="30" maxlength="255" placeholder="Enter your first name"><br>



        <label for="lname">
            <h4>Last Name</h4>
        </label>
        <input type="text" id="lname" name="lname" size="30" maxlength="255" placeholder="Enter your first name"><br>

        <br>
        <br>

        <label for="year">
            <h4>Date of Birth</h4>
        </label>
        <input type="date" id="year" name="year" size="30" placeholder="enter year the artwork was created"><br>

        <br>

        <label for="phone">
            <h4>Phone Number</h4>
        </label>
        <input type="tel" id="phone" name="phone" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}">
        <small>Format: 647-000-0000</small><br><br>

        <br>
        <input type="submit" name="save" value="SAVE RECORD">
        <input type="submit" name="clear" value="CLEAR RECORD">
    </form>

    <!-- JAVASCRIPT/JQUERY -->

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script>
        $("#painting-detail").hide();
        $(document).ready(function() {
            $("#type").change(function() {
                $("#painting-detail option").remove();
                switch ($("#type").val()) {
                    case "Painting":
                        $("#painting-detail").show();
                        $('#painting-detail').append($('<option>', {
                            value: 'Landscape',
                            text: 'Landscape'
                        }));
                        $('#painting-detail').append($('<option>', {
                            value: 'Portrait',
                            text: 'Portrait'
                        }));
                        break;
                    default:
                        $("#painting-detail").hide();
                        $('#painting-detail').append($('<option>', {
                            value: 'None',
                            text: 'None'
                        }));

                }
            });
        });
    </script>

    <!-- PHP -->

    <?php
    /*<-- TODO -->
    - Add limit on characters for fields @ table
    
*/

    $servername = "localhost";
    $username = "root";
    $password = "";

    $mysqli = new mysqli("localhost", "root", "");

    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli->connect_error;
        exit();
    } else {
        echo "Database connected!";
    }

    $result = $mysqli->query("CREATE DATABASE IF NOT EXISTS testnew");

    $mysqli->close();

    // Connect to database;

    $mysqli = new mysqli("localhost", "root", "", "testnew");

    //create table if it does not exist
    $result = $mysqli->query("CREATE TABLE IF NOT EXISTS StRec (
    STUDENT_ID INT(9) NOT NULL,
    FIRST_NAME varchar(255) NOT NULL,
    LAST_NAME varchar(255) NOT NULL,
    BDAY DATE NOT NULL,
    PHONE VARCHAR(255) NOT NULL,
    PRIMARY KEY (STUDENT_ID)
    )");

    $mysqli->close();
    

    load_table();

    if (array_key_exists('save', $_POST)) {
        save();
    } else if (array_key_exists('clear', $_POST)) {
        clear();
    } else if (array_key_exists('delete', $_POST)) {
        delete();
    }

    function delete() {
        //delete
        $mysqli = new mysqli("localhost", "root", "", "testnew");
        if (isset($_POST["STUDENT_ID"]))
        {
          $user = $_POST["STUDENT_ID"];
          $result = $mysqli->query("DELETE FROM StRec WHERE STUDENT_ID=$user");
        } 
        else 
        {
          $user = null;
          echo "no username supplied";
        }
        $mysqli->close();
        echo("<meta http-equiv='refresh' content='1'>"); 
       
    }


    function load_table() {
        $mysqli = new mysqli("localhost", "root", "", "testnew");
        echo "<div>";
        echo "<table>";
        echo "<tr>";
        echo "<th>STUDENT NUMBER</th>";
        echo "<th>FIRST NAME</th>";
        echo "<th>LAST NAME</th>";
        echo "<th>YEAR</th>";
        echo "<th>PHONE</th>";
        echo "<tr>";

        $result = $mysqli->query("SELECT STUDENT_ID, FIRST_NAME, LAST_NAME, BDAY, PHONE FROM StRec");
        
        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<form method='post' action='/lab2part2.php'>";
                echo "<td>", $row['STUDENT_ID'], "</td>";
                echo "<td>", $row['FIRST_NAME'], "</td>";  
                echo "<td>", $row['LAST_NAME'], "</td>";
                echo "<td>", $row['BDAY'], "</td>";
                echo "<td>", $row['PHONE'], "</td>";
                echo "<input type='hidden' name='STUDENT_ID' value=${row['STUDENT_ID']}>";
                echo "<td>", "<input type='submit' name='delete' value='DELETE RECORD'>","</td>";
                echo "</form>";
                echo "</tr>";
            }
        } else {
            echo "0 results";
        }

        echo "</table>";
        echo "</div>";
        $mysqli->close();
    }
    function save()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $record = [$_POST["snumber"], $_POST["fname"], $_POST["lname"], $_POST["year"], $_POST["phone"]];
        }

        //Serialize the array.
        $mysqli = new mysqli("localhost", "root", "", "testnew");
        $date=date("Y-m-d",strtotime($record[3]));
        $result = $mysqli->query("INSERT INTO StRec (STUDENT_ID, FIRST_NAME, LAST_NAME, BDAY, PHONE)
        VALUES ('$record[0]', '$record[1]', '$record[2]', '$date', '$record[4]')");
        $mysqli->close();
        echo("<meta http-equiv='refresh' content='1'>"); 

    }
    function clear()
    {
        
    $mysqli = new mysqli("localhost", "root", "", "testnew");

    //create table if it does not exist
    $mysqli->query("TRUNCATE TABLE StRec");
    $mysqli->close();
    echo("<meta http-equiv='refresh' content='1'>"); 
    }

    ?>


</body>

</html>