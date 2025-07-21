document.addEventListener('DOMContentLoaded', function() {
    // Mobile Navigation
    const hamburger = document.querySelector('.hamburger');
    const navLinks = document.querySelector('.nav-links');

    hamburger.addEventListener('click', function() {
        this.classList.toggle('active');
        navLinks.classList.toggle('active');
    });

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Close mobile menu if open
            hamburger.classList.remove('active');
            navLinks.classList.remove('active');
            
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 80,
                    behavior: 'smooth'
                });
            }
        });
    });

    // Combined auth modal functionality
const authBtn = document.getElementById('authBtn');
const authModal = document.getElementById('authModal');
const closeBtn = document.querySelector('.close');
const tabBtns = document.querySelectorAll('.tab-btn');
const tabContents = document.querySelectorAll('.auth-tab-content');

// Show auth modal
authBtn.addEventListener('click', function() {
    authModal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    // Show login tab by default
    switchTab('login');
});

// Close modal
closeBtn.addEventListener('click', function() {
    authModal.style.display = 'none';
    document.body.style.overflow = 'auto';
});

// Close modal when clicking outside
window.addEventListener('click', function(e) {
    if (e.target === authModal) {
        authModal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
});

// Tab switching functionality
tabBtns.forEach(btn => {
    btn.addEventListener('click', function() {
        const tabId = this.getAttribute('data-tab');
        switchTab(tabId);
    });
});

function switchTab(tabId) {
    // Update active tab button
    tabBtns.forEach(btn => {
        btn.classList.remove('active');
        if (btn.getAttribute('data-tab') === tabId) {
            btn.classList.add('active');
        }
    });
    
    // Update active tab content
    tabContents.forEach(content => {
        content.classList.remove('active');
        if (content.id === `${tabId}Tab`) {
            content.classList.add('active');
        }
    });
}

// Add switch link to forms (optional)
const loginForm = document.getElementById('loginForm');
const registerForm = document.getElementById('registerForm');

if (loginForm) {
    const switchToRegister = document.createElement('p');
    switchToRegister.className = 'switch-text';
    switchToRegister.innerHTML = 'Don\'t have an account? <a id="switchToRegister">Register here</a>';
    loginForm.appendChild(switchToRegister);
    
    document.getElementById('switchToRegister').addEventListener('click', function() {
        switchTab('register');
    });
}

if (registerForm) {
    const switchToLogin = document.createElement('p');
    switchToLogin.className = 'switch-text';
    switchToLogin.innerHTML = 'Already have an account? <a id="switchToLogin">Login here</a>';
    registerForm.appendChild(switchToLogin);
    
    document.getElementById('switchToLogin').addEventListener('click', function() {
        switchTab('login');
    });
}

// Form submissions remain the same as before

    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            // Add your contact form submission logic here
            alert('Thank you for your message! We will get back to you soon.');
            this.reset();
        });
    }

    // Sticky navbar on scroll
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.navbar');
        if (window.scrollY > 50) {
            navbar.style.padding = '15px 0';
            navbar.style.boxShadow = '0 5px 15px rgba(0, 0, 0, 0.1)';
        } else {
            navbar.style.padding = '20px 0';
            navbar.style.boxShadow = '0 5px 15px rgba(0, 0, 0, 0.1)';
        }
    });

    // Testimonial slider functionality
    const testimonialSlider = document.querySelector('.testimonial-slider');
    if (testimonialSlider) {
        let isDown = false;
        let startX;
        let scrollLeft;

        testimonialSlider.addEventListener('mousedown', (e) => {
            isDown = true;
            testimonialSlider.classList.add('active');
            startX = e.pageX - testimonialSlider.offsetLeft;
            scrollLeft = testimonialSlider.scrollLeft;
        });

        testimonialSlider.addEventListener('mouseleave', () => {
            isDown = false;
            testimonialSlider.classList.remove('active');
        });

        testimonialSlider.addEventListener('mouseup', () => {
            isDown = false;
            testimonialSlider.classList.remove('active');
        });

        testimonialSlider.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - testimonialSlider.offsetLeft;
            const walk = (x - startX) * 2;
            testimonialSlider.scrollLeft = scrollLeft - walk;
        });
    }
});

document.addEventListener('DOMContentLoaded', function () {
            const loadingScreen = document.getElementById('loadingScreen');

            // Hide loading screen after 2 seconds (simulating processing time)
            setTimeout(function () {
                loadingScreen.style.opacity = '0';

                // Remove loading screen from DOM after fade out
                setTimeout(function () {
                    loadingScreen.style.display = 'none';
                    createConfetti();
                }, 500);
            }, 2000);
        });
// Create confetti elements
        function createConfetti() {
            const colors = ['#4facfe', '#00f2fe', '#a6c1ee', '#fbc2eb', '#ff9a9e'];
            const container = document.querySelector('container ');

            for (let i = 0; i < 50; i++) {
                const confetti = document.createElement('div');
                confetti.className = 'confetti';
                confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.left = Math.random() * 100 + 'vw';
                confetti.style.width = Math.random() * 10 + 5 + 'px';
                confetti.style.height = Math.random() * 10 + 5 + 'px';
                confetti.style.animation = `confettiFall ${Math.random() * 3 + 2}s linear forwards`;
                confetti.style.animationDelay = Math.random() * 2 + 's';
                container.appendChild(confetti);
            }
        }