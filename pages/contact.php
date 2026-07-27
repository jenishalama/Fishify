<?php
include 'db.php';
$contact_success = false;
$contact_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$name || !$email || !$message) {
        $contact_error = 'Please fill in name, email, and message.';
    } else {
        // Ensure table exists (in case setup wasn't run)
        $conn->query("CREATE TABLE IF NOT EXISTS contact_messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(255) NOT NULL,
            subject VARCHAR(255) DEFAULT NULL,
            message TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("ssss", $name, $email, $subject, $message);
            if ($stmt->execute()) {
                $contact_success = true;
            } else {
                $contact_error = 'Something went wrong. Please try again.';
            }
            $stmt->close();
        } else {
            $contact_error = 'Something went wrong. Please try again.';
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Contact Us | Fishify</title>
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/contact.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <?php include 'header.php'; ?>

  <section class="contact-hero">
    <div class="container">
      <h1 class="contact-title">Contact Us</h1>
      <p class="contact-subtitle">
        We're here to help. Questions about products, orders, or anything else? Our team will get back to you soon.
      </p>
    </div>
  </section>

  <section class="contact-content">
    <div class="container">
      <div class="contact-layout">
        <div class="contact-box form-box">
          <div class="contact-box-header">
            <div class="header-title-row">
              <span class="contact-box-icon"><i class="fas fa-envelope"></i></span>
              <h2>Send a message</h2>
            </div>
            <?php if (!$contact_success): ?>
              <p class="form-intro">Fill out the form below and we'll respond as soon as we can.</p>
            <?php endif; ?>
          </div>
          <?php if ($contact_success): ?>
            <div class="contact-success-box">
              <div class="contact-success-icon-wrap">
                <i class="fas fa-check"></i>
              </div>
              <p class="contact-success-message">Thank you! Your message has been sent. We'll get back to you soon.</p>
            </div>
          <?php else: ?>
          <?php if ($contact_error): ?>
            <div class="contact-alert contact-alert-error">
              <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($contact_error) ?>
            </div>
          <?php endif; ?>
          <form id="contactForm" class="contact-form" method="post" action="">
            <div class="form-group">
              <label for="contact-name">Your name</label>
              <input type="text" id="contact-name" name="name" placeholder="e.g. Anonymous" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
            </div>
            <div class="form-group">
              <label for="contact-email">Email</label>
              <input type="email" id="contact-email" name="email" placeholder="you@example.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>
            <div class="form-group">
              <label for="contact-subject">Subject</label>
              <input type="text" id="contact-subject" name="subject" placeholder="How can we help?" value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label for="contact-message">Message</label>
              <textarea id="contact-message" name="message" rows="5" placeholder="Type your message here..." required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
            </div>
            <button type="submit" class="btn-contact-submit">
              <i class="fas fa-paper-plane"></i> Send message
            </button>
          </form>
          <?php endif; ?>
        </div>

        <div class="contact-box location-box">
          <div class="contact-box-header">
            <div class="header-title-row">
              <span class="contact-box-icon"><i class="fas fa-map-marker-alt"></i></span>
              <h2>Find us</h2>
            </div>
          </div>
          <div class="map-container">
            <iframe
              src="https://www.google.com/maps?q=27.7172,85.3240&z=15&output=embed"
              loading="lazy"
              allowfullscreen
              title="Fishify location map">
            </iframe>
          </div>
          <div class="location-info">
            <div class="location-item">
              <i class="fas fa-location-dot"></i>
              <div>
                <strong>Fishify Store</strong>
                <span>Saraswatinagar Road, Kathmandu, Nepal</span>
              </div>
            </div>
            <div class="location-item">
              <i class="fas fa-phone"></i>
              <div>
                <strong>Phone</strong>
                <a href="tel:+9779746434664">+977 974-6434665</a>
              </div>
            </div>
            <div class="location-item">
              <i class="fas fa-envelope"></i>
              <div>
                <strong>Email</strong>
                <a href="mailto:fishify@gmail.com">fishify@gmail.com</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <?php include 'footer.php'; ?>
  <script src="../js/main.js"></script>
</body>
</html>
