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
    <style>
        .guyFormatted{ 
            margin-top: 15px;
        }
        table, td{
          border:1px solid black;
        }
        th{
          width: 33%;
          border:1px solid black;
        }
    </style>
  </head>

  <body>
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

        <?php
            // dynamically echo heading tabs based on session variable
            if(!isset($_SESSION['userid'])) {
                echo '<a style="float:right" href="login.php">Login/Sign Up</a>';
            } else {
                echo '<a style="float:right" href="logout.php">Logout</a>';
                echo '<a style="float:right" href="channel.php">My Channel</a>';
                echo '<a style="float:right" href="profile_update.php">My Profile</a>';
                echo '<a style="float:right" href="upload.php">Upload Video</a>';
                echo '<a style="float:right" href="favorite.php">Favorited</a>';
                echo '<a style="float:right" href="playlisthub.php">Playlist</a>';
                echo '<a style="float:right" href="messages.php">Messages</a>';
            }
        ?>

      </div>
      
      <!-- Div container for the left side pane and the right video pane (browse homepage) -->
      <div class="content-container">
        <!-- This will host the left navigation pane -->
        <div class="left-column">
          <ul class="navigation-list-left">
            <li><a href="categories.php" class="dropdown">Categories</a></li>
            <?php
              if(isset($_SESSION['userid'])){
                echo '<li><a href="userlist.php">Add Friends</a></li>';
                echo '<li><a href="userlistaccept.php">Friend Requests</a></li>';
                echo '<li><a href="friendlist.php">Current Friends</a></li>';
              }
            ?>
          </ul>
        </div>

          <!-- This will host the right video browsing pane -->
        <div class="right-column">
          <!-- Will be a PHP block here to generate the divs for each video -->
          <table id="userTable">
          <script>
            let screenWidth = screen.width;
          </script>
            <tr>
              <th>First Name</th>
              <th>Last Name</th>
              <th>Username</th>
          </tr>
          <tr>
              <td>
              <?php
                  $mysqli = openConnection();
                  $currentUser = $_SESSION['userid'];
                  $request_array = array();
                  $stmt = $mysqli->prepare("SELECT User_ID_One FROM Contacts WHERE User_ID_Two = '$currentUser' AND Status_App = 'Approved'");
                  if(!$stmt->execute()) { print("Error user information."); } else {
                    $stmt->bind_result($user_id);
                    while($stmt->fetch()) {
                        array_push($request_array, array($user_id));
                    }
                    $stmt->close();
                  }
                  $stmt = $mysqli->prepare("SELECT User_ID_Two FROM Contacts WHERE User_ID_One = '$currentUser' AND Status_App = 'Approved'");
                  if(!$stmt->execute()) { print("Error user information."); } else {
                    $stmt->bind_result($user_id);
                    while($stmt->fetch()) {
                        array_push($request_array, array($user_id));
                    }
                    $stmt->close();
                  }
                  $length = count($request_array);
                  echo '<div class="guyFormatted">';
                  for($i = 0; $i < $length; $i++){
                    $tempID = $request_array[$i][0];
                    $stmt = $mysqli->prepare("SELECT fname FROM User WHERE User_ID = '$tempID'");
                    if(!$stmt->execute()) { print("Error user information."); } else {
                      $stmt->bind_result($user_fname);
                      $stmt->fetch();
                      $stmt->close();
                    }
                    echo '<div class="guyFormatted"><p> '.$user_fname.'</p>';
                  }
            
                  echo '</td>';
                  echo '<td>';
                  echo '<div class="guyFormatted">';
                  for($i = 0; $i < $length; $i++){
                    $tempID = $request_array[$i][0];
                    $stmt = $mysqli->prepare("SELECT lname FROM User WHERE User_ID = '$tempID'");
                    if(!$stmt->execute()) { print("Error user information."); } else {
                      $stmt->bind_result($user_lname);
                      $stmt->fetch();
                      $stmt->close();
                    }
                    echo '<div class="guyFormatted"><p> '.$user_lname.'</p>';
                  }
              
                  echo '</td>';
                  echo '<td>';
                  echo '<div class="guyFormatted">';
                  for($i = 0; $i < $length; $i++){
                    $tempID = $request_array[$i][0];
                    $stmt = $mysqli->prepare("SELECT username FROM User WHERE User_ID = '$tempID'");
                    if(!$stmt->execute()) { print("Error user information."); } else {
                      $stmt->bind_result($user_username);
                      $stmt->fetch();
                      $stmt->close();
                    }
                    echo '<div class="guyFormatted"><p> '.$user_username.'</p>';
                  }
                  closeConnection($mysqli);
            
              ?>
              </td>
            </tr>
          </table>
        </div>
      </div>
    </main>
  </body>
</html>