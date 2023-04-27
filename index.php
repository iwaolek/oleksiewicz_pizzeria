<?php
  session_start();
  $check_error = '';
  $conn = mysqli_connect("localhost", "root", "", "Oleksiewicz_Pizzeria");
  if (isset($_SESSION['username'])){
    $username = $_SESSION['username'];
    $sql = "SELECT role_id FROM userdata WHERE username='$username';";
    $role = mysqli_fetch_array($conn->query($sql))['role_id'];
  }
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
    $sql = "SELECT * FROM pizza WHERE id = '" . $_POST['pizza_number'] . "' AND deleted = '0';";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
      $pizza_number = $_POST['pizza_number'];
      $sql1 = "INSERT INTO pizza_order (id, user_id, pizza_id, price) VALUES (NULL, (SELECT id FROM userdata WHERE username='$username'), '$pizza_number', (SELECT price FROM pizza WHERE id='$pizza_number'));";
      $sql2 = " INSERT INTO pizza_history (id, user_id, pizza_preparation, pizza_delivery, pizza_order) VALUES (NULL, (SELECT id FROM userdata WHERE username='$username'), NULL, NULL, NULL);";
      mysqli_query($conn, $sql1);
      mysqli_query($conn, $sql2);
      unset($_POST['order']);
    }
  }
  if (isset($_POST['add_pizza']) && isset($_POST['add_pizza_description'])!="" && isset($_POST['add_pizza_price'])!=""){
    $add_pizza_description = $_POST['add_pizza_description'];
    $add_pizza_price = $_POST['add_pizza_price'];
    if ($add_pizza_price > 0) {
      $sql = "INSERT INTO pizza (id, description, price) VALUES (NULL,'$add_pizza_description', '$add_pizza_price');";
      unset($_POST['add_pizza']);
      mysqli_query($conn, $sql) or die(mysqli_error($conn));
    }
  }
  if (isset($_POST['delete_pizza']) && isset($_POST['delete_pizza_id'])!=""){
    $delete_pizza_id = $_POST['delete_pizza_id'];
    $sql = "UPDATE pizza SET deleted='1' WHERE id='$delete_pizza_id'; ";
    unset($_POST['add_pizza']);
    mysqli_query($conn, $sql) or die(mysqli_error($conn));
  }
  if (isset($_POST['send_pizza']) && isset($_POST['send_pizza_value'])!="" && isset($_POST['send_pizza_id'])!=""){
    $pizza_value = $_POST['send_pizza_value'];
    $pizza_id = $_POST['send_pizza_id'];
    $date = date("d.m.Y h:i:s");
    if ($pizza_value == "W przygotowaniu") {
      $sql = "UPDATE pizza_history SET pizza_preparation = '$date' WHERE id = '$pizza_id';";
    } else if ($pizza_value == "W dostawie") {
      $sql = "UPDATE pizza_history SET pizza_delivery = '$date' WHERE id = '$pizza_id';";
    } else if ($pizza_value == "Odebrana") {
      $sql = "UPDATE pizza_history SET pizza_order = '$date' WHERE id = '$pizza_id';";
    }
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
    if (isset($_SESSION['username'])) {
      if (isset($_POST["message_send_admin"])!="" && isset($_POST["admin_support_text"])!="" && isset($_POST["support_id"])!="") {
        $textarea = $_POST["admin_support_text"];
        $message_id = $_POST["support_id"];
        $sql = "UPDATE messages SET addressee_user_id = (SELECT id FROM userdata WHERE username='$username'), addressee_text = '$textarea' WHERE id = '$message_id';";
        $result = $conn->query($sql);
      }
      if (isset($_POST["message_send"])!="" && isset($_POST["support_text"])!="") {
        $textarea = $_POST["support_text"];
        $sql = "INSERT INTO messages (id, sender_user_id, addressee_user_id, sender_text, addressee_text) VALUES (NULL,(SELECT id FROM userdata WHERE username='$username'), NULL, '$textarea', NULL);";
        $result = $conn->query($sql);
      }
      $username = $_SESSION['username'];
      if ($role==2){
        $sql_menu = "SELECT * FROM pizza;";
      } else {
        $sql_menu = "SELECT * FROM pizza WHERE deleted = '0';";
      }
    } else {
      $sql_menu = "SELECT * FROM pizza WHERE deleted = '0';";
    }
    $result = $conn->query($sql_menu);
    if ($result->num_rows > 0) {
      echo "<table><tr><th colspan=3>Dostępne pizze:</th></tr>";
      echo "<tr><th>Numer</th><th>Nazwa pizzy</th><th>Cena</th></tr>";
      while($row = $result->fetch_assoc()) {
        if ($row["deleted"] == '0'){
          echo "<tr><td>". $row["id"] ."</td><td>".$row["description"]."</td><td>". $row["price"] . "zł" ."</td></tr>";
        } else {
          echo "<tr><td class='deleted_pizza'>". $row["id"] ."</td><td class='deleted_pizza'>".$row["description"]."</td><td class='deleted_pizza'>". $row["price"] . "zł" ."</td></tr>";
        }
      }
      echo "</table>";
    }
    if (isset($_SESSION['username'])) {
      if ($role == 2){
        echo "<hr>
          <form action='index.php' method='post' class='display'>
          <h2>Dodaj pizzę do menu: </h2>
          <label id='text'>Podaj nazwę pizzy: </label>
          <input type='text' class='small' id='number' placeholder='Wpisz nazwę pizzy' name='add_pizza_description'>
          <label id='text'>Podaj cenę pizzy: </label>
          <input type='number' class='small' id='number' placeholder='Wpisz cenę pizzę' name='add_pizza_price'><br>
          <input type='submit' name='add_pizza' class='btn' id='sumbit' value='Wyślij'>
          </form>";
        echo "<hr>
          <form action='index.php' method='post' class='display'>
          <h2>Usuń pizzę z menu: </h2>
          <label id='text'>Podaj id pizzy: </label>
          <input type='number' class='small' id='number' placeholder='Wpisz id pizzy' name='delete_pizza_id'><br>
          <input type='submit' name='delete_pizza' class='btn' id='sumbit' value='Wyślij'>
        </form>";
      }
    }
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
      if ($role == 1){
        echo "<hr>
        <form action='index.php' method='post' class='display'>
        <h2>Kup pizzę: </h2>
        <label id='text' for='pizza_id'>Podaj numer pizzy: </label>
        <input id='number' class='small' min='1' type='number' name='pizza_number' placeholder='Wpisz numer pizzy'>
        <br>
        <input type='submit' name='order' class='btn' id='sumbit' value='Zamów'>
        </form>";
        echo "<hr>";
      }
    }
  if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    if ($role==1){
      $sql = "SELECT * FROM pizza_order WHERE user_id=(SELECT id FROM userdata WHERE username='$username');";
      $result = $conn->query($sql);
      if ($result->num_rows > 0) {
        $i = 0;
        $cost = 0;
        echo "<br><table><tr><th colspan=3>Historia zamówień:</th></tr>";
        echo "<tr><th>Numer zamówienia<br></th><th>Nazwa pizzy</th><th>Cena</th></tr>";
        while($row = $result->fetch_assoc()) {
          $i += 1;
          $cost += $row["price"];
          $ordered_pizza = $row["pizza_id"];
          $sql = "SELECT description FROM pizza WHERE id=$ordered_pizza;";
          $ordered_pizza = mysqli_fetch_array(mysqli_query($conn, $sql))[0];
          echo "<tr><td>". $i ."</td><td>" . $ordered_pizza . "</td><td>" . $row["price"] . "zł". "</td></tr>";
        }
        echo "</table>";
        echo "<br><p>Łączna suma kosztów: $cost zł</p>";
        //Tabela z historią zmian satusów pizzy
        echo "<hr>";
        $sql = "SELECT * FROM pizza_history WHERE user_id=(SELECT id FROM userdata WHERE username='$username');";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
          $i = 0;
          $cost = 0;
          echo "<br><table><tr><th colspan=4>Statusy pizzy:</th></tr>";
          echo "<tr><th>Numer zamówienia<br></th><th>W przygotowaniu</th><th>W dostawie</th><th>Dostarczono</th></tr>";
          while($row = $result->fetch_assoc()) {
            $i += 1;
            echo "<tr><td>". $i ."</td><td>" . $row["pizza_preparation"] . "</td><td>" . $row["pizza_delivery"] . "</td><td>" . $row["pizza_order"]. "</td></tr>";
          }
        }
      }
    } else if ($role==2 || $role==3){
      $sql = "SELECT * FROM pizza_order;";
      $result = $conn->query($sql);
      if ($result->num_rows > 0) {
        $i = 0;
        echo "<hr>";
        echo "<br><table><tr><th colspan=4>Historia zamówień:</th></tr>";
        echo "<tr><th>id<br></th><th>username</th><th>pizza</th></tr>";
        while($row = $result->fetch_assoc()) {
          $ordered_username = $row["user_id"];
          $sql = "SELECT username FROM userdata WHERE id=$ordered_username;";
          $ordered_username = mysqli_fetch_array(mysqli_query($conn, $sql))[0];
          $ordered_pizza = $row["pizza_id"];
          $sql = "SELECT description FROM pizza WHERE id=$ordered_pizza;";
          $ordered_pizza = mysqli_fetch_array(mysqli_query($conn, $sql))[0];
          echo "<tr><td>". $row["id"] ."</td><td>" . $ordered_username . "</td><td>" . $ordered_pizza . "</td></tr>";
        }
        echo "</table>";
        //Tabela z historią zmian satusów pizzy
        $sql = "SELECT * FROM pizza_history;";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
          $i = 0;
          $cost = 0;
          echo "<br><table><tr><th colspan=4>Statusy pizzy:</th></tr>";
          echo "<tr><th>Numer zamówienia<br></th><th>W przygotowaniu</th><th>W dostawie</th><th>Dostarczono</th></tr>";
          while($row = $result->fetch_assoc()) {
            $i += 1;
            echo "<tr><td>". $row["id"] ."</td><td>" . $row["pizza_preparation"] . "</td><td>" . $row["pizza_delivery"] . "</td><td>" . $row["pizza_order"]. "</td></tr>";
          }
        }
      }
    }
    if ($result->num_rows > 0 && ($role == 2 || $role == 3)) {
      echo "<hr>
      <form action='index.php' method='post' class='display'>
      <h2>Wyślij pizze: </h2>";
      if ($role == 2){
        echo 
        "<div><input type='radio' id='number' name='send_pizza_value' value='W przygotowaniu'><label for='send_pizza_value'>W przygotowaniu</label></div>";
      } else if ($role == 3) {
        echo 
        "<div><input type='radio' id='number' name='send_pizza_value' value='W dostawie'><label for='send_pizza_value'>W dostawie</label></div>
        <div><input type='radio' id='number' name='send_pizza_value' value='Odebrana'><label for='send_pizza_value'>Odebrana</label></div>";
      }
      echo
      "<br><input type='number' class='small' id='number' name='send_pizza_id' placeholder='Podaj id pizzy'><br>
      <input type='submit' name='send_pizza' class='btn' id='sumbit' value='Wyślij'>
      </form>";
      echo "<hr>";
    }
    if ($role == 1){
      echo "<form action='index.php' method='post' class='display'>
      <h2>Napisz wiadomość do support: </h2>
      <label id='number' for='support_text'>Treść wiadomości: </label>
      <textarea id='number' class='small textarea' name='support_text' placeholder='Wpisz treść'> </textarea>
      <br>
      <input type='submit' name='message_send' class='btn' id='sumbit' value='Wyślij'>
      </form>";
      $sql = "SELECT * FROM messages WHERE sender_user_id = (SELECT id FROM userdata WHERE username='$username');";
      $result = $conn->query($sql);
      if ($result->num_rows > 0) {
        echo "<hr>";
        $i = 0;
        $cost = 0;
        echo "<br><table><tr><th colspan=4>Wiadomości z support:</th></tr>";
        echo "<tr><th>Id<br></th><th>Treść</th><th>Odesłano</th></tr>";
        while($row = $result->fetch_assoc()) {
          $i += 1;
          echo "<tr><td>". $i ."</td><td>" . $row["sender_text"] . "</td><td>" . $row["addressee_text"] . "</td></tr>";
        }
      }
    }
    if ($role == 2){
      echo "<hr>
      <form action='index.php' method='post' class='display'>
      <h2>Odpisz na wiadomość: </h2>
      <label id='number' for='support_id'>Id wiadomości: </label>
      <input type='number' class='small' id='number' name='support_id' placeholder='Podaj id wiadomości'>
      <label id='number' for='admin_support_text'>Treść wiadomości: </label>
      <textarea id='number' class='small textarea' name='admin_support_text' placeholder='Wpisz treść'> </textarea>
      <br>
      <input type='submit' name='message_send_admin' class='btn' id='sumbit' value='Wyślij'>
      </form>";
      $sql = "SELECT * FROM messages;";
      $result = $conn->query($sql);
      if ($result->num_rows > 0) {
        echo "<hr>";
        $i = 0;
        $cost = 0;
        echo "<br><table><tr><th colspan=4>Wiadomości z support:</th></tr>";
        echo "<tr><th>Id<br></th><th>Treść</th><th>Odesłano</th></tr>";
        while($row = $result->fetch_assoc()) {
          $i += 1;
          echo "<tr><td>". $row["id"] ."</td><td>" . $row["sender_text"] . "</td><td>" . $row["addressee_text"] . "</td></tr>";
        }
      }
    }
    }
  ?>
  <container_alfa>
</body>
</html>
