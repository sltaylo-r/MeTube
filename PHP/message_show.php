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
                <li><a href="new_message.php">New Message</a></li>
                <li><a href="messages.php">Inbox</a></li>
                <li><a href="messages_sent.php">Sent</a></li>
            </ul>
          </div>

          <!-- This will host the right video browsing pane -->
          <div class="right-column">

            <?php

                $mysqli = openConnection();

                if(isset($_POST['receiveID'])) {
                    if(isset($_POST['convID'])) {
                        $mysqli = openConnection();

                        $convID = $_POST['convID'];
                        $message_array = array();

                        // getting information about the message
                        $stmt = $mysqli->prepare("SELECT Conv_ID, username, User_ID_Send, Message, Reply_ID, Timestamp FROM Conversation LEFT JOIN User on Conversation.User_ID_Send = User.User_ID WHERE Conv_ID = '$convID'");
                        // $stmt = $mysqli->prepare("SELECT * FROM Conversation WHERE Conv_ID = '$convID'");
                        if(!$stmt->execute()) { print("Error getting conversation details."); } else {
                            $stmt->bind_result($convid_entry, $username_entry, $userid_send_entry, $message_entry, $replyid_entry, $timestamp_entry);
                            $stmt->fetch();
                            $stmt->close();

                            array_push($message_array, array($convid_entry, $username_entry, $userid_send_entry, $message_entry, $replyid_entry, $timestamp_entry));

                            // checking if message has any replies (0 means it is not a reply to anything)
                            if($replyid_entry != 0) {
                                // array to hold replies

                                // while the message is a reply (tracing back to start), get next message
                                while($replyid_entry != 0) {
                                    $stmt = $mysqli->prepare("SELECT Conv_ID, username, User_ID_Send, Message, Reply_ID, Timestamp FROM Conversation LEFT JOIN User on Conversation.User_ID_Send = User.User_ID WHERE Conv_ID = '$replyid_entry'");
                                    if(!$stmt->execute()) { print("Error getting reply details."); } else {
                                        $stmt->bind_result($convid_replyentry, $username_replyentry, $userid_send_replyentry, $message_replyentry, $replyid_replyentry, $timestamp_replyentry);
                                        $stmt->fetch();
                                        $stmt->close();

                                        // if it is ever null, this will close the while loop
                                        $replyid_entry = $replyid_replyentry;

                                        // pushing details to array
                                        array_push($message_array, array($convid_replyentry, $username_replyentry, $userid_send_replyentry, $message_replyentry, $replyid_replyentry, $timestamp_replyentry));
                                    }
                                }

                                for($i = 0; $i < count($message_array); $i++) {
                                    if($message_array[$i][0] != 0) {
                                        // echo the contents of the array
                                        echo '<h2>Username: '.$message_array[$i][1].'</h2></br>
                                                <p>Date: '.$message_array[$i][5].'</p></br>
                                                <p>'.$message_array[$i][3].'</p></br>
                                                </br>
                                                </br>';
                                    }
                                }
                            } else {
                              echo '<h2>Username: '.$username_entry.'</h2></br>
                                      <p>Date: '.$timestamp_entry.'</p></br>
                                      <p>'.$message_entry.'</p></br>
                                      </br>
                                      </br>';
                            }
                        }
                    }
                }

                closeConnection($mysqli);

                if($_POST['sentView'] == '1') {
                  // do nothing
                } else {
                  echo '<form action="messages.php" method="POST" id="replyForm">
                    <p>Message Body:</p>
                    <textarea id="messageBody" name="messageBody"></textarea>
                    </br>
                    <input type="hidden" name="receiveID" value="'.$_POST['receiveID'].'">
                    <input type="hidden" name="sendID" value="'.$_SESSION['userid'].'">
                    <input type="hidden" name="replyID" value="'.$convid_entry.'">
                    <button class="regular-btn" type="submit" id="sendButton" name="sendButton">Send</button>
                  </form>';
                }

                ?>

                <!--
                <form action="messages.php" method="POST" id="replyForm">
                  <p>Message Body:</p>
                  <textarea id="messageBody" name="messageBody"></textarea>
                  </br>
                  <input type="hidden" name="receiveID" value="'.$_GET['receiveID'].'">
                  <input type="hidden" name="sendID" value="'.$_SESSION['userid'].'">
                  <button class="regular-btn" type="submit" id="sendButton" name="sendButton">Send</button>
                </form>
              -->

            </div>            
          </div>
        </div>
    </main>

  </body>

</html>
