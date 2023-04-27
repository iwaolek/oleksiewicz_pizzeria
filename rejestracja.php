<?php
  session_start();
  if (isset($_POST['submit'])){
    $error = true;
  }
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
  if (isset($_POST['submit']) && $_POST['login']!="" && $_POST['password']!="" && $_POST['email']!="") {
    $conn = mysqli_connect("localhost", "root", "", "Oleksiewicz_Pizzeria");
    if ($conn->connect_error) {
      die("Nie podlłączono bazy danych");
    }
    $username_user = $_POST['login'];
    $password_user = Encipher(sha1($_POST['password']));
    $email_user = $_POST['email'];
    $query_1 = "SELECT * FROM `userdata` WHERE `username`='$username_user' OR `email`='$email_user';";
    $result = mysqli_query($conn, $query_1) or die(mysqli_error($conn));
    $count = mysqli_num_rows($result);
    if ($count > 0){
      $error = true;
    } else {
      if (!filter_var($email_user, FILTER_VALIDATE_EMAIL)) {
        $error = true;
      } else {
        $query_2 = "INSERT INTO userdata (username, password, email) VALUES ('$username_user', '$password_user', '$email_user')";
        $error = false;
        if (!mysqli_query($conn, $query_2)) {
          $error = true;
        };
      }
    } 
}
?>
<!DOCTYPE html>
<html lang='en'>
<head>
  <meta charset='UTF-8'>
  <meta http-equiv='X-UA-Compatible' content='IE=edge'>
  <meta name='viewport' content='width=device-width, initial-scale=1.0'>
  <title>Rejestracja</title>
  <link rel='stylesheet' href='style.css'>
</head>
<body>
  <container class='form'>
  <h1>Pizzeria CKZiU</h1>
  <hr>
    <form action='rejestracja.php' method='post'>
      <h1 id='number' for='login'>Login: </h1>
      <input id='number' class='small' type='text' name='login' id='login' placeholder='Wpisz login'>
      <h1 id='number' for='domena'>Hasło: </h1>
      <input id='number' class='small' type='password' name='password' id='password' placeholder='Wpisz hasło'>
      <h1 id='number' for='email'>Email: </h1>
      <input id='number' class='small' type='text' name='email' id='email' placeholder='Wpisz email'><br>
      <input type='submit' name='submit' class='btn' id='sumbit' value='Zarejestruj'><br>
      <a href="logowanie.php"><p id='number' for='login'>Formularz logowania</p></a>
      <a href="index.php"><p id='number' for='login'>Strona pizzeri</p></a>
      <?php
      if (isset($_POST['submit']) && $error == true){
        echo '<h4>Błąd w rejestracji</h4>';
      } else if (isset($_POST['submit'])) {
        echo '<h4>Pomyślnie zarejestrowano</h4>';
      }
      ?>
    </form>
  </container>
</body>
</html>