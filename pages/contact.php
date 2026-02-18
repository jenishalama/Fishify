<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Contact Us</title>
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/Contact.css">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>
<body>
  <!-- header -->
   <?php include 'header.php'; ?>

    <!-- CONTACT -->

<section class="contact-page">

  <h1 class="contact-title">Contact Us</h1>
  <p class="contact-subtitle">
    We're here to help! Whether you have a question about our products,
    an order, or anything else, our team is ready to assist you.
  </p>

  <div class="contact-layout">

    <!-- LEFT FORM -->
    <div class="contact-box form-box">
      <h2>Send Us a Message</h2>
      <p class="form-text">
        Have questions, feedback, or need assistance? Fill out the form below,
        and our team will get back to you as soon as possible.
      </p>

      <form id="contactForm">
        <label>Your Name</label>
        <input type="text" placeholder="" required>

        <label>Your Email</label>
        <input type="email" placeholder="" required>

        <label>Subject</label>
        <input type="text" placeholder="">

        <label>Your Message</label>
        <textarea placeholder="Type your message here..." required></textarea>

        <button type="submit">Send Message</button>
      </form>
    </div>

    <!-- RIGHT LOCATION -->
    <div class="contact-box location-box">
      <h2>Our Location</h2>

      <div class="map-container">
        <iframe
          src="https://www.google.com/maps?q=27.7172,85.3240&z=15&output=embed"
          loading="lazy"
          allowfullscreen>
        </iframe>
      </div>

      <div class="location-info">
        <p><i class="fas fa-location-dot"></i> <strong>Fishify Store</strong></p>
        <p>Saraswatinagar Road,<br>Kathmandu,<br>Nepal</p>
        <p><i class="fas fa-phone"></i> +977 974-6434664</p>
        <p><i class="fas fa-envelope"></i> fishify@gmail.com</p>
      </div>
    </div>

  </div>
</section>

  
    <!-- Footer -->
      <?php include 'footer.php'; ?>

  <script src="../js/main.js"></script>
    <script src="../js/Contact.js"></script>
</body>
</html>
