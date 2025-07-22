<?php
// Database connection
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'akshu';

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch events
$sql = "SELECT id, title, description, image1 FROM events";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Cards</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f0f0;
        }
        .card-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .card {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            width: 300px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
        }
        .card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .card-content {
            padding: 15px;
            flex: 1;
        }
        .card h2 {
            font-size: 20px;
            margin: 0 0 10px;
        }
        .card p {
            font-size: 14px;
            color: #555;
        }
        .card a {
            display: block;
            text-align: center;
            padding: 10px;
            background: #f8231cff;
            color: white;
            text-decoration: none;
            font-weight: bold;
            border-top: 1px solid #ddd;
        }
        .card a:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

<h1 style="text-align:center; margin-bottom: 30px;">Available Events</h1>

<div class="card-container">
    <?php while($row = $result->fetch_assoc()): ?>
        <div class="card">
            <img src="include/admin/upload/<?php echo htmlspecialchars($row['image1']); ?>" alt="Event Image">
            <div class="card-content">
                <h2><?php echo htmlspecialchars($row['title']); ?></h2>
                <p><?php echo htmlspecialchars($row['description']); ?></p>
            </div>
            <a href="include/book.php?id=<?php echo $row['id']; ?>">Book Now</a>
        </div>
    <?php endwhile; ?>
</div>

</body>
</html>
