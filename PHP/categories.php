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
                <li><a href="categories.php">Categories</a></li>
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

            <div class="wrapper">
                <ul class="navigation-list-left">
                    <?php
                        $mysqli = openConnection();

                        $stmt = $mysqli->prepare("SELECT DISTINCT Category FROM Categories");
                        if(!$stmt->execute()) { print("Error, unable to fetch categories."); } else {
                        $stmt->bind_result($category);
                        while($stmt->fetch()) {

                            echo "<li><a href='browse_category.php?varname=$category'>$category</a></li>";
                            
                        }
                        $stmt->close();
                        }

                        closeConnection($mysqli);
                    ?>
                </ul>
                
            </div>
          </div>
        </div>
    </main>

  </body>

</html>
