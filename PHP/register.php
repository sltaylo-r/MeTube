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
    // opening database connection
    $mysqli = OpenConnection();

    if(isset($_POST['createButton'])) {
        $username = $_POST['userBox'];
        $email = $_POST['emailBox'];
        $firstname = $_POST['fnameBox'];
        $lastname = $_POST['lnameBox'];
        $password = $_POST['passBox'];
        $password_confirm = $_POST['passBoxConfirm'];

        // ensuring that all fields have some type of data in them, not validating everything
        if(!empty($username) && !empty($email) && !empty($firstname) && !empty($lastname) && !empty($password) && !empty($password_confirm)) {
            // check if username is using correct characters
            if(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST['userBox']))) {
                $errorMessage = "Username can only contain letters, numbers, and underscores.";
            } else {
                $stmt = $mysqli->prepare("SELECT User_ID FROM User WHERE username = '".$username."'");
                if(!$stmt->execute()) { print("Error getting userid."); } else {
                    $stmt->bind_result($userid_entry);
                    $stmt->fetch();
                    $stmt->close();

                    if($userid_entry !== null) {
                        // there was a match in the database for the username entry
                        // print error
                        $errorMessage = "Username already exists.";
                    } else {
                        // there was no match in the database for the username entry
                        // process registration
                        $stmt = $mysqli->prepare("SELECT MAX(User_ID) FROM User");
                        if(!$stmt->execute()) { print("Error getting userid."); } else {
                            $stmt->bind_result($userid_largest);
                            $stmt->fetch();
                            $stmt->close();

                            // incrementing max userid by 1
                            $new_userid = $userid_largest + 1;
                        }

                        // checking that passwords match
                        if($password == $password_confirm) {
                            // SQL statement to insert new user data into User table
                            $stmt = "INSERT INTO User (User_ID, fname, lname, username, email, password) VALUES ('$new_userid', '$firstname', '$lastname', '$username', '$email', '$password')";
                            
                            if(mysqli_query($mysqli, $stmt)) {
                                // success, user account created
                                print("User account created successfully, redirecting to login.");
                                header('Location: login.php');
                            } else {
                                // failure, user account not created
                                print("Error, user account not created.");
                                echo "ERROR: Could not able to execute $stmt. " . mysqli_error($mysqli);
                            }
                        } else {
                            $errorMessage = "Passwords do not match.";
                        }
                    }
                }
            }
        } else if(empty($_POST['userBox'])) {
            // username box empty
            $errorMessage = "Please enter a username.";
        } else if(empty($_POST['emailBox'])) {
            // email box empty
            $errorMessage = "Please enter an email.";
        } else if(empty($_POST['fnameBox'])) {
            // firstname box empty
            $errorMessage = "Please enter a first name.";
        } else if(empty($_POST['lnameBox'])) {
            // lastname box empty
            $errorMessage = "Please enter a last name.";
        } else if(empty($_POST['passBox'])) {
            // password box empty
            $errorMessage = "Please enter a password.";
        } else if(empty($_POST['passBoxConfirm'])) {
            // password box empty
            $errorMessage = "Please confirm your password.";
        }

    }

    // closing database connection
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

          <div class="register-box">
            <form class="register-form" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <fieldset id="fieldBox">
                    <p class="register-p">Register</p>
                    <div class="register-username">
                        <input type="text" id="userBox" name="userBox" placeholder="Username" required/>
                    </div>
                    <div class="register-email">
                        <input type="text" id="emailBox" name="emailBox" placeholder="Email" required/>
                    </div>
                    <div class="register-fname">
                        <input type="text" id="fnameBox" name="fnameBox" placeholder="First name" required/>
                    </div>
                    <div class="register-lname">
                        <input type="text" id="lnameBox" name="lnameBox" placeholder="Last name" required/>
                    </div>
                    <div class="register-password" id="passField">
                        <input type="password" id="passBox" name="passBox" placeholder="Password" required/>
                    </div>
                    <div class="register-password" id="passField">
                        <input type="password" id="passBoxConfirm" name="passBoxConfirm" placeholder="Confirm Password" required/>
                    </div>
                    <br />
                    <div class="login-wrapper">
                        <button class="regular-btn" type="submit" id="createButton" name="createButton">Register</button>
                        <a href="/public/HTML/login.php"><button class="regular-btn" type="button" id="loginLinkButton" name="registerButton">Login</button></a>
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