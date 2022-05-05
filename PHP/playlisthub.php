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
              <th>Playlist Name</th>
              <th>View Playlist</th>
              <th>Delete Playlist</th>
          </tr>
          <tr>
              <td>
              <?php
                $mysqli = openConnection();
                if (isset($_POST['remove'])){
                    $listName = $_POST['remove'];
                    $te = $_SESSION['userid'];
                    $stmt = $mysqli->prepare("DELETE FROM Playlist WHERE User_ID ='$te' AND Listname = '$listName'");
                    $stmt->execute();
                }
                  $currentUserID = $_SESSION['userid'];

                  $list_array = array();
                  $stmt = $mysqli->prepare("SELECT Listname FROM Playlist WHERE User_ID = '$currentUserID'");
                  if(!$stmt->execute()) { print("Error user information."); } else {
                    $stmt->bind_result($listName);
                    while($stmt->fetch()) {
                        $duplicate = 0;
                        $length = count($list_array);
                        for($i = 0; $i < $length; $i++){
                            if($listName == $list_array[$i][0]){
                                $duplicate = 1;
                            }
                        }
                        if($duplicate == 0){
                            array_push($list_array, array($listName));
                        }
                    }
                    $stmt->close();
                  }
                  
                  $length = count($list_array);
                  echo '<div class="guyFormatted">';
                  for ($i = 0; $i < $length; $i++) {
                    echo '<div class="guyFormatted"><p> '.$list_array[$i][0].'</p>';
                    
                  }
            
              echo '</td>';
              echo '<td>';
                  echo '<div class="guyFormatted">';
                  for ($i = 0; $i < $length; $i++) {
    
                    $currentListName = $list_array[$i][0];
                    echo '<div class="guyFormatted">
                    <form method="get" action="playlist.php">
                        <input type="hidden" name="listName" value="'.$currentListName.'">
                        <button type="submit" id="viewButton" name="viewButton">View</button>
                    </form>';
                    
                  }

                echo '</td>';
                echo '<td>';
                    echo '<div class="guyFormatted">';
                    for ($i = 0; $i < $length; $i++) {

                    $currentListName = $list_array[$i][0];
                    echo '<div class="guyFormatted">
                    <form method="post" action="">
                        <input type="hidden" name="remove" value="'.$currentListName.'">
                        <button type="submit" id="removeButton" name="removeButton">Remove</button>
                    </form>';
                    
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