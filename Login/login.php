<?php
require 'login_auth.php';

session_start(); //issue a cookie

$message = '';

if (isset($_POST['name']) && isset($_POST['password'])) {
    $db = new mysqli(
      MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE);
    $sql = sprintf("SELECT * FROM users WHERE name='%s'",
           $db->real_escape_string($_POST['name']));

    $result = $db->query($sql);
    $row = $result->fetch_object();

    if ($row != null) {
      $hash = $row->hash;
      if (password_verify($_POST['password'], $hash)) {
        $message = 'Login successful.';

        $_SESSION['username'] = $row->name;
        $_SESSION['isAdmin'] = $row->isAdmin;
      } else {
        $message = 'Login failed.';
      }
    } else {
      $message = 'Login failed.';
    }
    
    $db->close();
}


?>
<?php
readfile('login_form.html');

echo "<div class='text-info'>$message</div>";
?>

<?php
//readfile('footer.tmpl.html');
?>