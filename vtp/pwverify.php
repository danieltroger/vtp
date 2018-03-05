<?php
include_once "pwds.php";
header('P3P: CP=”NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM”'); // https://stackoverflow.com/a/2955720
 // https://www.formget.com/login-form-in-php/
session_start(); // Starting Session
$error = ''; // Variable To Store Error Message
$jli = false; // workaround for ie8 if domain contains dash to not need session cookies (but you'll have to enter the password every time)
if (isset($_POST['password']))
{
  if (empty($_POST['password']))
  {
  $error = "Username or Password is invalid";
  }
  else
  {
    $password = $_POST['password'];
    if ($password == password1)
    {
      $_SESSION['logged_in'] = true;
      $jli = true;
    }
    else
    {
      $error = "Username or Password is invalid";
    }
  }
}
if(strlen($error) > 0 || (!isset($_SESSION['logged_in']) && $jli == false))
{
  ?><!DOCTYPE html>
  <html>
  <head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width" />
  <meta name="apple-mobile-web-app-capable" content="yes" />
  <meta name="mobile-web-app-capable" content="yes" />
  <meta name="apple-mobile-web-app-title" content="Vertretungsplan" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login</title>
  <style>
  @font-face
  {
    font-family: 'Waldorf';
    font-style: normal;
    src: url(Waldorf.ttf) format('truetype');
  }
  body{font-family: Waldorf;}
  </style>
  <script>
  window.onload = function()
  {
    var pwfield = document.getElementById("password");
    if(localStorage.getItem("password") != null && pwfield.value.length < 1)
    {
      pwfield.value = localStorage.getItem("password");
      <?php if($error == ""){echo "document.getElementsByName('submit')[0].click();";} ?>
    }
    pwfield.onkeypress = pwfield.onchange = function(e)
    {
      localStorage.setItem("password",e.target.value);
    }
  };
  </script>
  </head>
  <body>
  <div id="main">
  <h1>Anmeldung</h1>
  <form method="post">
  <label>Passwort :</label>
  <input id="password" name="password" placeholder="Passwort" type="text" pattern="[0-9]*">
  <input name="submit" type="submit" value="Login">
  <br />
  <br />
  <span><?php echo $error; ?></span>
  </form>
  </div>
  </body>
  </html>
  <?php
  die();
  }
?>
