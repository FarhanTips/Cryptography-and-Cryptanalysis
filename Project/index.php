<?php
  session_start();
  // Redirect if not logged in
  if (!isset($_SESSION['username'])) {
    $_SESSION['msg'] = "You must log in first";
    header('location: login.php');
  }
  if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['username']);
    header("location: login.php");
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home</title>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
  <div class="container">
    <div class="header">
      <h2>Welcome to the CSE447 Project</h2>
    </div>

    <div class="content">
      <!-- Success Message -->
      <?php if (isset($_SESSION['success'])) : ?>
        <div class="success">
          <h3>
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
          </h3>
        </div>
      <?php endif ?>

      <!-- Logged In User Message -->
      <?php if (isset($_SESSION['username'])) : ?>
        <p class="welcome-message">Welcome <strong><?php echo $_SESSION['username']; ?></strong></p>
        <p><a href="index.php?logout='1'" class="logout-btn">Logout</a></p>

        <!-- Encryption Form -->
        <form method="post" action="server.php">
          <div class="input-group">
            <label for="text_to_encrypt">Enter text to encrypt:</label>
            <textarea name="text_to_encrypt" id="text_to_encrypt" required></textarea>
          </div>
          <button type="submit" id="encrypt-btn" name="encrypt_text">Encrypt</button>
        </form>

        <!-- Display encrypted result -->
        <?php if (isset($_SESSION['encrypted_text'])): ?>
          <div class="encrypted-result">
            <p><strong>Encrypted Text:</strong></p>
            <textarea readonly><?php echo $_SESSION['encrypted_text']; ?></textarea>
          </div>
          <?php unset($_SESSION['encrypted_text']); ?>
        <?php endif; ?>

        <!-- Decryption Form -->
        <form method="post" action="server.php">
          <div class="input-group">
            <label for="cipher_text">Enter ciphertext to decrypt:</label>
            <textarea name="cipher_text" id="cipher_text" required></textarea>
          </div>
          <button type="submit" id="decrypt-btn" name="decrypt_text">Decrypt</button>
        </form>

        <!-- Display decrypted result -->
        <?php if (isset($_SESSION['decrypted_text'])): ?>
          <div class="decrypted-result">
            <p><strong>Decrypted Text:</strong></p>
            <textarea readonly><?php echo $_SESSION['decrypted_text']; ?></textarea>
          </div>
          <?php unset($_SESSION['decrypted_text']); ?>
        <?php endif; ?>

      <?php endif; ?>
    </div>
  </div>
</body>
</html>
