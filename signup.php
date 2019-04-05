<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = "<your_host>";
$db = "<your_database>";
$password = "<your_password>";
$link = mysqli_connect($host, $db, $password, $db);
if (mysqli_connect_error()) {
  echo "<b>Failure:</b><br>";
  echo mysqli_connect_error()."<br>";
  die("<b>Unable to connect</b><br>");
}

if ($_POST) {
  $stop = false;
  $email = $_POST['email-input'];
  $password = $_POST['password-input'];
  
  $uppercase = preg_match('@[A-Z]@', $password);
  $lowercase = preg_match('@[a-z]@', $password);
  $number    = preg_match('@[0-9]@', $password);

  if (!$uppercase || !$lowercase || !$number || strlen($password) < 8) {
    $keep_text = true;
    $alert = '<div class="alert alert-danger" role="alert"><p><b>The password does not meet the requirements:</b></p>
    <ul>
      <li>Must be a minimum of 8 characters</li>
      <li>Must contain at least 1 number</li>
      <li>Must contain at least one uppercase character</li>
      <li>Must contain at least one lowercase character</li>
    </ul>
    </div>';
    $stop = true;
  }
  
  if (!$stop) {
    $query = "select * from users where email = '".mysqli_real_escape_string($link, $email)."'";
    if ($result = mysqli_query($link, $query)) {
      $row = mysqli_fetch_array($result);
      if ($row) {
        $keep_text = true;
        $alert = '<div class="alert alert-danger" role="alert"><b>The user with this email is registered.</b></div>';
      } else {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $keep_text = false;
        $query = "insert into users (`email`,`password`) values ('".$email."', '".$password."')";
        mysqli_query($link, $query);
        $alert = '<div class="alert alert-success" role="alert"><b>You have successfully signed up!</b></div>';
      }
    } 
  }
  
}

?>


<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    
    <link rel="shortcut icon" href="images/icon.png" type="image/x-icon">

    <title>StickIt</title>
    
    <style>
      .alert {
        margin-top: 10px;
        width: 300px;
        text-align: center;
      }
      
      .alert ul {
        text-align:left;
      }
      
      #login-btn {
        margin-bottom: 10px;
        position: relative;
        top: -10px;
      }
      #h1-changed {
        margin-bottom: 0px;
        position: relative;
        
      }
      or-p {
        position: relative
        top: -200px;
      }
      h1 {
        text-align: center;
        margin-bottom: 40px;
      } 
      
      h1 img {
        position: relative;
        top: -5px;
        width: 67.5px;
      }
    </style>
  </head>
  <body>
    <h1 class="display-3"><img src="images/icon.png">Stick It</h1>
    <h2 id = "h1-changed" class="text-center">Sign Up</h2>
    <p id="or-p" class="text-center">or</p>
    <div class="container d-flex justify-content-center">
      <a href="login.php"><button id="login-btn" class="btn btn-success">Log in</button></a>
    </div>';
    
    <div class="container d-flex justify-content-center">
      <form method="post">
        <div class="form-group">
          <label for="email-input">Email address</label>
          <input type="email" name="email-input" class="form-control" id="email-input" aria-describedby="emailHelp" placeholder="Enter email" required value = 
                 "<?php
                  if ($_POST && $keep_text) {
                    echo $_POST["email-input"];
                  }
                  ?>">
          <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
        </div>
        <div class="form-group">
          <label for="password-input">Password</label>
          <input type="password" name="password-input" class="form-control" id="password-input" placeholder="Password" required value = 
                 "<?php
                  if ($_POST && $keep_text) {
                    echo $_POST["password-input"];
                  }
                  ?>">
        </div>
        <button type="submit" id="submit-btn" class="btn btn-primary">Submit</button>
      </form>
    </div>
    
    <div class="container d-flex justify-content-center">
      <?php 
        if ($_POST) {
          echo $alert;
        }
      ?>
    </div>
  
    

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    
    <script>
      
    </script>
  </body>
</html>