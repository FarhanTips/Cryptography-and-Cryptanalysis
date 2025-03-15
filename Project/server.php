<?php
session_start();

// initializing variables
$username = "";
$email    = "";
$errors = array(); 

// connect to the database
$db = mysqli_connect('localhost', 'root', '', 'project');

// AES Encryption/Decryption settings
$key = 'your-secret-key'; // A secret key for AES encryption
$method = 'aes-256-cbc'; // AES method (using AES-256-CBC here)
$iv_length = openssl_cipher_iv_length($method); // Initialization vector length

// REGISTER USER
if (isset($_POST['reg_user'])) {
  // receive all input values from the form
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
  $password_2 = mysqli_real_escape_string($db, $_POST['password_2']);

  // form validation: ensure that the form is correctly filled
  if (empty($username)) { array_push($errors, "Username is required"); }
  if (empty($email)) { array_push($errors, "Email is required"); }
  if (empty($password_1)) { array_push($errors, "Password is required"); }
  if ($password_1 != $password_2) {
    array_push($errors, "The two passwords do not match");
  }

  // first check the database to make sure 
  $user_check_query = "SELECT * FROM users WHERE username='$username' OR email='$email' LIMIT 1";
  $result = mysqli_query($db, $user_check_query);
  $user = mysqli_fetch_assoc($result);

  if ($user) { 
    if ($user['username'] === $username) {
      array_push($errors, "Username already exists");
    }
    if ($user['email'] === $email) {
      array_push($errors, "Email already exists");
    }
  }

  // Register the user if there are no errors
  if (count($errors) == 0) {
    $password = password_hash($password_1, PASSWORD_DEFAULT);
    $query = "INSERT INTO users (username, email, password) 
              VALUES('$username', '$email', '$password')";
    mysqli_query($db, $query);
    $_SESSION['username'] = $username;
    $_SESSION['success'] = "You are now logged in";
    header('location: index.php');
  }
}

// LOGIN USER
if (isset($_POST['login_user'])) {
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $password = mysqli_real_escape_string($db, $_POST['password']);

  if (empty($username)) {
    array_push($errors, "Username is required");
  }
  if (empty($password)) {
    array_push($errors, "Password is required");
  }

  if (count($errors) == 0) {
    $query = "SELECT * FROM users WHERE username='$username'";
    $results = mysqli_query($db, $query);
    $user = mysqli_fetch_assoc($results);

    if ($user && password_verify($password, $user['password'])) {
      $_SESSION['username'] = $username;
      $_SESSION['success'] = "You are successfully logged in";
      header('location: index.php');
    } else {
      array_push($errors, "Wrong username or password");
    }
  }
}

// ENCRYPT TEXT INPUT (AES)
if (isset($_POST['encrypt_text'])) {
  $text_to_encrypt = mysqli_real_escape_string($db, $_POST['text_to_encrypt']);
  
  if (empty($text_to_encrypt)) {
    array_push($errors, "Text is required for encryption");
  } else {
    // Encrypt the text using AES
    $iv = openssl_random_pseudo_bytes($iv_length); // Generate random IV for encryption
    $ciphertext = openssl_encrypt($text_to_encrypt, $method, $key, 0, $iv); // Encrypt text
    $encrypted_result = base64_encode($ciphertext . "::" . base64_encode($iv)); // Concatenate cipher and IV, base64 encode it

    // Store the encrypted text and IV in session
    $_SESSION['encrypted_text'] = $encrypted_result;  
    header('location: index.php'); // Redirect to show the result
  }
}

// DECRYPT TEXT INPUT (AES)
if (isset($_POST['decrypt_text'])) {
  $ciphertext = mysqli_real_escape_string($db, $_POST['cipher_text']);

  if (empty($ciphertext)) {
    array_push($errors, "Ciphertext is required for decryption");
  } else {
    // Decode the ciphertext and extract IV
    $decoded = base64_decode($ciphertext);
    list($cipher, $iv_base64) = explode("::", $decoded, 2);
    $iv = base64_decode($iv_base64);  // Decode the IV

    // Decrypt the text using AES
    $decrypted_text = openssl_decrypt($cipher, $method, $key, 0, $iv); // Decrypt the text

    // Store the decrypted text in session
    $_SESSION['decrypted_text'] = $decrypted_text;  
    header('location: index.php'); // Redirect to show the result
  }
}
?>