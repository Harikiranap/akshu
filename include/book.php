<?php
session_start();

if (!isset($_SESSION['email'])) {
    echo '<script>
    alert("You must be logged in to book event.");
    window.location.href = "../index.php";
</script>';
    exit;
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $event = $conn->real_escape_string($_POST['event'] ?? '');
    $from_date = $conn->real_escape_string($_POST['from_date'] ?? '');
    $to_date = $conn->real_escape_string($_POST['to_date'] ?? '');
    $hall = $conn->real_escape_string($_POST['hall'] ?? '');
    $seats = (int)($_POST['seats'] ?? 0);
    $name = $conn->real_escape_string($_POST['cust'] ?? '');
    $number = $conn->real_escape_string($_POST['number'] ?? '');
    
    if (empty($event) || empty($from_date) || empty($to_date) || empty($hall) || empty($name) || empty($number)) {
        echo "<script>alert('Please fill all required fields.');</script>";
    } else {
        $stmt = $conn->prepare("INSERT INTO bookings (event_name, frm_date, to_date, placeofhall, seats, name, number, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

if ($stmt) {
    // Bind the parameters: s = string, i = integer
    $stmt->bind_param("ssssisss", $event, $from_date, $to_date, $hall, $seats, $name, $number, $email);

    if ($stmt->execute()) {
        echo "<script>
            if (confirm('Booking successful!')) {
                window.location.href = 'submit.html';
            }
        </script>";
    } else {
        echo "<script>alert('Execute Error: " . addslashes($stmt->error) . "');</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('Prepare Failed: " . addslashes($conn->error) . "');</script>";
}

        $stmt->close();
    }
}

// Get event details
$event_id = (int)($_GET['id'] ?? 0);
$images = ['image1' => '', 'image2' => '', 'image3' => ''];
$event_name = '';

if ($event_id > 0) {
    $stmt = $conn->prepare("SELECT image1, image2, image3, title FROM events WHERE id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();


$images = [
    'image1' => !empty($row['image1']) ? 'admin/upload/' . $row['image1'] : '',
    'image2' => !empty($row['image2']) ? 'admin/upload/' . $row['image2'] : '',
    'image3' => !empty($row['image3']) ? 'admin/upload/' . $row['image3'] : '',
];

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventEase - Book Event</title>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            background: #f4f4f4;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            background: #ffffff;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            padding: 30px;
            border-radius: 10px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .header h1 {
            font-size: 2.2rem;
            color: #222;
            flex: 1 1 65%;
        }

        .price {
            text-align: right;
            color: #000000ff;
            font-weight: bold;
            font-size: 1.8rem;
            flex: 1 1 30%;
        }

        .price small {
            display: block;
            font-size: 0.9rem;
            color: #666;
        }

        .main-content {
            display: flex;
            flex-wrap: wrap;
            gap: 40px;
        }

        .overview {
            flex: 1 1 60%;
        }

        .overview .tab {
            background: #060606ff;
            color: #fff;
            padding: 10px 20px;
            font-weight: bold;
            border-radius: 5px 5px 0 0;
            margin-bottom: 10px;
            width: fit-content;
        }

        .overview p {
            background: #f8f8f8;
            padding: 20px;
            border-radius: 0 5px 5px 5px;
            color: #333;
            line-height: 1.7;
            font-size: 1rem;
        }

        .booking-form {
            flex: 1 1 35%;
            background: #f8f8f8;
            padding: 20px;
            border-radius: 10px;
        }

        .booking-form h2 {
            color: #020202ff;
            margin-bottom: 20px;
            font-size: 1.3rem;
        }

        .booking-form label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
            font-size: 0.95rem;
            color: #444;
        }

        .booking-form input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 0.95rem;
        }

        .booking-form button {
            margin-top: 20px;
            padding: 12px;
            width: 100%;
            background-color: #000000ff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .booking-form button:hover {
            background-color: #ffffffff;
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
            }

            .main-content {
                flex-direction: column;
            }

            .header h1,
            .price {
                flex: 1 1 100%;
            }
        }

        a {
            text-decoration: none;
            color: black;
            font-weight: bold;
        }

        .return {
            background: #040404ff;
            color: #fff;
            padding: 10px 20px;
            font-weight: bold;
            border-radius: 5px 10px 0 0;
            margin-bottom: 10px;
            width: fit-content;
        }

        .slider-container {
            position: relative;
            width: 100%;
            height: 350px;
            overflow: hidden;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .slider {
            display: flex;
            height: 100%;
            transition: transform 0.5s ease-in-out;
        }
        .slide {
            width: 60%; 
            flex-shrink: 0;
            border-radius: 10px;
            overflow: hidden;
        }

        .slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .slider-nav {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
        }

        .slider-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            transition: background 0.3s;
        }

        .slider-dot.active {
            background: white;
        }

        /* Loader Styles */
        #loader {
            position: fixed;
            width: 100vw;
            height: 100vh;
            background: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            left: 0;
            top: 0;
        }

        .dots-container {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            width: 100%;
        }

        .dot {
            height: 20px;
            width: 20px;
            margin-right: 10px;
            border-radius: 10px;
            background-color: #b3d4fc;
            animation: pulse 1.5s infinite ease-in-out;
        }

        .dot:last-child {
            margin-right: 0;
        }

        .dot:nth-child(1) {
            animation-delay: -0.3s;
        }

        .dot:nth-child(2) {
            animation-delay: -0.1s;
        }

        .dot:nth-child(3) {
            animation-delay: 0.1s;
        }

        @keyframes pulse {
            0% {
                transform: scale(0.8);
                background-color: #b3d4fc;
                box-shadow: 0 0 0 0 rgba(178, 212, 252, 0.7);
            }

            50% {
                transform: scale(1.2);
                background-color: #6793fb;
                box-shadow: 0 0 0 10px rgba(178, 212, 252, 0);
            }

            100% {
                transform: scale(0.8);
                background-color: #b3d4fc;
                box-shadow: 0 0 0 0 rgba(178, 212, 252, 0.7);
            }
        }
    </style>
</head>
<body>
    <div id="loader">
        <section class="dots-container">
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        </section>
    </div>

    <div id="main-content" class="container" style="display:none;">
        <a href="../index.php" class="return"> ← Return to Back</a>
        
        <!-- Image Slider -->
        <div class="slider-container">
            <div class="slider">
    <div class="slide">
        <img src="<?= htmlspecialchars($images['image1']) ?>" alt="Event Image 1">
    </div>
    <div class="slide">
        <img src="<?= htmlspecialchars($images['image2']) ?>" alt="Event Image 2">
    </div>
    <div class="slide">
       <img src="<?= htmlspecialchars($images['image3']) ?>" alt="Event Image 3">
    </div>
    </div>
            <!-- Navigation dots -->
            <div class="slider-nav"></div>
        </div>

        <div class="main-content">
            <div class="overview">
                <div class="tab">Event Management By EventEase</div>
                <p>EventEase is a professional event management platform designed to make planning and hosting events simple, seamless, and successful. Whether you're organizing a corporate conference, wedding, birthday party, or public festival, EventEase offers end-to-end solutions to bring your vision to life.<br>
                ✅ Event Planning & Coordination<br>
                ✅ Vendor Booking & Venue Selection<br>
                ✅ Guest Management & Invitations<br>
                ✅ Real-time Tracking & Communication Tools<br>
                ✅ Media Galleries & Past Event Highlights<br>
                Our user-friendly interface allows clients to browse upcoming events, book event services, and track their bookings all in one place. From elegant weddings to high-profile corporate events, EventEase ensures every detail is executed to perfection.
                Let us take the stress out of planning.<br>
                🎉 <b>With EventEase, your event is in expert hands.</b></p>
            </div>

            <div class="booking-form">
                <h2><i class="fas fa-envelope"></i> Book Now</h2>
                <form method="POST">
                    <label for="event">Event Name</label>
                    <input type="text" id="event" name="event" value="<?= htmlspecialchars($event_name) ?>" placeholder="Enter event name" required>

                    <label for="from-date">From Date</label>
                    <input type="date" id="from-date" name="from_date" required>

                    <label for="to-date">To Date</label>
                    <input type="date" id="to-date" name="to_date" required>

                    <label for="hall">Place of Hall</label>
                    <input type="text" id="hall" name="hall" placeholder="Enter hall name" required>

                    <label for="seats">No. of Seats</label>
                    <input type="number" id="seats" name="seats" placeholder="Enter number of seats" required min="1">

                    <label for="cust">Customer Name</label>
                    <input type="text" id="cust" name="cust" placeholder="Enter your name" required>

                    <label for="number">Contact Number</label>
                    <input type="tel" id="number" name="number" placeholder="Enter contact number" required pattern="[0-9]{10}">

                    <button type="submit">Submit Booking</button>
                </form>
            </div>
        </div>
    </div>

    
    <script>
        // Loader transition
        window.addEventListener('load', () => {
            setTimeout(() => {
                document.getElementById('loader').style.display = 'none';
                document.getElementById('main-content').style.display = 'block';
            }, 1000);
        });

        // Image Slider Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const slider = document.querySelector('.slider');
            const slides = document.querySelectorAll('.slide');
            const dotsContainer = document.querySelector('.slider-nav');
            let currentIndex = 0;
            const slideCount = slides.length;
            
            // Only initialize slider if there are slides
            if (slideCount > 0) {
                // Create navigation dots
                slides.forEach((_, index) => {
                    const dot = document.createElement('div');
                    dot.classList.add('slider-dot');
                    if (index === 0) dot.classList.add('active');
                    dot.addEventListener('click', () => {
                        goToSlide(index);
                    });
                    dotsContainer.appendChild(dot);
                });
                
                const dots = document.querySelectorAll('.slider-nav .slider-dot');
                
                // Auto-slide every 3 seconds
                let slideInterval = setInterval(nextSlide, 3000);
                
                function nextSlide() {
                    currentIndex = (currentIndex + 1) % slideCount;
                    updateSlider();
                }
                
                function goToSlide(index) {
                    currentIndex = index;
                    updateSlider();
                    resetInterval();
                }
                
                function updateSlider() {
                    slider.style.transform = `translateX(-${currentIndex * 33.333}%)`;
                    
                    // Update active dot
                    dots.forEach((dot, index) => {
                        dot.classList.toggle('active', index === currentIndex);
                    });
                }
                
                function resetInterval() {
                    clearInterval(slideInterval);
                    slideInterval = setInterval(nextSlide, 3000);
                }
                
                // Pause on hover
                slider.addEventListener('mouseenter', () => {
                    clearInterval(slideInterval);
                });
                
                slider.addEventListener('mouseleave', () => {
                    resetInterval();
                });
            }
        });
    </script>
</body>
</html>