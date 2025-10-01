<?php
session_start();


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Crown & King Hair Salon</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    /* ACCOUNT DROPDOWN STYLES */
    .account {
      position: relative;
      display: flex;
      align-items: center;
      gap: 8px;
      color: #fff;
      font-weight: 700;
      cursor: pointer;
    }
    .account img {
      width: 35px;
      height: 35px;
    }
    .account-dropdown {
      display: none;
      position: absolute;
      top: 120%;
      right: 0;
      background: #222;
      border: 1px solid #555;
      padding: 10px;
      width: 200px;
      text-align: left;
      box-shadow: 0 2px 8px rgba(0,0,0,0.5);
      z-index: 1000;
    }
    .account-dropdown p {
      margin: 5px 0;
      font-size: 0.95rem;
    }
    .account-dropdown form {
      margin-top: 10px;
    }
    .account-dropdown button {
      width: 100%;
      padding: 8px 0;
      border: none;
      background: #cca46c;
      color: #222;
      font-weight: 700;
      cursor: pointer;
      border-radius: 5px;
    }
    .account-dropdown button:hover {
      background: #e0b865;
    }
  </style>
</head>
<body>

  <!-- HEADER -->
  <header>
    <div class="container nav">
      <img src="img/logo.png" alt="Logo" class="logo">
      <nav>
        <a href="#home" class="active">Home</a>
        <a href="#services">Service</a>
        <a href="#about">About</a>
        <a href="#contact">Contact</a>
        <a href="../Bookings/index.php">Bookings</a>
      </nav>

      <!-- ACCOUNT -->
      <div class="account" id="account">
        <img src="img/profile.png" alt="Account Icon">
        <span id="account-name">
          <?php
          if(isset($_SESSION['username']) && !empty($_SESSION['username'])){
            echo "Hi " . htmlspecialchars($_SESSION['username']);
          } else {
            echo "Account";
          }
          ?>
        </span>

        <!-- DROPDOWN -->
        <div class="account-dropdown" id="account-dropdown">
          <?php if(isset($_SESSION['username']) && !empty($_SESSION['username'])): ?>
            <p><strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></p>
            <p><?php echo htmlspecialchars($_SESSION['email']); ?></p>
            <form action="../Logout/logout.php" method="POST">
              <button type="submit">Logout</button>
            </form>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </header>

  <!-- HERO -->
  <section id="home" class="hero" style="background-image:url('img/Background.png')">
    <div class="hero-content container">
      <h1>MAKING YOU LOOK <span class="accent">AWESOME & MANLY</span></h1>
      <p>Experience luxury and elegance at Crown and Glow Hair Salon. Our expert stylists create stunning transformations that enhance your natural beauty.</p>
      <a href="../Book/index.php" class="btn">BOOK NOW</a>
    </div>
  </section>

  <!-- SERVICES -->
  <section id="services" class="services">
    <div class="container">
      <h2>SERVICES</h2>
      <div class="service-grid">
        <div>
          <h3>Haircuts & Styling</h3>
          <ul>
            <li>Men’s haircut</li>
            <li>Women’s haircut</li>
            <li>Children’s haircut</li>
            <li>Blow dry & styling</li>
            <li>Bridal/occasion styling</li>
          </ul>
        </div>
        <div>
          <h3>Hair Treatments</h3>
          <ul>
            <li>Deep conditioning treatment</li>
            <li>Keratin treatment</li>
            <li>Scalp treatment</li>
            <li>Hair strengthening & repair</li>
          </ul>
        </div>
        <div>
          <h3>Hair Coloring</h3>
          <ul>
            <li>Full hair color</li>
            <li>Highlights / lowlights</li>
            <li>Ombre / balayage</li>
            <li>Root touch-up</li>
          </ul>
        </div>
      </div>
      <a href="#" class="learn-more">LEARN MORE</a>
    </div>
  </section>

  <!-- ABOUT -->
  <section id="about" class="about">
    <div class="container">
      <h2>ABOUT US</h2>
      <div class="timeline">
        <div>
          <h3>Who We Are</h3>
          <p>Crown & King Hair Salon is a modern, full-service salon dedicated to both men and women. We provide premium hair care, styling, and treatment services in a comfortable and stylish environment.</p>
        </div>
        <div>
          <h3>Our Mission</h3>
          <p>To deliver professional hairstyling with a personal touch, ensuring every client leaves feeling confident and refreshed.</p>
        </div>
        <div>
          <h3>Why Choose Us</h3>
          <ul>
            <li>Experienced and skilled stylists</li>
            <li>High-quality hair products</li>
            <li>Personalized services</li>
            <li>Modern, welcoming environment</li>
          </ul>
        </div>
        <div>
          <h3>Our Vision</h3>
          <p>To become the go-to salon in the community for creativity, professionalism, and exceptional customer experiences.</p>
        </div>
        <div>
          <h3>Operating Hours</h3>
          <p>Monday – Friday: 09:00 AM – 07:00 PM<br>Saturday: 09:00 AM – 05:00 PM<br>Sunday: Closed</p>
        </div>
      </div>
    </div>
  </section>

  <!-- CONTACT -->
  <section id="contact" class="contact">
    <div class="container contact-grid">
      <div>
        <h2>CONTACT</h2>
        <h3>CROWN & GLOW HAIR SALON</h3>
        <div class="social">
          <a href="#"><img src="img/facebook.png" alt="Facebook"></a>
          <a href="#"><img src="img/instagram.png" alt="Instagram"></a>
        </div>
        <a href="#contact" class="btn-outline">CONTACT US</a>
      </div>
      <div>
        <h3>CONTACT INFO</h3>
        <p><img src="img/location.png" alt=""> Thohoyandou, UNIVERSITY OF VENDA</p>
        <p><img src="img/phone.png" alt=""> 073 024 3864, 066 3654 8585</p>
        <p><img src="img/email.png" alt=""> info@group15.co.za</p>
      </div>
    </div>
  </section>

  <script>
    const account = document.getElementById('account');
    const dropdown = document.getElementById('account-dropdown');
    const isLoggedIn = <?php echo isset($_SESSION['username']) ? 'true' : 'false'; ?>;

    account.addEventListener('click', () => {
      if(isLoggedIn){
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
      } else {
        window.location.href = '../Sign in/index.php';
      }
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
      if(!account.contains(e.target)){
        dropdown.style.display = 'none';
      }
    });
  </script>
  <script src="script.js"></script>
</body>
</html>
