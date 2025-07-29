<?php
session_start();

if (!isset($_SESSION['email'])) {
    echo "<script>alert('You must be logged in to view orders.'); window.location.href = 'index.php';</script>";
    exit();
}

$email = $_SESSION['email'];

$host = 'localhost';
$user = 'root';
$password = '';
$database = 'akshu';

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, name, number, email, event_name, frm_date, to_date, placeofhall, seats, bookedon FROM bookings WHERE email = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Bookings</title>
    <link id='favicon' rel="shortcut icon" href="images/AE.png" type="image/x-png">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        h2 {
            text-align: center;
            padding: 20px;
            background-color: #fe0101ff;
            color: white;
        }

        .container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .card {
            background: white;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.95);
            display: flex;
            overflow: hidden;
            transition: transform 0.2s;
        }

        .card:hover {
            transform: scale(1.01);
        }

        .card img {
            width: 250px;
            height: auto;
            object-fit: cover;
        }

        .card-content {
            padding: 20px;
            flex: 1;
        }

        .card-content div {
            margin-bottom: 8px;
        }

        .card-content strong {
            display: inline-block;
            width: 130px;
            color: black;
        }

        .card-content p {
            color:red;
            font-weight:bolder;
            font-size:20px;
        }

        .no-orders {
            text-align: center;
            color: #777;
            margin-top: 50px;
            font-style: italic;
        }
        .back-btn {
    text-align: center;
    margin: 20px 0;
}

.back-btn a {
    display: inline-block;
    padding: 10px 20px;
    background-color: #4CAF50;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    transition: background-color 0.2s ease;
}

.back-btn a:hover {
    background-color: #45a049;
}

    </style>
</head>
<body>

<h2>Your Bookings</h2>
<div class="back-btn">
    <a href="../index.php">← Return to Home</a>
</div>
<div class="container">
    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="card">
                <img src="images/AE.png" alt="Event Image">
                <div class="card-content">
                    <div><strong>Event:</strong> <?= htmlspecialchars($row['event_name']) ?></div>
                    <div><strong>Name:</strong> <?= htmlspecialchars($row['name']) ?></div>
                    <div><strong>Mobile:</strong> <?= htmlspecialchars($row['number']) ?></div>
                    <div><strong>Email:</strong> <?= htmlspecialchars($row['email']) ?></div>
                    <div><strong>From:</strong> <?= htmlspecialchars($row['frm_date']) ?></div>
                    <div><strong>To:</strong> <?= htmlspecialchars($row['to_date']) ?></div>
                    <div><strong>Venue:</strong> <?= htmlspecialchars($row['placeofhall']) ?></div>
                    <div><strong>Seats:</strong> <?= htmlspecialchars($row['seats']) ?></div>
                    <div><strong>Booked On:</strong> <?= htmlspecialchars($row['bookedon']) ?></div>
                    <p>" Our team will reach out to you shortly "</p>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="no-orders">No bookings found for your account.</p>
    <?php endif; ?>
</div>

<?php
$stmt->close();
$conn->close();
?>

</body>
</html>
