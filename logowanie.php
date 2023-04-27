<?php
  session_start();
  $login = false;
  $conn = mysqli_connect("localhost", "root", "", "Oleksiewicz_Pizzeria");
  function Cipher($ch)
  {
	  if (!ctype_alpha($ch))
		  return $ch;
      $offset = ord(ctype_upper($ch) ? 'A' : 'a');
	  return chr(fmod(((ord($ch) + 3) - $offset), 26) + $offset);
  }
  function Encipher($input)
  {
	  $output = "";
	  $inputArr = str_split($input);
	  foreach ($inputArr as $ch)
		$output .= Cipher($ch, 3);
    return $output;
  }
  if ($conn->connect_error) {
    die("Nie podlłączono bazy danych");
  }
  if (isset($_POST['submit']) && $_POST['login']!="" && $_POST['password']!="") {
    $conn = mysqli_connect("localhost", "root", "", "Oleksiewicz_Pizzeria");
    $username_user = $_POST['login'];
    $password_user = Encipher(sha1($_POST['password']));
    $query = "SELECT * FROM `userdata` WHERE `username`='$username_user' and `password`='$password_user';";
    $result = mysqli_query($conn, $query) or die(mysqli_error($conn));
    $count = mysqli_num_rows($result);
    if ($count != 1){
      header('Location: logowanie.php');
      session_destroy();
    } else {
      $_SESSION['username'] = $username_user;
      header('Location: index.php');
    }
  }
?>
<!DOCTYPE html>
<html lang='en'>

<head>
  <meta charset='UTF-8'>
  <meta http-equiv='X-UA-Compatible' content='IE=edge'>
  <meta name='viewport' content='width=device-width, initial-scale=1.0'>
  <link rel='stylesheet' href='style.css'>
  <title>Logowanie</title>
</head>
<body>
  <container class='form'>
  <h1>Pizzeria CKZiU</h1>
  <hr>
    <form action='logowanie.php' method='post'>
      <h1 id='number' for='login'>Login: </h1>
      <input id='number' class='small' type='text' name='login' id='login' placeholder='Wpisz login'>
      <h1 id='number' for='domena'>Hasło: </h1>
      <input id='number' class='small' type='password' name='password' id='password' placeholder='Wpisz hasło'><br>
      <input type='submit' name='submit' class='btn' id='sumbit' value='Zaloguj'><br>
      <a href="rejestracja.php"><p id='number' for='login'>Formularz rejestracji</p></a>
      <a href="index.php"><p id='number' for='login'>Strona pizzeri</p></a>
    </form>
    <?php
    if (isset($_POST['submit'])) {
      echo '<h4>Podałeś błędne dane</h4>';
    }
    ?>
  </container>
</body>
</html>