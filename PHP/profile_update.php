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

    if(isset($_POST['createButton'])) {
        $username = $_POST['userBox'];
        $email = $_POST['emailBox'];
        $firstname = $_POST['fnameBox'];
        $lastname = $_POST['lnameBox'];
        $password = $_POST['passBox'];

        // ensuring that all fields have some type of data in them, not validating everything
        if(!empty($username) && !empty($email) && !empty($firstname) && !empty($lastname) && !empty($password)) {
            // check if username is using correct characters
            if(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST['userBox']))) {
                $errorMessage = "Username can only contain letters, numbers, and underscores.";
            } else {
                // update user information
                $stmt = "UPDATE User SET username='".$username."', fname='".$firstname."', lname='".$lastname."', email='".$email."', password='".$password."' WHERE User_ID = '".$_SESSION['userid']."'";
                        
                if(mysqli_query($mysqli, $stmt)) {
                    // success, user account created
                    print("User account updated successfully, redirecting to profile.");
                    header('Location: browse.php');
                } else {
                    //failure, user account not created
                    print("Error, user account not created.");
                    echo "ERROR: Could not able to execute $stmt. " . mysqli_error($mysqli);
                }
            }
        } else if(empty($username)) {
            // username box empty
            $errorMessage = "Please enter a username.";
        } else if(empty($email)) {
            // email box empty
            $errorMessage = "Please enter an email.";
        } else if(empty($firstname)) {
            // firstname box empty
            $errorMessage = "Please enter a first name.";
        } else if(empty($lastname)) {
            // lastname box empty
            $errorMessage = "Please enter a last name.";
        } else if(empty($password)) {
            // password box empty
            $errorMessage = "Please enter a password.";
        }

    }

    if(isset($_SESSION['userid'])) {
        $stmt = $mysqli->prepare("SELECT * FROM User WHERE User_ID = '".$_SESSION['userid']."'");
        if(!$stmt->execute()) { print("Error, unable to get user information."); } else {
            $stmt->bind_result($userid_entry, $firstname_entry, $lastname_entry, $username_entry, $email_entry, $password_entry);
            $stmt->fetch();
        }
        $stmt->close();
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
                    <p class="register-p">Profile</p>
                    <div class="register-username">
                        <label for="userBox">Username</label>
                        <input type="text" id="userBox" name="userBox" value="<?= $username_entry ?>" required/>
                    </div>
                    <div class="register-email">
                        <label for="emailBox">Email</label>
                        <input type="text" id="emailBox" name="emailBox" value="<?= $email_entry ?>" required/>
                    </div>
                    <div class="register-fname">
                        <label for="fnameBox">First name</label>
                        <input type="text" id="fnameBox" name="fnameBox" value="<?= $firstname_entry ?>" required/>
                    </div>
                    <div class="register-lname">
                        <label for="lnameBox">Last name</label>
                        <input type="text" id="lnameBox" name="lnameBox" value="<?= $lastname_entry ?>" required/>
                    </div>
                    <div class="register-password" id="passField">
                        <label for="passBox">Password</label>
                        <input type="password" id="passBox" name="passBox" value="<?= $password_entry ?>" required/>
                    </div>
                    <br />
                    <div class="login-wrapper">
                        <button class="regular-btn" type="submit" id="createButton" name="createButton">Update</button>
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