<?php
session_start();
require 'include/dbmain/db.php';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['contact_submit'])) {

    if (empty($_SESSION['email']) || empty($_SESSION['id'])) {
        echo "<script>alert('You must be logged in to send a message.'); window.location.href='index.php';</script>";
        exit;
    }

    $userId = $_SESSION['id'];
    $email = $_SESSION['email'];
    $name = htmlspecialchars(trim($_POST['name']));
    $subject = htmlspecialchars(trim($_POST['subject']));
    $message = htmlspecialchars(trim($_POST['message']));

    $stmt = $conn->prepare("INSERT INTO contact (id, name, email, subject, message) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $userId, $name, $email, $subject, $message);

    if ($stmt->execute()) {
        echo "<script>alert('Your message has been sent successfully!'); window.location.href='index.php#contact';</script>";
    } else {
        echo "<script>alert('Failed to send message. Please try again later.'); window.location.href='index.php#contact';</script>";
    }

    $stmt->close();
}

// Auth Handling
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'register') {
        $name     = $_POST['name'];
        $email    = $_POST['email'];
        $password = $_POST['password'];
        $confirm  = $_POST['confirm_password'];

        if ($password !== $confirm) {
            echo "<script>alert('Passwords do not match!'); window.location.href='index.php';</script>";
            exit;
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo "<script>alert('Email already exists!'); window.location.href = 'index.php';</script>";
        } else {
            $stmt->close();
            $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $passwordHash);
            if ($stmt->execute()) {
                echo "<script>alert('Registration successful!'); window.location.href = 'index.php';</script>";
            } else {
                echo "<script>alert('Registration failed!'); window.location.href = 'index.php';</script>";
            }
        }

    } elseif ($action === 'login') {
        $email    = $_POST['email'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT id, name, password, type FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $stmt->bind_result($id, $name, $hashedPassword, $accountType);
            $stmt->fetch();

            if (password_verify($password, $hashedPassword)) {
                $_SESSION['id'] = $id;
                $_SESSION['name'] = $name;
                $_SESSION['email'] = $email;
                $_SESSION['account_type'] = $accountType;
                echo "<script>window.location.href='index.php';</script>";
            } else {
                echo "<script>alert('Invalid password'); window.location.href='index.php';</script>";
            }
        } else {
            echo "<script>alert('User not found'); window.location.href='index.php';</script>";
        }
    }
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Aakiran - Event Management</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="styles.css">
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar">
  <div class="container">
    <a href="#about" class="logo">Akiran<span style="color:#000000"> Events</span></a>
    <div class="nav-links">
      <a href="#home" class="nav-link">Home</a>
      <a href="#about" class="nav-link">About Us</a>
      <a href="#events" class="nav-link">Services</a>
      <a href="#contact" class="nav-link">Contact Us</a>
      <div class="auth-buttons">
        <?php if (isset($_SESSION['email'])): ?>
          <div class="account-dropdown">
            <button class="account-icon"><i class="fas fa-user-circle fa-2x"></i></button>
            <div class="dropdown-menu">
              <a href="include/orders.php">View Orders</a>
              <?php if ($_SESSION['account_type'] === 'admin'): ?>
                <a href="include/admin/admin.php">Admin Panel</a>
              <?php endif; ?>
              <a href="include/dbmain/logout.php">Logout</a>
            </div>
          </div>
        <?php else: ?>
          <button id="authBtn" class="btn btn-primary">Login</button>
        <?php endif; ?>
      </div>
    </div>
    <button class="hamburger">
      <span></span><span></span><span></span>
    </button>
  </div>
</nav>

<!-- Auth Modal -->
<div id="authModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <div class="auth-tabs">
      <button class="tab-btn active" data-tab="login">Login</button>
      <button class="tab-btn" data-tab="register">Register</button>
    </div>
    <div id="loginTab" class="auth-tab-content active">
      <h2>Login</h2>
      <form id="loginForm" method="POST">
        <input type="hidden" name="action" value="login">
        <div class="form-group">
          <label for="loginEmail">Email</label>
          <input type="email" id="loginEmail" name="email" required>
        </div>
        <div class="form-group">
          <label for="loginPassword">Password</label>
          <input type="password" id="loginPassword" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
      </form>
    </div>
    <div id="registerTab" class="auth-tab-content">
      <h2>Register</h2>
      <form id="registerForm" method="POST">
        <input type="hidden" name="action" value="register">
        <div class="form-group">
          <label for="registerName">Full Name</label>
          <input type="text" id="registerName" name="name" required>
        </div>
        <div class="form-group">
          <label for="registerEmail">Email</label>
          <input type="email" id="registerEmail" name="email" required>
        </div>
        <div class="form-group">
          <label for="registerPassword">Password</label>
          <input type="password" id="registerPassword" name="password" required>
        </div>
        <div class="form-group">
          <label for="registerConfirmPassword">Confirm Password</label>
          <input type="password" id="registerConfirmPassword" name="confirm_password" required>
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
      </form>
    </div>
  </div>
</div>

<!-- Home Section -->
 <div class="home">
    <section id="home" class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Lighting Up Your Special Moments</h1>
                <p>Professional event management services for weddings, corporate events, and more.</p>
                <a href="#events" class="btn btn-primary">Get Started</a>
            </div>
        </div>
    </section>
  </div>

    <!-- About Us Section -->
    <section id="about" class="about-section">
        <div class="container">
            <h2>About Us</h2>
            <div class="about-content">
                <div class="about-text">
                    <p>Akiran is a premier event management company dedicated to creating memorable experiences for our clients. With over 10 years of experience in the industry, we specialize in weddings, corporate events, and private parties.</p>
                    <p>Our team of professional event planners will work closely with you to bring your vision to life, handling every detail from venue selection to catering and entertainment.</p>
                </div>
                <div class="about-image">
                    <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Event Team">
                </div>
            </div>
        </div>
    </section>

    <section id="events">
        <?php include 'include/events.php';?>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials">
        <div class="container">
            <h2>HAPPY CLIENTS</h2>
            <div class="testimonial-slider">
                <div class="testimonial">
                    <div class="testimonial-content">
                        <p>"Akiran made our wedding day absolutely perfect! They handled everything seamlessly and we didn't have to worry about a thing."</p>
                    </div>
                    <div class="testimonial-author">
                        <img src="include/images/client.png" alt="Sarah J.">
                        <h4>Kshamya </h4>
                        <p>Wedding Client</p>
                    </div>
                </div>
                <div class="testimonial">
                    <div class="testimonial-content">
                        <p>"The corporate retreat organized by Akiran was flawless. Our team had an amazing time and everything was perfectly executed."</p>
                    </div>
                    <div class="testimonial-author">
                        <img src="include/images/client.png" alt="Michael T.">
                        <h4>Vedansh </h4>
                        <p>Corporate Client</p>
                    </div>
                </div>
                <div class="testimonial">
                    <div class="testimonial-content">
                        <p>"I can't recommend Akiran enough! They took care of every detail for my daughter's sweet sixteen and it was magical."</p>
                    </div>
                    <div class="testimonial-author">
                        <img src="include/images/client.png" alt="Lisa M.">
                        <h4>Akshay </h4>
                        <p>Birthday Party Client</p>
                    </div>
                </div>
            </div>
        </div>
    </section>


<!-- Contact Section -->
<section id="contact" class="contact-section">
  <div class="container">
    <h2>Contact Us</h2>
    <div class="contact-content">
      <div class="contact-info">
        <h3>Get in Touch</h3>
        <div class="cicon">
        <p><i class="fas fa-map-marker-alt"></i> Your Office Address</p>
        <p><i class="fas fa-phone"></i> +91 8086316245</p>
        <p><i class="fas fa-envelope"></i> info@Akiran.com</p>
        </div>
        <div class="social-links">
          <a href="#"><i class="fab fa-facebook-f"></i></a>
          <a href="#"><i class="fab fa-twitter"></i></a>
          <a href="#"><i class="fab fa-instagram"></i></a>
          <a href="#"><i class="fab fa-linkedin-in"></i></a>
        </div>
      </div>
      <div class="contact-form">
        <h3>Send Us a Message</h3>
        <?php if (isset($_SESSION['email'])): ?>
<form id="contactForm" method="POST">
    <div class="form-group">
        <input type="text" name="name" placeholder="Your Name" required>
    </div>
    <div class="form-group">
        <input type="email" name="email" value="<?php echo htmlspecialchars($_SESSION['email']); ?>" readonly required>
    </div>
    <div class="form-group">
        <input type="text" name="subject" placeholder="Subject" required>
    </div>
    <div class="form-group">
        <textarea name="message" placeholder="Your Message" required></textarea>
    </div>
    <button type="submit" name="contact_submit" class="btn btn-primary">Send Message</button>
</form>
<?php else: ?>
    <p style="color: red; text-align:center;">Please <strong>log in</strong> to send a message.</p>
<?php endif; ?>

      </div>
    </div>
  </div>
</section>

<!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-about">
                    <h3>Akiran</h3>
                    <p>Creating unforgettable events with passion and precision.</p>
                </div>
                <div class="footer-links">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="#home">Home</a></li>
                        <li><a href="#about">About Us</a></li>
                        <li><a href="#events">Services</a></li>
                        <li><a href="#contact">Contact Us</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2023 Akiran. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

<script src="script.js"></script>
</body>
</html>
