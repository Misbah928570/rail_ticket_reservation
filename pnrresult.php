<?php
$conn = mysqli_connect("localhost", "root", "", "railway_db");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

if (isset($_POST['pnr']) && !empty($_POST['pnr'])) {
    $pnr = $_POST['pnr'];

    // Join tickets with passengers using p_id
    $stmt = $conn->prepare("
        SELECT t.PNR, t.t_status, t.t_fare,
               p.p_fname, p.p_lname, p.p_age, p.p_gender, p.p_contact, p.email, p.t_no
        FROM tickets t
        JOIN passengers p ON t.p_id = p.p_id
        WHERE t.PNR = ?
    ");
    $stmt->bind_param("s", $pnr);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $full_name = $row['p_fname'] . ' ' . $row['p_lname'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>PNR Status Result</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url(img/bg7.jpg) no-repeat center center fixed;
            background-size: cover;
        }
        .container {
            background-color: white;
            width: 600px;
            margin: 100px auto;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 0 15px rgba(0,0,0,0.3);
        }
        h2 {
            text-align: center;
            color: blue;
        }
        .info {
            margin: 10px 0;
            font-size: 18px;
        }
        .button {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: blue;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            width: 200px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>PNR Status Details</h2>
        <div class="info"><strong>PNR Number:</strong> <?= htmlspecialchars($row['PNR']) ?></div>
        <div class="info"><strong>Passenger Name:</strong> <?= htmlspecialchars($full_name) ?></div>
        <div class="info"><strong>Age:</strong> <?= htmlspecialchars($row['p_age']) ?></div>
        <div class="info"><strong>Gender:</strong> <?= htmlspecialchars($row['p_gender']) ?></div>
        <div class="info"><strong>Contact:</strong> <?= htmlspecialchars($row['p_contact']) ?></div>
        <div class="info"><strong>Email:</strong> <?= htmlspecialchars($row['email']) ?></div>
        <div class="info"><strong>Train Number:</strong> <?= htmlspecialchars($row['t_no']) ?></div>
        <div class="info"><strong>Ticket Status:</strong> <?= htmlspecialchars($row['t_status']) ?></div>
        <div class="info"><strong>Fare:</strong> ₹<?= htmlspecialchars($row['t_fare']) ?></div>

        <a href="pnrstatus.php" class="button">Go Back</a>
    </div>
</body>
</html>
<?php
    } else {
        echo "<script>alert('PNR not found'); window.location.href='pnrstatus.php';</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('PNR not provided'); window.location.href='pnrstatus.php';</script>";
}
?>
