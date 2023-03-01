<?php
  session_start();
  $check_error = '';
  if (isset($_SESSION['username'])){
    $username = $_SESSION['username'];
  }
  $conn = mysqli_connect("localhost", "root", "", "Oleksiewicz_Pizzeria");
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
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
  if (isset($_POST['new_user_data']) && $_POST["password_user"]!="" && (($_POST["username_user"]!="") || ($_POST["new_password_user"]!="") || ($_POST["email_user"]!=""))) {
    $username = $_SESSION['username'];
    $password_user = Encipher(sha1($_POST['password_user']));
    $username_user = $_POST['username_user'];
    $query = "SELECT * FROM userdata WHERE `username`='$username' and `password`='$password_user';";
    $result = mysqli_query($conn, $query) or die(mysqli_error($conn));
    $count = mysqli_num_rows($result);
    if ($count != 1){
      #echo "Podano błędne dane";
      #echo $_SESSION['username'];
      #echo $_POST['password_user'];
      #echo " ".$password_user;
      $check_error = "Twoje hasło jest błędne";
    } else {
      if (isset($_POST["username_user"])){
        $username = $_SESSION['username'];
        $query = "SELECT * FROM userdata WHERE username='$username_user';";
        $result = mysqli_query($conn, $query) or die(mysqli_error($conn));
        $count = mysqli_num_rows($result);
        if ($count == 1){
          $check_error = "Podany login jest już zajęty";
        } else {
          string_math();
        }
      } else {
        string_math();
        }
      }
    }
  function string_math(){
    $username_user = $_POST["username_user"];
    $sql2 = "";
    $conn = mysqli_connect("localhost", "root", "", "Oleksiewicz_Pizzeria");
    if (isset($_POST["name_user"])){
      if ($_POST["name_user"]!=''){
        $name_user = $_POST["name_user"];
        $sql2 .= "UPDATE userdata SET name='$name_user' WHERE username='" . $_SESSION['username'] . "'; ";
      }
    }
    if (isset($_POST["new_password_user"])){
      if ($_POST["new_password_user"]!=''){
        $new_password_user = Encipher(sha1($_POST['new_password_user']));
        $sql2 .= "UPDATE userdata SET password='$new_password_user' WHERE username='" . $_SESSION['username'] . "'; ";
      }
    }
    if (isset($_POST["email_user"])){
      if ($_POST["email_user"]!=''){
        $email_user = $_POST["email_user"];
        $sql2 .= "UPDATE userdata SET email='$email_user' WHERE username='" . $_SESSION['username'] . "'; ";
      }
    }
    if (isset($_POST["username_user"])){
      if ($_POST["username_user"]!=''){
        $username_user = $_POST["username_user"];
        $sql2 .= "UPDATE userdata SET username='$username_user' WHERE username='" . $_SESSION['username'] . "'; ";
      }
    }
    mysqli_multi_query($conn, $sql2);
    header('Location: index.php');
    if($_POST["username_user"]!=''){
      $_SESSION['username']=$username_user;
    }
    if (isset($_POST["new_password_user"])){
      if ($_POST["new_password_user"]!=''){
        header('Location: logout.php');
      }
    }
  }
  if (isset($_POST['order']) && $_POST["pizza_number"]!="") {
    if ($_POST["pizza_number"]<5){
      $pizza_number = $_POST['pizza_number'];
      $order_time = getdate()[0];
      $sql = "INSERT INTO pizza_history (id, user_id, pizza_id, price, order_time) VALUES (NULL, (SELECT id FROM userdata WHERE username='$username'), '$pizza_number', (SELECT price FROM pizza WHERE id='$pizza_number'), '$order_time');";
      unset($_POST['order']);
      mysqli_query($conn, $sql);
    }
  }
  if (isset($_POST['send_pizza']) && $_POST['send_pizza_id']!=""){
    $sql = "UPDATE pizza_history SET force_status=1 WHERE id='" . $_POST['send_pizza_id'] . "'; ";
    mysqli_query($conn, $sql) or die(mysqli_error($conn));
  }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='stylesheet' href='style.css'>
    <title>Wypisywanie</title>
    <style>
      table, td, th {  
        border: 1px solid #757575;
        text-align: left;
      }
      table {
        border-collapse: collapse;
        width: calc(auto+5px);
      }
      th, td {
        padding: 8px;
      }
      td, th{
        text-align: center;
      }
    </style>
</head>
<body>
  <container_alfa class = "form container_alfa">
  <container>
    <h1>Pizzeria CKZiU</h1>
    <hr>
    <?php
      if (isset($_SESSION['username'])) {
        echo "<h2>Zalogowałeś się na konto:</h2><h1>". $_SESSION['username'] ."</h1>";
        echo "<a id='logout' href='logout.php'>Wyloguj się</a>";
        if ($check_error!=""){
          echo "<br><br>$check_error"; 
        }
      } else {
        echo "<a id='logout' href='logout.php'>Zaloguj się</a>";
        echo "<br><br>";
        echo "<a id='logout' href='logout.php'>Zarejestruj się</a>";
      }
    ?>
  </container>
  <hr>
  <?php
    $sql = "SELECT * FROM pizza";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
      echo "<table><tr><th colspan=3>Dostępne pizze:</th></tr>";
      echo "<tr><th>Numer</th><th>Nazwa pizzy</th><th>Cena</th></tr>";
    while($row = $result->fetch_assoc()) {
      echo "<tr><td>". $row["id"] ."</td><td>".$row["description"]."</td><td>". $row["price"] . "zł" ."</td></tr>";
    }
    echo "</table>";
    }
  ?>
  <?php
  if (isset(($_SESSION['username']))) {
    echo "<hr>
    <form action='index.php' method='post' class='display'>
      <h2>Zmień dane użytkownika: </h2>
        <label id='text' for='login'>Nowy login: </label>
        <input id='number' class='small' type='text' name='username_user' placeholder='Wpisz nowy login'>
        <label id='text' for='login'>Nowe hasło: </label>
        <input id='number' class='small' type='text' name='new_password_user' placeholder='Wpisz nowe hasło'>
        <label id='text' for='login'>Nowy email: </label>
        <input id='number' class='small' type='text' name='email_user' placeholder='Wpisz nowy email'>
        <label id='text' for='login'>Podaj bieżące hasło: </label>
        <input id='number' class='small' type='text' name='password_user' placeholder='Wpisz stare hasło'><br>
        <input type='submit' name='new_user_data' class='btn' id='sumbit' value='Dodaj'>
    </form>";
    echo "<hr>
    <form action='index.php' method='post' class='display'>
      <h2>Kup pizzę: </h2>
        <label id='text' for='pizza_id'>Podaj numer pizzy: </label>
        <input id='number' class='small' type='text' name='pizza_number' placeholder='Wpisz numer pizzy'>
        <br>
        <input type='submit' name='order' class='btn' id='sumbit' value='Zamów'>
    </form>";
  }
  ?>
  <hr>
  <?php
    if (isset($_SESSION['username'])) {
      $username = $_SESSION['username'];
      $sql = "SELECT admin FROM userdata WHERE username='$username';";
      $result = mysqli_fetch_array($conn->query($sql))['admin'];
      if ($result==0){
        $sql = "SELECT * FROM pizza_history WHERE user_id=(SELECT id FROM userdata WHERE username='$username');";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
          $i = 0;
          $cost = 0;
          echo "<br><table><tr><th colspan=4>Historia zamówień:</th></tr>";
          echo "<tr><th>Numer zamówienia<br></th><th>Nazwa pizzy</th><th>Cena</th><th>Status</th></tr>";
        while($row = $result->fetch_assoc()) {
          $i += 1;
          $cost += $row["price"];
          if (intval($row['order_time']) - getdate()[0] < - 120 || $row["force_status"] == 1){
            $status = "Dostarczono";
          } else {
            $status = "W przygotowaniu";
          }

          $history_pizza = $row["pizza_id"];
          $sql = "SELECT description FROM pizza WHERE id=$history_pizza;";
          $history_pizza = mysqli_fetch_array(mysqli_query($conn, $sql))[0];

          echo "<tr><td>". $i ."</td><td>" . $history_pizza . "</td><td>" . $row["price"] . "zł". "</td><td>" . $status . "</td></tr>";
        }
        echo "</table>";
        echo "<br><p>Łączna suma kosztów: $cost zł</p>";
        }
      } else {
        $sql = "SELECT * FROM pizza_history;";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
          $i = 0;
          echo "<br><table><tr><th colspan=6>Historia zamówień:</th></tr>";
          echo "<tr><th>id<br></th><th>username</th><th>pizza</th><th>ordered</th><th>status</th></tr>";
        while($row = $result->fetch_assoc()) {

          $history_username = $row["user_id"];
          $sql = "SELECT username FROM userdata WHERE id=$history_username;";
          $history_username = mysqli_fetch_array(mysqli_query($conn, $sql))[0];

          $history_pizza = $row["pizza_id"];
          $sql = "SELECT description FROM pizza WHERE id=$history_pizza;";
          $history_pizza = mysqli_fetch_array(mysqli_query($conn, $sql))[0];
          
          $date = gmdate("d.m.Y H:i:s", $row["order_time"]);

          if (intval($row['order_time']) - getdate()[0] < - 120 || $row["force_status"] == 1){
            $status = "Dostarczono";
          }else {
            $status = "W przygotowaniu";
          }

          echo "<tr><td>". $row["id"] ."</td><td>" . $history_username . "</td><td>" . $history_pizza . "</td><td>" . $date . "</td><td>" . $status . "</td></tr>";
        }
        echo "</table>";
        echo "<hr>
        <form action='index.php' method='post' class='display'>
          <h2>Wyślij pizze: </h2>
          <label id='text' for='send_pizza_id'>id pizzy: </label>
          <input id='number' class='small' type='text' name='send_pizza_id' placeholder='Podaj id jednej pizzy'><br>
          <input type='submit' name='send_pizza' class='btn' id='sumbit' value='Wyślij'>
        </form>";
        }
      }
    }
  ?>
  <container_alfa>
</body>
</html>
