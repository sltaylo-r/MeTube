<!DOCTYPE html>
<html lang="en">

<?php 
    include 'inc/bootstrap.php';
    include 'db_connection.php';
?>

  <head>
    <meta charset="UTF-8" />
    <link rel="icon" type="image/svg+xml" href="favicon.svg" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MeTube</title>
    <link rel="stylesheet" href="CSS/index.css">
  </head>

  <body>

  <?php

    $mysqli = OpenConnection();

    if(isset($_POST['loginButton'])) {
        $username = $_POST['userBox'];
        $password = $_POST['passBox'];

        if(!empty($username) && !empty($password)) {
            $stmt = $mysqli->prepare("SELECT username, email, User_ID, password FROM User WHERE username = '".$username."'");
            if(!$stmt->execute()) { print("Error getting user data from table."); } else {
                $stmt->bind_result($username_entry, $email_entry, $userid_entry, $pwd_entry);
                $stmt->fetch();

                // IF user is not found, print error
                // ELSE IF password does not match password on record, print error
                // ELSE update session variables and route to homepage
                if($username_entry == null) {
                    $errorMessage = "Username not found";
                } else if (!$password == $pwd_entry) {
                    $errorMessage = "Password is incorrect";
                } else {
                    $_SESSION['userid'] = $userid_entry;
                    $_SESSION['username'] = $username_entry;
                    $_SESSION['email'] = $email_entry;                
                    header('Location: browse.php');
                    die;
                }

                $stmt->close();
            }
        }
    }

    closeConnection($mysqli);
  ?>

    <main>

      <!-- Main navigation bar at the top of all pages -->
      <div class="topnav">
        <a style="background-color: #2196F3" href="browse.php">MeTube</a>
        <div class="search-container">
          <!-- Check my PHP files for how I did the report generation -->
          <form action="search.php" id="searchForm" method="GET">
            <input type="text" placeholder="Search.." name="searchBox" id="searchBox" required>
            <button type="submit">Search</button>
          </form>
        </div>
      </div>

      <!-- Div container for the left side pane and the right video pane (browse homepage) -->
      <div class="content-container">
          <!-- Login area -->

          <div class="login-box">
            <form class="login-form" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <fieldset id="fieldBox">
                    <p class="login-p">Login</p>
                    <div class="login-username">
                        <input type="text" id="userBox" name="userBox" placeholder="Username" required/>
                    </div>
                    <div class="login-password" id="passField">
                        <input type="password" id="passBox" name="passBox" placeholder="Password" required/>
                    </div>
                    <br />
                    <div class="login-wrapper">
                        <button class="regular-btn" type="submit" id="loginButton" name="loginButton">Login</button>
                        <a href="register.php"><button class="regular-btn" type="button" id="registerLinkButton" name="registerButton">Register</button></a>
                    </div>
                    <br />
                </fieldset>
            </form>
            <?= $errorMessage ?? '' ?>
          </div>
      </div>

    </main>

  </body>

</html>