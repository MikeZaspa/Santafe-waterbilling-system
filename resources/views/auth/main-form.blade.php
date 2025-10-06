<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Santa Fe Water Billing System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), 
                              url('{{ asset('image/background.jfif') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            color: white;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 50px;
            background-color: rgba(255, 255, 255, 0.98);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-img {
            height: 50px;
            width: 50px;
            border-radius: 50%;
            object-fit: cover;
        }

        .logo-text {
            color: #0077b6;
            font-weight: 700;
        }

        .nav-links {
            display: flex;
            list-style: none;
        }

        .nav-links li {
            margin-left: 30px;
            position: relative;
        }

        .nav-links a {
            color: #333;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .nav-links a:hover {
            color: #e64a19;
        }

        .nav-links a:hover i {
            transform: translateY(-2px);
        }

        .nav-links i {
            transition: all 0.3s ease;
            font-size: 0.9em;
        }

        .auth-buttons .btn {
            padding: 8px 20px;
            border-radius: 5px;
            margin-left: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
        }

        .login-btn {
            background-color: transparent;
            color: #333;
            border: 2px solid #0077b6;
        }

        .login-btn:hover {
            background-color: #0077b6;
            color: white;
        }

        .signup-btn {
            background-color:rgb(22, 209, 37);
            color: white;
        }

        .signup-btn:hover {
            background-color: #bf360c;
            transform: translateY(-2px);
        }

        .menu-toggle {
            display: none;
            flex-direction: column;
            cursor: pointer;
        }

        .menu-toggle span {
            height: 3px;
            width: 25px;
            background-color: #333;
            margin: 4px;
            border-radius: 3px;
            transition: all 0.3s ease;
        }

         .hero {
    background-image: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0)), 
                    url('{{ asset('image/background.jfif') }}');
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: flex-start;
    text-align: left;
    padding: 100px 50px;
    color: white;
    height: 70vh;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    font-family: 'Times New Roman', serif; /* Added to match formal college style */
}

.hero h1 {
    font-size: 2rem; /* Increased from 1.5rem for better visibility */
    margin-bottom: 15px;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    font-weight: bold;
    text-transform: uppercase; /* Matches the all-caps style in the image */
    letter-spacing: 1px; /* Improves readability for uppercase text */
}

.hero p {
    font-size: 1.1rem;
    margin-bottom: 25px;
    line-height: 1.6;
    max-width: 600px;
}

.hero ul {
    list-style-type: none;
    padding-left: 0;
}

.hero ul li {
    margin-bottom: 10px;
    position: relative;
    padding-left: 25px;
    font-size: 1rem;
}

.hero ul li:before {
    content: "•";
    position: absolute;
    left: 0;
    color: white;
    font-size: 1.2rem;
}

        .cta-buttons {
            display: flex;
            gap: 20px;
        }

        .cta-btn {
            padding: 12px 30px;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .primary-btn {
            background-color: #e64a19;
            color: white;
        }

        .primary-btn:hover {
            background-color: #bf360c;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .secondary-btn {
            background-color: transparent;
            color: white;
            border: 2px solid white;
        }

        .secondary-btn:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        /* Features Section */
        .features {
            padding: 80px 20px;
            background-color: rgba(255, 255, 255, 0.9);
            color: #333;
        }

        .section-title {
            text-align: center;
            margin-bottom: 50px;
            font-size: 2.5rem;
            color: #0077b6;
        }

        /* Mobile Menu Login Button */
        .mobile-login-btn {
            display: none;
            padding: 12px 20px;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            background-color: transparent;
            color: #333;
            border: 2px solid #0077b6;
            width: 90%;
            margin: 15px auto;
            text-align: center;
            justify-content: center;
            gap: 8px;
        }

        .mobile-login-btn:hover {
            background-color: #0077b6;
            color: white;
        }

        /* Footer */
        footer {
            background-color: #333;
            color: white;
            padding: 40px 20px;
            text-align: center;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            text-align: left;
        }

        .footer-column h3 {
            margin-bottom: 20px;
            font-size: 1.3rem;
            color: #e64a19;
        }

        .footer-column ul {
            list-style: none;
        }

        .footer-column ul li {
            margin-bottom: 10px;
        }

        .footer-column ul li a {
            color: #ccc;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .footer-column ul li a:hover {
            color: white;
            padding-left: 5px;
        }

        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .social-links a {
            color: white;
            font-size: 1.5rem;
            transition: all 0.3s ease;
        }

        .social-links a:hover {
            color: #e64a19;
            transform: translateY(-3px);
        }

        .copyright {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #555;
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 15px 20px;
            }

            .nav-links {
                position: absolute;
                top: 70px;
                left: 0;
                width: 100%;
                background-color: rgba(255, 255, 255, 0.98);
                flex-direction: column;
                align-items: center;
                padding: 20px 0;
                clip-path: circle(0px at 90% -10%);
                transition: all 0.5s ease-out;
                pointer-events: none;
                z-index: 1000;
            }

            .nav-links.active {
                clip-path: circle(1000px at 90% -10%);
                pointer-events: all;
            }

            .nav-links li {
                margin: 15px 0;
            }

            .auth-buttons {
                display: none;
            }
            
            .menu-toggle {
                display: flex;
            }

            .hero h1 {
                font-size: 2.5rem;
            }
            
            .hero p {
                font-size: 1rem;
            }
            
            .cta-buttons {
                flex-direction: column;
                gap: 15px;
            }
            
            .cta-btn {
                width: 100%;
                justify-content: center;
            }

            /* Show mobile login button */
            .mobile-login-btn {
                display: flex;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            <img src="{{ asset('image/santafe.png') }}" alt="Santa Fe Water" class="logo-img">
            <span class="logo-text">Santa Fe Water</span>
        </div>
        
        <ul class="nav-links">
            <li><a href="main-form"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="#"><i class="fas fa-tint"></i> Services</a></li>
            <li><a href="#"><i class="fas fa-file-invoice-dollar"></i> Billing</a></li>
            <li><a href="about"><i class="fas fa-chart-line"></i> About</a></li>
            <li><a href="#"><i class="fas fa-phone"></i> Contact</a></li>
            <!-- Mobile login button will be added here by JavaScript -->
        </ul>
        
        <div class="auth-buttons">
            <button class="btn login-btn" id="loginBtn"><i class="fas fa-sign-in-alt"></i> Login</button>
        </div>
        
        <div class="menu-toggle">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>

   <section class="hero">
    <h1>WELCOME TO SANTA FE WATER BILLING SYSTEM</h1>
    <p>Efficient water management and billing services for Santa Fe residents</p>
    <div class="cta-buttons">
        <button class="cta-btn secondary-btn" id="learnMoreBtn">
            <i class="fas fa-question-circle"></i> LEARN MORE
        </button>
    </div>
</section>

    <section class="features">
       
    </section>

    <footer>
        <div class="footer-content">
            <div class="footer-column">
                Develop by: Niño Mike Zaspa | Aian Desucatan | Mirriam Batain | Elmer Espinosa
            </div>
            <div class="footer-column">
                <h3>Contact Us</h3>
                <ul>
                    <li><i class="fas fa-phone"></i> 09943434343</li>
                    <li><i class="fas fa-envelope"></i> santafewaterbilling@gmail.com</li>
                    <li><i class="fas fa-map-marker-alt"></i> Santa Fe, Cebu, Philippines</li>
                    <li><i class="fas fa-clock"></i> Mon-Fri: 8AM-5PM</li>
                </ul>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; 2025. Santa Fe Water Utility. All rights reserved.</p>
        </div>
    </footer>
    <script>
        const menuToggle = document.querySelector('.menu-toggle');
        const navLinks = document.querySelector('.nav-links');
        const loginBtn = document.getElementById('loginBtn');
        const learnMoreBtn = document.getElementById('learnMoreBtn');
        
        // Create mobile login button
        const mobileLoginBtn = document.createElement('li');
        mobileLoginBtn.innerHTML = `
            <button class="mobile-login-btn" id="mobileLoginBtn">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        `;
        
        // Add mobile login button to nav links
        navLinks.appendChild(mobileLoginBtn);
        
        menuToggle.addEventListener('click', () => {
            navLinks.classList.toggle('active');
            menuToggle.classList.toggle('active');
        });
        
        loginBtn.addEventListener('click', () => {
            window.location.href = "admin-login";
        });
        
        // Add event listener for mobile login button
        document.getElementById('mobileLoginBtn').addEventListener('click', () => {
            window.location.href = "admin-login";
        });
        
        learnMoreBtn.addEventListener('click', () => {
            window.location.href = "about";
        });
    </script>
</body>
</html>