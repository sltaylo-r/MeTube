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
                <li><a class="active" href="new_message.php">New Message</a></li>
                <li><a href="messages.php">Inbox</a></li>
                <li><a href="messages_sent.php">Sent</a></li>
            </ul>
          </div>

          <!-- This will host the right video browsing pane -->
          <div class="right-column">

            <?php

                $mysqli = openConnection();

                if(isset($_SESSION['userid'])) {

                    $userid = $_SESSION['userid'];
                    $contactid_array = array();
                    $username_array = array();

                    // getting all userid's of approved contacts
                    $stmt = $mysqli->prepare("SELECT User_ID_One, User_ID_Two FROM Contacts WHERE User_ID_One = '$userid' || User_ID_Two = '$userid' && Status_App = 'Approved'");
                    if(!$stmt->execute()) { print("Error getting contact information."); } else {
                        $stmt->bind_result($userid1_entry, $userid2_entry);
                        while($stmt->fetch()) {
                            if($userid1_entry == $userid) {
                                array_push($contactid_array, $userid2_entry);
                            } else {
                                array_push($contactid_array, $userid1_entry);
                            }
                        }
                        $stmt->close();
                    }

                    // getting usernames of all approved contacts
                    for($i = 0; $i < count($contactid_array); $i++) {
                        $stmt = $mysqli->prepare("SELECT username FROM User WHERE User_ID = '$contactid_array[$i]'");
                        if(!$stmt->execute()) { print("Error getting username."); } else {
                            $stmt->bind_result($username_entry);
                            $stmt->fetch();
                            $stmt->close();

                            array_push($username_array, $username_entry);
                        }
                    }
                }

                closeConnection($mysqli);

                ?>

                <form action="messages.php" class="message-form" method="POST">
                    <label for="receiveID">Select a recipient:</label></br>
                    <select id="receiveID" name="receiveID" required>
                        <?php
                            for($i = 0; $i < count($username_array); $i++) {
                                printf("<option value=\"%s\">%s</option>", $contactid_array[$i], $username_array[$i]);
                            }
                        ?>
                    </select>
                    </br>
                    </br>

                    <label for="messageBody">Message:</label></br>
                    <textarea class="messages-textarea" id="messageBody" name="messageBody"></textarea>
                    </br>
                    <input type="hidden" name="sendID" value="<?php echo $_SESSION['userid'] ?>">
                    <input type="hidden" name="replyID" value="0">
                    <button class="regular-btn" type="submit" id="sendButton" name="sendButton">Send</button>
                </form>

            </div>            
          </div>
        </div>
    </main>

  </body>

</html>
