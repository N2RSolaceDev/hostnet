<?php
session_start();
require_once 'config/database.php';

// Security Headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com ; font-src 'self' https://fonts.gstatic.com ; img-src 'self' data: https:");
header('Referrer-Policy: no-referrer-when-downgrade');

// Generate CSRF Token
$csrf_token = Security::generateCSRFToken(); // Assuming Security class exists in your system
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>cloned.lol - Cloned to the brim.</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter :wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-gradient: linear-gradient(135deg, #000000 0%, #0a0a0a 50%, #121212 100%);
            --text-color: #ffffff;
            --bg-mockup: rgba(255, 255, 255, 0.05);
            --border-mockup: rgba(255, 68, 68, 0.2);
            --bg-input: rgba(255, 68, 68, 0.05);
            --border-input: rgba(255, 68, 68, 0.3);
            --placeholder-input: #888888;
            --shadow: rgba(0, 0, 0, 0.3);
        }
        .light-mode {
            --bg-gradient: linear-gradient(135deg, #f9f9f9 0%, #ffffff 100%);
            --text-color: #000000;
            --bg-mockup: rgba(0, 0, 0, 0.05);
            --border-input: rgba(255, 68, 68, 0.3);
            --placeholder-input: #aaaaaa;
            --shadow: rgba(0, 0, 0, 0.1);
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-gradient);
            color: var(--text-color);
            min-height: 100vh;
            overflow-x: hidden;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        .navbar {
            padding: 20px 0;
            position: relative;
            z-index: 100;
        }
        .nav-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            display: flex;
            align-items: center;
            font-size: 24px;
            font-weight: 700;
            color: #ff4444;
            text-decoration: none;
        }
        .logo::before {
            content: "🔗";
            margin-right: 10px;
            font-size: 28px;
        }
        .nav-links {
            display: flex;
            gap: 30px;
            align-items: center;
        }
        .nav-links a {
            color: #cccccc;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        .nav-links a:hover {
            color: #ff4444;
        }
        .auth-buttons {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-outline {
            background: transparent;
            border: 2px solid #ff4444;
            color: #ff4444;
        }
        .btn-outline:hover {
            background: #ff4444;
            color: #ffffff;
        }
        .btn-primary {
            background: linear-gradient(135deg, #ff4444 0%, #cc3333 100%);
            color: #ffffff;
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #ff5555 0%, #dd4444 100%);
            transform: translateY(-2px);
        }
        .theme-toggle {
            background: none;
            border: none;
            color: #ff4444;
            font-size: 20px;
            cursor: pointer;
            transition: color 0.3s ease;
        }
        .theme-toggle:hover {
            color: #ff5555;
        }
        .brightness-control {
            display: none;
            align-items: center;
            gap: 8px;
        }
        .brightness-slider {
            width: 80px;
            height: 6px;
            border-radius: 3px;
            background: #ff4444;
            appearance: none;
            outline: none;
            transition: background 0.3s ease;
        }
        .brightness-slider::-webkit-slider-thumb {
            appearance: none;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: #ff4444;
            cursor: pointer;
            box-shadow: 0 0 4px rgba(0,0,0,0.3);
        }
        .hero {
            text-align: center;
            padding: 80px 0 120px;
            position: relative;
        }
        .hero h1 {
            font-size: 4rem;
            font-weight: 700;
            margin-bottom: 20px;
            background: linear-gradient(135deg, var(--text-color) 0%, #ff4444 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.2;
        }
        .hero p {
            font-size: 1.2rem;
            color: #cccccc;
            margin-bottom: 40px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.6;
        }
        .claim-section {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            margin-bottom: 60px;
            flex-wrap: wrap;
        }
        .url-input {
            background: var(--bg-input);
            border: 2px solid var(--border-input);
            border-radius: 8px;
            padding: 15px 20px;
            color: var(--text-color);
            font-size: 16px;
            min-width: 300px;
            width: 100%;
            max-width: 400px;
            backdrop-filter: blur(10px);
        }
        .url-input::placeholder {
            color: var(--placeholder-input);
        }
        .url-input:focus {
            outline: none;
            border-color: #ff4444;
            box-shadow: 0 0 20px rgba(255, 68, 68, 0.3);
        }
        .mockup-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 40px;
            flex-wrap: wrap;
            margin-top: 60px;
        }
        .device-mockup {
            background: var(--bg-mockup);
            border: 1px solid var(--border-mockup);
            border-radius: 20px;
            padding: 20px;
            backdrop-filter: blur(20px);
            transform: perspective(1000px) rotateY(-5deg);
            transition: transform 0.3s ease;
            box-shadow: 0 10px 30px var(--shadow);
        }
        .device-mockup:hover {
            transform: perspective(1000px) rotateY(0deg) translateY(-10px);
        }
        .device-mockup.mobile {
            width: 250px;
            height: 400px;
            transform: perspective(1000px) rotateY(5deg);
        }
        .device-mockup.desktop {
            width: 400px;
            height: 300px;
        }
        .mockup-screen {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d1010 100%);
            border-radius: 12px;
            padding: 15px;
            height: 100%;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .mockup-header {
            display: flex;
            gap: 8px;
            margin-bottom: 15px;
        }
        .mockup-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #ff4444;
        }
        .mockup-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .bg-effect {
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 68, 68, 0.1) 0%, transparent 70%);
            filter: blur(40px);
            animation: float 6s ease-in-out infinite;
        }
        .bg-effect:nth-child(1) {
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }
        .bg-effect:nth-child(2) {
            top: 60%;
            right: 10%;
            animation-delay: 2s;
        }
        .bg-effect:nth-child(3) {
            bottom: 10%;
            left: 50%;
            animation-delay: 4s;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px) scale(1); }
            50% { transform: translateY(-20px) scale(1.1); }
        }
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }
            .hero h1 {
                font-size: 2.5rem;
            }
            .claim-section {
                flex-direction: column;
            }
            .url-input {
                min-width: 280px;
                width: 100%;
            }
            .mockup-container {
                flex-direction: column;
                gap: 20px;
            }
        }
        .security-badge {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            border-radius: 8px;
            padding: 10px 15px;
            font-size: 12px;
            color: #ff4444;
            backdrop-filter: blur(10px);
            z-index: 999;
        }
        .brightness-wrapper {
            transition: filter 0.3s ease;
        }
    </style>
</head>
<body id="page-body">
    <!-- Background Effects -->
    <div class="bg-effect"></div>
    <div class="bg-effect"></div>
    <div class="bg-effect"></div>

    <div class="brightness-wrapper">
        <!-- Navigation -->
        <nav class="navbar">
            <div class="container">
                <div class="nav-content">
                    <a href="/" class="logo">cloned.lol</a>
                    <div class="nav-links">
                        <a href="credits.php">Credits</a>
                        <a href="#pricing">Pricing</a>
                        <a href="#support">Support</a>
                        <a href="#docs">Docs</a>
                    </div>
                    <div class="auth-buttons">
                        <div class="brightness-control" id="brightnessControl">
                            <input type="range" min="0.5" max="2" step="0.1" value="1" class="brightness-slider" id="brightnessSlider">
                        </div>
                        <button class="theme-toggle" id="themeToggle">🌙</button>
                        <a href="login.php" class="btn btn-outline">Login</a>
                        <a href="register.php" class="btn btn-primary">Sign Up Free</a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="hero">
            <div class="container">
                <h1>Become addicted to our bio link.</h1>
                <p>cloned.lol (formally addiction.rest) is the cleanest bio link all features are free, constantly updated, become addicted today! (Hiring staff and claim a verified badge in discord.gg/clonedlol).</p>
                <div class="claim-section">
                    <input type="text" class="url-input" placeholder="cloned.lol/username" id="usernameInput">
                    <a href="#" class="btn btn-primary" onclick="claimUsername()">Claim Now</a>
                </div>

                <!-- Mockup Devices -->
                <div class="mockup-container">
                    <div class="device-mockup desktop">
                        <div class="mockup-screen">
                            <div class="mockup-header">
                                <div class="mockup-dot"></div>
                                <div class="mockup-dot"></div>
                                <div class="mockup-dot"></div>
                            </div>
                            <div class="mockup-content">
                                <div class="mockup-bar"></div>
                                <div class="mockup-bar short"></div>
                                <div class="mockup-bar medium"></div>
                                <div class="mockup-bar"></div>
                                <div class="mockup-bar short"></div>
                            </div>
                        </div>
                    </div>
                    <div class="device-mockup mobile">
                        <div class="mockup-screen">
                            <div class="mockup-header">
                                <div class="mockup-dot"></div>
                                <div class="mockup-dot"></div>
                                <div class="mockup-dot"></div>
                            </div>
                            <div class="mockup-content">
                                <div class="mockup-bar medium"></div>
                                <div class="mockup-bar"></div>
                                <div class="mockup-bar short"></div>
                                <div class="mockup-bar medium"></div>
                                <div class="mockup-bar"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Security Badge -->
        <div class="security-badge">
            🔒 Security is our real passion. You are protected from any data leaks here <3
        </div>
    </div>

    <!-- JavaScript for Theme & Brightness Control -->
    <script>
        const toggleButton = document.getElementById('themeToggle');
        const brightnessControl = document.getElementById('brightnessControl');
        const brightnessSlider = document.getElementById('brightnessSlider');
        const brightnessWrapper = document.querySelector('.brightness-wrapper');
        const body = document.getElementById('page-body');

        function setTheme(themeName, brightness) {
            if (themeName === 'light') {
                body.classList.add('light-mode');
                toggleButton.textContent = '☀️';
                brightnessControl.style.display = 'flex';
                brightnessSlider.value = brightness || 1;
                brightnessWrapper.style.filter = `brightness(${brightness || 1})`;
            } else {
                body.classList.remove('light-mode');
                toggleButton.textContent = '🌙';
                brightnessControl.style.display = 'none';
                brightnessWrapper.style.filter = 'brightness(1)';
            }
            localStorage.setItem('theme', themeName);
            localStorage.setItem('brightness', brightness || 1);
        }

        toggleButton.addEventListener('click', () => {
            const currentBrightness = parseFloat(brightnessSlider.value);
            if (body.classList.contains('light-mode')) {
                setTheme('dark', currentBrightness);
            } else {
                setTheme('light', currentBrightness);
            }
        });

        brightnessSlider.addEventListener('input', e => {
            const val = e.target.value;
            brightnessWrapper.style.filter = `brightness(${val})`;
            if (body.classList.contains('light-mode')) {
                localStorage.setItem('brightness', val);
            }
        });

        (() => {
            const savedTheme = localStorage.getItem('theme');
            const savedBrightness = localStorage.getItem('brightness') || 1;
            if (savedTheme === 'light') {
                setTheme('light', savedBrightness);
            } else {
                setTheme('dark', savedBrightness);
            }
        })();
    </script>

    <!-- JavaScript for Username Claim -->
    <script>
        function claimUsername() {
            const username = document.getElementById('usernameInput').value.replace('cloned.lol/', '');
            if (username.trim()) {
                window.location.href = `register.php?username=${encodeURIComponent(username)}`;
            } else {
                alert('Please enter a username');
            }
        }
        document.getElementById('usernameInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') claimUsername();
        });
        document.getElementById('usernameInput').addEventListener('input', function(e) {
            let value = e.target.value;
            if (!value.startsWith('cloned.lol/')) {
                value = value.replace(/^(addiction\.rest\/)?/, 'cloned.lol/');
                e.target.value = value;
            }
        });
    </script>
</body>
</html>