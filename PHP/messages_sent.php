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

  <?php
    $mysqli = openConnection();

    if(isset($_POST['receiveID'])) {
        if(isset($_POST['sendID'])) {
            if(isset($_POST['replyID'])) {
                if(!empty($_POST['messageBody'])) {
                    // all conditions met, push data to conversations table
                    $receiveID = $_POST['receiveID'];
                    $sendID = $_POST['sendID'];
                    $replyID = $_POST['replyID'];
                    $message = $_POST['messageBody'];
                    $date = date("Y-m-d");
                    $time = date("H:i:s");
                    $curr_time = $date . ' ' . $time;
                    
                    $stmt = $mysqli->prepare("SELECT MAX(Conv_ID) FROM Conversation");
                    if(!$stmt->execute()) { print("Error getting max Conv_ID"); } else {
                        $stmt->bind_result($convid);
                        $stmt->fetch();
                        $stmt->close();

                        $convid++;
                    }

                    $stmt = "INSERT INTO Conversation (Conv_ID, User_ID_Send, User_ID_Receive, Reply_ID, Message, Timestamp) VALUES('$convid', '$sendID', '$receiveID', '$replyID', '$message', '$curr_time')";

                    if(mysqli_query($mysqli, $stmt)) {
                        // success, message sent
                        print("Message sent successfully.");
                    } else {
                        // failure, message not sent
                        print("Error, message was not sent.");
                        echo "ERROR: Could not able to execute $stmt. " . mysqli_error($mysqli);
                    }
                }
            }
        }
    }

    closeConnection($mysqli);
  ?>

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
                <li><a class="active" href="messages_sent.php">Sent</a></li>
            </ul>
          </div>

          <!-- This will host the right video browsing pane -->
          <div class="right-column">

            <div class="messages-container">
                <table id="messageTable">
                    <tr class="header">
                        <th id="date" style="width:15%"><button class="sort-button" type="button" onclick="sortRows(0)">Date</button></th>
                        <th id="username" style="width:20%"><button class="sort-button" type="button" onclick="sortRows(1)">Sent To</button></th>
                        <th id="message" style="width:45%"><button class="sort-button" type="button" onclick="sortRows(2)">Message</button></th>
                        <th style="width:10%"><label>Action</label></th>
                    </tr>

                    <!--
                    <tr>
                        <form method="get" action="message_show.php" id="conversation-form">
                            <input type="hidden" name="receiveID" value='.$userid_send_entry.'>
                            <input type="hidden" name="convID" value='.$convid_entry.'>
                            <td>
                                <label>.'$timestamp_entry.'</label>
                            </td>
                            <td>
                                <label>'.$username_entry.'</label>
                            </td>
                            <td>
                                <label>'.$message_entry.'</label>
                            </td>
                            <td>
                                <button type="submit">Open</button>
                            </td>
                        </form>
                    </tr>
                    -->

                    <?php
                        $mysqli = openConnection();

                        $userid = $_SESSION['userid'];

                        $stmt = $mysqli->prepare("SELECT Conv_ID, username, User_ID_Send, Message, Timestamp FROM Conversation LEFT JOIN User on Conversation.User_ID_Receive = User.User_ID WHERE User_ID_Send = '$userid' ORDER BY Timestamp DESC;");
                        if(!$stmt->execute()) { print("Unable to get conversation information."); } else {
                            $stmt->bind_result($convid_entry, $username_entry, $userid_send_entry, $message_entry, $timestamp_entry);
                            while($stmt->fetch()) {
                                // creating forms based on messages that are in inbox

                                echo '<tr>
                                        <form method="post" action="message_show.php" id="conversation-form">
                                            <input type="hidden" name="receiveID" value='.$userid_send_entry.'>
                                            <input type="hidden" name="convID" value='.$convid_entry.'>
                                            <input type="hidden" name="sentView" value="1">
                                            <td>
                                                <label>'.$timestamp_entry.'</label>
                                            </td>
                                            <td>
                                                <label>'.$username_entry.'</label>
                                            </td>
                                            <td>
                                                <label>'.$message_entry.'</label>
                                            </td>
                                            <td>
                                                <button type="submit">Open</button>
                                            </td>
                                        </form>
                                    </tr>';
                            }
                            $stmt->close();
                        }
                    ?>
                </table>

                <!-- Second right column, this is for displaying the messages -->
                <!--
                <div class="right-column">
                    <iframe name="conversation_iframe" src="message_show.php" id="conversation_iframe" width="100%" height="100%">
                    </iframe>
                </div>
                -->

            </div>            
          </div>
        </div>

        <script>
            function sortRows(num) {
                // variable declarations
                var table = document.getElementById("messageTable");
                var switching = true;
                var rows, i, x, y, shouldSwitch, dir, switchcount = 0;

                // Set the sorting direction to ascending:
                dir = "ascending";
                
                // while sorting (switching values)
                while (switching) {
                    // start in ascending order
                    switching = false;
                    rows = table.rows;
                    
                    for (i = 1; i < (rows.length - 1); i++) {
                        // don't switch, stay in ascending for now
                        shouldSwitch = false;
                        
                        // elements to compare
                        x = rows[i].getElementsByTagName('td')[num];
                        y = rows[i + 1].getElementsByTagName('td')[num];
                        
                        // comparing selected elements if ascending
                        if (dir == "ascending") {
                            if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                                // If so, mark as a switch and break the loop:
                                shouldSwitch = true;
                                break;
                            }
                            // else compare as descending
                        } else if (dir == "descending") {
                            if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                                // If so, mark as a switch and break the loop:
                                shouldSwitch = true;
                                break;
                            }
                        }
                    }
                    // checking if the switch has been done and if it should be
                    // marked to switch under the next sort
                    if (shouldSwitch) {
                        rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                        switching = true;
                        
                        // increment to keep track of switches
                        switchcount ++;
                    } else {
                    
                        // if it hasn't been switched but is already in ascending order,
                        // perform the switch and sort
                        if (switchcount == 0 && dir == "ascending") {
                            dir = "descending";
                            switching = true;
                        }
                    }
                }
            }
        </script>
    </main>

  </body>

</html>
