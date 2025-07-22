<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Admin Panel</title>
    <style>
        :root {
            --primary-color: #4a6bff;
            --secondary-color: #f8f9fa;
            --accent-color: #ff4a6e;
            --text-dark: #333;
            --text-light: #f8f9fa;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --border-radius: 8px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            display: flex;
            min-height: 100vh;
            background-color: #f5f7fa;
        }

        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            color: var(--text-light);
            padding: 20px 0;
            position: fixed;
            height: 100vh;
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-header img {
            width: 40px;
            height: 40px;
            margin-right: 10px;
            border-radius: 50%;
        }

        .sidebar-title {
            font-size: 18px;
            font-weight: 600;
        }

        .nav-menu {
            list-style: none;
            padding: 20px 0;
        }

        .nav-item {
            margin: 5px 0;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            color: var(--text-light);
            text-decoration: none;
            transition: all 0.2s;
        }

        .nav-link:hover, .nav-link.active {
            background-color: var(--primary-color);
        }

        .nav-icon {
            margin-right: 10px;
            font-size: 18px;
        }

        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 30px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 24px;
            font-weight: 600;
            color: var(--text-dark);
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }

        .card {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 25px;
            margin-bottom: 30px;
        }

        .card-title {
            font-size: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-dark);
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-size: 16px;
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: var(--border-radius);
            font-size: 16px;
            cursor: pointer;
            font-weight: 500;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-secondary {
            background-color: #eee;
            color: var(--text-dark);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        table th {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header">
            <img src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/3cc02e4b-5f43-4bb0-ae58-8917ff69159a.png" />
            <div class="sidebar-title">Admin Panel</div>
        </div>
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="?page=add_event" class="nav-link <?= ($_GET['page'] ?? 'add_event') == 'add_event' ? 'active' : '' ?>">
                    <span class="nav-icon">📅</span><span>Events</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="?page=orders" class="nav-link <?= ($_GET['page'] ?? '') == 'orders' ? 'active' : '' ?>">
                    <span class="nav-icon">🧾</span><span>Orders</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="?page=contacts" class="nav-link <?= ($_GET['page'] ?? '') == 'contacts' ? 'active' : '' ?>">
                    <span class="nav-icon">📩</span><span>Contacted</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="../dbmain/logout.php" class="nav-link <?= ($_GET['page'] ?? '') == 'logout' ? 'active' : '' ?>">
                    <span class="nav-icon">🔒</span><span>Logout</span>
                </a>
            </li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="header">
            <h1 class="page-title">Admin Dashboard</h1>
            <div class="user-profile">
                <span>Admin User</span>
                <img src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/da4f9b1c-2624-4eac-8fe6-0684fccd0376.png" />
            </div>
        </header>

        <?php
        $conn = new mysqli('localhost', 'root', '', 'akshu');
        if ($conn->connect_error) {
            die('Connection failed: ' . $conn->connect_error);
        }

        $page = $_GET['page'] ?? 'add_event';

        if ($page === 'orders') {
            echo '<div class="card"><h2 class="card-title">Orders</h2>';
            $result = $conn->query("SELECT id, 	event_name, frm_date, to_date, 	placeofhall, seats, name, number FROM bookings");
            echo '<table><tr><th>ID</th><th>Event</th><th>Name</th><th>Number</th><th>Frm Date</th><th>To Date</th><th>Place</th><th>Seats</th></tr>';
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['event_name']}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['number']}</td>
                    <td>{$row['frm_date']}</td>
                    <td>{$row['to_date']}</td>
                    <td>{$row['placeofhall']}</td>
                    <td>{$row['seats']}</td>
                </tr>";
            }
            echo '</table></div>';
        } elseif ($page === 'contacts') {
            echo '<div class="card"><h2 class="card-title">Contacted Users</h2>';
            $result = $conn->query("SELECT id, name, email, subject, message, date FROM contact ");
            echo '<table><tr><th>ID</th><th>Name</th><th>Email</th><th>Subject</th><th>Message</th><th>Date</th></tr>';
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['subject']}</td>
                    <td>{$row['message']}</td>
                    <td>{$row['date']}</td>
                </tr>";
            }
            echo '</table></div>';
        } elseif ($page === 'logout'){
             echo "<script>
                alert('Logout successful!');
                window.location.href='../index.php';
            </script>";
        }
        else{
        ?>

        <!-- Add New Event Form -->
        <div class="card">
            <h2 class="card-title">Add New Event</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="form-label" for="title">Title</label>
                    <input class="form-control" type="text" name="title" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="description">Description</label>
                    <textarea class="form-control" name="description"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label" for="image">Image</label>
                </div>
                <input class="form-control" type="file" name="img1" accept="image/*" required>
                <input class="form-control" type="file" name="img2" accept="image/*" required>
                <input class="form-control" type="file" name="img3" accept="image/*" required>
                <button type="submit" class="btn btn-primary">Save Event</button>
            </form>
        </div>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $image1 = $_FILES["img1"]["name"];
    $image2 = $_FILES["img2"]["name"];
    $image3 = $_FILES["img3"]["name"];

    // Move images to upload folder
    $uploadSuccess = move_uploaded_file($_FILES["img1"]["tmp_name"], "upload/" . $image1) &&
                     move_uploaded_file($_FILES["img2"]["tmp_name"], "upload/" . $image2) &&
                     move_uploaded_file($_FILES["img3"]["tmp_name"], "upload/" . $image3);

    if ($uploadSuccess) {
        // Prepare the SQL using MySQLi
        $stmt = $conn->prepare("INSERT INTO events (title, description, image1, image2, image3) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $title, $description, $image1, $image2, $image3);

        if ($stmt->execute()) {
            echo "<script>alert('Event created successfully');</script>";
        } else {
            echo "<script>alert('DB Error: " . addslashes($stmt->error) . "');</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('One or more images failed to upload');</script>";
    }
}}        $conn->close();
        ?>
    </main>
</body>
</html>