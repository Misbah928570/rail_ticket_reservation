<?php 
session_start();
$conn = mysqli_connect("localhost", "root", "", "railway_db");

if (!$conn) {  
    echo "<script type='text/javascript'>alert('Database connection failed');</script>";
    die('Could not connect: ' . mysqli_connect_error());  
}

// Check PNR Status
if (isset($_POST['submit']) && isset($_POST['pnr']) && !empty($_POST['pnr'])) {
    $pnr = $_POST['pnr'];

    $stmt = $conn->prepare("SELECT t_status FROM tickets WHERE PNR = ?");
    $stmt->bind_param("s", $pnr);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row == NULL) {
        echo "<script type='text/javascript'>alert('PNR not found');</script>";
    } else {
        $status = $row['t_status'];
        echo "<script type='text/javascript'>alert('Your status is $status');</script>";
    }

    $stmt->close();
}

// Cancel Ticket
if (isset($_POST['cancel']) && isset($_POST['pnr']) && !empty($_POST['pnr'])) {
    $pnr = $_POST['pnr'];

    $stmt = $conn->prepare("DELETE FROM tickets WHERE PNR = ?");
    $stmt->bind_param("s", $pnr);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "<script type='text/javascript'>alert('Your ticket has been cancelled');</script>";
        } else {
            echo "<script type='text/javascript'>alert('PNR not found. Cancellation failed');</script>";
        }
    } else {
        echo "<script type='text/javascript'>alert('Cancellation failed');</script>";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>PNR Status</title>
    <link rel="stylesheet" href="style.css">
    <style>
        #pnr {
            font-size: 20px;
            background-color: white;
            width: 500px;
            height: auto;
            margin: auto;
            border-radius: 25px;
            border: 2px solid blue; 
            padding: 40px 20px;
            margin-top: 130px;
            position: relative;
        }

        html { 
            background: url(img/bg7.jpg) no-repeat center center fixed; 
            background-size: cover;
        }

        .button {
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            background-color: blue;
            color: white;
            border-radius: 8px;
            cursor: pointer;
        }

        input[type="text"] {
            padding: 10px;
            font-size: 16px;
            width: 80%;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .form-section {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<?php include("header.php"); ?>

<center>
    <div id="pnr">
        <div class="form-section">
            <form method="post" action="pnrresult.php">
                <h3>Check your PNR status here:</h3>
                <input type="text" name="pnr" maxlength="10" placeholder="Enter PNR here" required><br><br>
                <input type="submit" name="submit" value="Check here!" class="button">
            </form>
        </div>

        <?php if (isset($_SESSION['user_info'])) { ?>
            <div class="form-section">
                <form method="post" action="pnrstatus.php">
                    <h3>Cancel your ticket:</h3>
                    <input type="text" name="pnr" maxlength="10" placeholder="Enter PNR to cancel" required><br><br>
                    <input type="submit" name="cancel" value="Cancel your ticket!" class="button">
                </form>
            </div>
        <?php } else { ?>
            <a href="register.php">Login/Register</a>
        <?php } ?>
    </div>
</center>
</body>
</html>
