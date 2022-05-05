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
              <th>Add Friend</th>
          </tr>
          <tr>
              <td>
              <?php
                  $currentUserID = $_SESSION['userid'];
                  $mysqli = openConnection();

                  if (isset($_POST['firstUser']) && isset($_POST['secondUser'])) {
                    $first_userID = $_POST['firstUser'];
                    $second_userID = $_POST['secondUser'];
                    $stmt = $mysqli->prepare("SELECT MAX(Contact_ID) FROM Contacts");
                    if(!$stmt->execute()) { print("Error getting userid."); } else {
                        $stmt->bind_result($contactID_largest);
                        $stmt->fetch();
                        $stmt->close();

                        // incrementing max userid by 1
                        $new_contact_id = $contactID_largest + 1;
                    }
                    $stmt = "INSERT INTO Contacts (Contact_ID, User_ID_One, User_ID_Two, Status_App) VALUES ('$new_contact_id', '$first_userID', '$second_userID', 'Pending')";
                    mysqli_query($mysqli, $stmt);
                  }

                  $people_array = array();
                  $stmt = $mysqli->prepare("SELECT fname, lname, username, User_ID FROM User");
                  if(!$stmt->execute()) { print("Error user information."); } else {
                    $stmt->bind_result($user_fname, $user_lname, $user_username, $user_id);
                    while($stmt->fetch()) {
                        array_push($people_array, array($user_fname, $user_lname, $user_username, $user_id));
                    }
                    $stmt->close();
                  }
                  $length = count($people_array);
                  echo '<div class="guyFormatted">';
                  for ($i = 0; $i < $length; $i++) {
                    $tempUserID = $people_array[$i][3];
                    if($currentUserID != $tempUserID){
                      echo '<div class="guyFormatted"><p> '.$people_array[$i][0].'</p>';
                    } 
                  }
            
              echo '</td>';
              echo '<td>';
                  echo '<div class="guyFormatted">';
                  for ($i = 0; $i < $length; $i++) {
                    $tempUserID = $people_array[$i][3];
                    if($currentUserID != $tempUserID){
                      echo '<div class="guyFormatted"><p> '.$people_array[$i][1].'</p>';
                    } 
                  }

              
                echo '</td>';
                echo '<td>';
                 echo '<div class="guyFormatted">';
                 for ($i = 0; $i < $length; $i++) {
                  $tempUserID = $people_array[$i][3];
                  if($currentUserID != $tempUserID){
                    echo '<div class="guyFormatted"><p> '.$people_array[$i][2].'</p>';
                  } 
                 }
                
                echo '</td>';
                echo '<td>';
              
                if(isset($_SESSION['userid'])){

                  $people_array = array();
                   $stmt = $mysqli->prepare("SELECT User_ID FROM User");
                   if(!$stmt->execute()) { print("Error user information."); } else {
                     $stmt->bind_result($user_id);
                     while($stmt->fetch()) {
                         array_push($people_array, array($user_id));
                     }
                     $stmt->close();
                   }
                   $length = count($people_array);
                   echo '<div class="guyFormatted">';
                   for ($i = 0; $i < $length; $i++) {
                     $receiveUserID = $people_array[$i][0];
                     $stmt = $mysqli->prepare("SELECT Status_App FROM Contacts WHERE User_ID_One = '$currentUserID' AND User_ID_Two = '$receiveUserID'");
                     if(!$stmt->execute()) { print("Error user information."); } else {
                       $stmt->bind_result($app_status);
                       $stmt->fetch();
                       $stmt->close();
                     }
                     $stmt = $mysqli->prepare("SELECT Status_App FROM Contacts WHERE User_ID_One = '$receiveUserID' AND User_ID_Two = '$currentUserID'");
                     if(!$stmt->execute()) { print("Error user information."); } else {
                       $stmt->bind_result($check_app_status);
                       $stmt->fetch();
                       $stmt->close();
                     }
                     if($app_status == NULL && $check_app_status == NULL){
                      if($currentUserID != $receiveUserID)
                      {
                        echo '<div class="guyFormatted">
                        <form method="post" action="">
                          <input type="hidden" name="firstUser" value="'.$currentUserID.'">
                          <input type="hidden" name="secondUser" value="'.$receiveUserID.'">
                          <button type="submit" id="friendButton" name="friendButton">Send</button>
                        </form>';
                      }
                     }
                     if($app_status == 'Pending'){
                      if($currentUserID != $receiveUserID)
                      {
                        echo '<div class="guyFormatted"><p>Request Sent</p>';
                      }
                     }
                     if($check_app_status == 'Pending'){
                      if($currentUserID != $receiveUserID)
                      {
                        echo '<div class="guyFormatted"><p>Check Request</p>';
                      }
                     }
                     if($app_status == 'Approved'){
                      if($currentUserID != $receiveUserID)
                      {
                        echo '<div class="guyFormatted"><p>Current Friend</p>';
                      }
                     }
                     if($check_app_status == 'Approved'){
                      {
                        echo '<div class="guyFormatted"><p>Current Friend</p>';
                      }
                     }
                   }
                   closeConnection($mysqli);
                }
              ?>
              </td>
            </tr>
          </table>
        </div>
      </div>
    </main>
  </body>
</html>