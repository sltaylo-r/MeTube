
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
        .viewDiv { 
            text-align: center;
            height: 70%;
        }
        .videoInfo {
            height: 10%;
            width: 65%;
            padding-left: 10%;
            display: block;
            position: absolute;
        }
        .video {
            display: block;
            margin: auto;
            padding-top: 15px;
        }
        .videoTitle {
            font-size: 150%;
            font-family: sans-serif;
            float: left;
        }

        .profileNameButton {
            background-color: #008CBA;
            color: white;
            /* margin-top:15px; */
            font-size: 30px;
            transition-duration: 0.4s;
            border: none;
            float: right;
            position: relative;
            left: 475%;
        }
        .profileNameButton:hover{
            background-color: #4CAF50;
            color: white;
            cursor: pointer;

        }

        .profileName{
            float: right;
            padding-right: 200px;
        }
        .subDiv{
            position: absolute;
            left:75%;
            top:110%;
        }

        .subcommentsButton{
            background-color: #008CBA;
            color: white;
            margin-left: 150px;
            font-size: 18px;
            transition-duration: 0.4s;
            border: none;
        }
        .subcommentsButton:hover{
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }
        .subcomments{
            margin-left: 170px;
            font-size: 20px;
        }
        .subform{
            margin-left: 150px;
            font-size: 20px;
            padding-bottom:10px;
        }
        .commentsButton{
            background-color: #008CBA;
            color: white;
            margin-left: 50px;
            font-size: 18px;
            transition-duration: 0.4s;
            border: none;
        }
        .commentsButton:hover{
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }
        .comments{
            margin-left: 70px;
            font-size: 20px;
        }
        .form{
            margin-left: 50px;
            font-size: 20px;
            padding-bottom: 10px;
            padding-top: 20px;
        }
        .commentsDiv{
            padding-top: 15px;
        }
        .spacer{
            padding:100px;
        }
        .playlistDiv{
            left: 70%;
            position: absolute;
            top: 90%;
        }
        .removePlaylistDiv{
            left: 70%;
            position: absolute;
            top: 95%;
        }
        .starDiv{
            left: 80%;
            position: absolute;
            top: 100%;
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

            <div class="wrapper">
                <?php

                    $mysqli = openConnection();
                    
                    $var_value = $_GET['varname'];
                    $var_value = intval($var_value);
                    if (isset($_POST['remove'])){
                        $listName = $_POST['remove'];
                        $te = $_SESSION['userid'];
                        $stmt = $mysqli->prepare("DELETE FROM Playlist WHERE Video_ID = '$var_value' AND User_ID ='$te' AND Listname = '$listName'");
                        $stmt->execute();
                    }
                    if (isset($_POST['playlistName'])) {
                        $listName = $_POST['playlistName'];
                        $tempUserID = $_SESSION['userid'];
                        $playlistVidID = array();
                        $newVidInList = 0;
                        $stmt = $mysqli->prepare("SELECT Video_ID FROM Playlist WHERE Listname = '$listName' AND User_ID = '$tempUserID'");
                        if(!$stmt->execute()) { print("Error getting userid."); } else {
                            $stmt->bind_result($vidID);
                            while($stmt->fetch()) {
                                array_push($playlistVidID, array($vidID));
                            }
                            $stmt->close();

                            // incrementing max userid by 1
                        }
                        $length = count($playlistVidID);
                        for($i = 0; $i < $length; $i++){
                            if($playlistVidID[$i][0] == $var_value){
                                $newVidInList = 1;
                            }
                        }
                        if($newVidInList == 0){
                            $tempUserID = $_SESSION['userid'];
                            $stmt = $mysqli->prepare("SELECT MAX(Playlist_ID) FROM Playlist");
                            if(!$stmt->execute()) { print("Error getting id."); } else {
                                $stmt->bind_result($listID_largest);
                                $stmt->fetch();
                                $stmt->close();
    
                                // incrementing max userid by 1
                                $new_list_id = $listID_largest + 1;
                            }
                            $stmt = "INSERT INTO Playlist (Playlist_ID, User_ID, Video_ID, Listname) VALUES ('$new_list_id', '$tempUserID', '$var_value', '$listName')";
                            mysqli_query($mysqli, $stmt);
                        }
                    }
                    if (isset($_POST['id_user']) && isset($_POST['commentBox'])) {
                        $comment_userID = $_POST['id_user'];
                        $comment_data = $_POST['commentBox'];
                        $stmt = $mysqli->prepare("SELECT MAX(Comment_ID) FROM Comments");
                        if(!$stmt->execute()) { print("Error getting userid."); } else {
                            $stmt->bind_result($commentID_largest);
                            $stmt->fetch();
                            $stmt->close();

                            // incrementing max userid by 1
                            $new_comment_id = $commentID_largest + 1;
                        }
                        $stmt = "INSERT INTO Comments (Video_ID, User_ID, Comment, Comment_ID) VALUES ('$var_value', '$comment_userID', '$comment_data', '$new_comment_id')";
                        mysqli_query($mysqli, $stmt);
                    }
                    if (isset($_POST['sub_id_user']) && isset($_POST['sub_commentBox']) && isset($_POST['above_commentID'])) {
                        $sub_comment_userID = $_POST['sub_id_user'];
                        $sub_comment_data = $_POST['sub_commentBox'];
                        $aboveID = $_POST['above_commentID'];
                        $stmt = $mysqli->prepare("SELECT MAX(Comment_ID) FROM SubComments");
                        if(!$stmt->execute()) { print("Error getting userid."); } else {
                            $stmt->bind_result($sub_commentID_largest);
                            $stmt->fetch();
                            $stmt->close();

                            // incrementing max userid by 1
                            $new_sub_comment_id = $sub_commentID_largest + 1;
                        }
                        $stmt = "INSERT INTO SubComments (Comment_ID, Above_Comment_ID, Video_ID, User_ID, Comment) VALUES ('$new_sub_comment_id', '$aboveID', '$var_value', '$sub_comment_userID', '$sub_comment_data')";
                        mysqli_query($mysqli, $stmt);
                    }
                    if (isset($_POST['favorite'])) {
                        $stmt = $mysqli->prepare("SELECT MAX(Favorite_List_ID) FROM Favorite_List");
                        $te = $_SESSION['userid'];
                        if(!$stmt->execute()) { print("Error getting userid."); } else {
                            $stmt->bind_result($favoriteID_largest);
                            $stmt->fetch();
                            $stmt->close();

                            // incrementing max userid by 1
                            $new_fav_comment_id = $favoriteID_largest + 1;
                        }
                        $stmt = "INSERT INTO Favorite_List (Favorite_List_ID, User_ID, Video_ID) VALUES ('$new_fav_comment_id', '$te', '$var_value')";
                        mysqli_query($mysqli, $stmt);
                    }
                    if (isset($_POST['unfavorite'])) {
                        $te = $_SESSION['userid'];
                        $stmt = $mysqli->prepare("DELETE FROM Favorite_List WHERE Video_ID = '$var_value' AND User_ID ='$te'");
                        $stmt->execute();
                    }
                    if (isset($_POST['unsubbed'])) {
                        $stmt = $mysqli->prepare("SELECT User_ID FROM Video WHERE Video_ID = '$var_value'");
                        $te = $_SESSION['userid'];
                        if(!$stmt->execute()) { print("Error getting userid."); } else {
                            $stmt->bind_result($useID);
                            $stmt->fetch();
                            $stmt->close();

                            // incrementing max userid by 1
                        }
                        $stmt = $mysqli->prepare("SELECT MAX(Subscribe_ID) FROM Subscribed");
                        $te = $_SESSION['userid'];
                        if(!$stmt->execute()) { print("Error getting userid."); } else {
                            $stmt->bind_result($subscribedID_largest);
                            $stmt->fetch();
                            $stmt->close();

                            // incrementing max userid by 1
                            $new_sub_id = $subscribedID_largest + 1;
                        }
                        $stmt = "INSERT INTO Subscribed (Subscribe_ID, User_ID, Sub_User_ID) VALUES ('$new_sub_id', '$te', '$useID')";
                        mysqli_query($mysqli, $stmt);
                    }
                    if (isset($_POST['subbed'])) {
                        $stmt = $mysqli->prepare("SELECT User_ID FROM Video WHERE Video_ID = '$var_value'");
                        if(!$stmt->execute()) { print("Error getting userid."); } else {
                            $stmt->bind_result($useID);
                            $stmt->fetch();
                            $stmt->close();
                        }
                        $te = $_SESSION['userid'];
                        $stmt = $mysqli->prepare("DELETE FROM Subscribed WHERE Sub_User_ID = '$useID' AND User_ID ='$te'");
                        $stmt->execute();
                    }
                    $stmt = $mysqli->prepare("SELECT Video_Name, Video_Filename, Video_Description, User_ID, Video_Size, Timestamp FROM Video WHERE Video_ID = '$var_value'");
                    if(!$stmt->execute()) { print("Error video information."); } else {
                        $stmt->bind_result($video_title, $video_filename, $video_description, $userid, $video_size, $timestamp);
                        $stmt->fetch();
                        $stmt->close();
                    }
                    $stmt = $mysqli->prepare("SELECT username FROM User WHERE User_ID = '$userid'");
                    if(!$stmt->execute()) { print("Error video information."); } else {
                        $stmt->bind_result($username);
                        $stmt->fetch();
                        $stmt->close();
                    }

                    // video size in MB
                    $video_size = $video_size / 1024000;

                    /*
                    <div id="videoInfo" class="videoInfo">
                            <p class="videoTitle">
                                <b>'.$video_title.'</b>
                            </p>
                            <form method="get" action="userchannel.php">
                                <input type="hidden" name="varname" value="'.$userid.'">
                                <button type="submit" class="profileNameButton">
                                    '.$username.'
                                </button>
                            </form>

                        </div>';
                        */

                    echo '<div id = "viewingArea" class="viewDiv">
                            <video class="video" width="1280" height="720" controls>
                                <source src='.$video_filename.' type="video/mp4">
                            </video>
                        </div>
                        <table>
                            <tr>
                                <td>'.$video_title.'</td>
                                <td class="profileName"><form method="get" action="userchannel.php">
                                    <input type="hidden" name="varname" value='.$userid.'>
                                    <button type="submit" class="profileNameButton">'.$username.'</button>
                                    </form></td>
                            </tr>
                            <tr>
                                <td>'.$video_description.'</td>
                            </tr>
                            <tr></tr>
                            <tr>
                                <td>Date Posted: '.$timestamp.'</td>
                            </tr>
                            <tr>
                                <td>File Size: '.round($video_size, 2).'MB</td>
                            </tr>
                        </table>';
                    if(isset($_SESSION['userid'])){
                        $t = $_SESSION['userid'];
                        $subscribed = 0;
                        $subscribe_array = array();
                        $stmt = $mysqli->prepare("SELECT User_ID FROM Video WHERE Video_ID = '$var_value'");
                        if(!$stmt->execute()) { print("Error getting userid."); } else {
                            $stmt->bind_result($useID);
                            $stmt->fetch();
                            $stmt->close();
                        }
                        $stmt = $mysqli->prepare("SELECT Sub_User_ID FROM Subscribed WHERE User_ID = '$t'");
                        if(!$stmt->execute()){print("Error subscription information."); } else{
                            $stmt->bind_result($subID);
                            while($stmt->fetch()){
                                array_push($subscribe_array,array($subID));
                            }
                            $stmt->close();
                        }
                    $length = count($subscribe_array);
                    for ($i = 0; $i < $length; $i++){
                        if($subscribe_array[$i][0] == $useID){
                            $subscribed = 1;
                        }
                    }
                    if($useID != $t){
                        if($subscribed == 0){
                            echo '
                            <div class="subDiv">
                            <form class = "form" method="post" action="">
                                    <input type="hidden" name="unsubbed" value="hello">
                                    <input type="image" style="width:200px;height:50px;" src="img/unsubbed.png">
                                </input>
                            </form>
                            </div>
                            ';
                        }
                        if($subscribed == 1){
                            echo '
                            <div class="subDiv">
                            <form class = "form" method="post" action="">
                                    <input type="hidden" name="subbed" value="hello">
                                    <input type="image" style="width:200px;height:50px;" src="img/subbed.png">
                                </input>
                            </form>
                            </div>
                            ';
                        }
                    }
                    }
                    if(isset($_SESSION['userid'])){
                        $t = $_SESSION['userid'];
                        $favorited = 0;
                        $favorite_array = array();
                        $stmt = $mysqli->prepare("SELECT Video_ID FROM Favorite_List WHERE User_ID = '$t'");
                        if(!$stmt->execute()) { print("Error video information."); } else {
                            $stmt->bind_result($vidID);
                            while($stmt->fetch()) {
                                array_push($favorite_array, array($vidID));
                            }
                            $stmt->close();
                        }
                        $length = count($favorite_array);
                        for ($i = 0; $i < $length; $i++){
                            if($favorite_array[$i][0] == $var_value)
                            {
                                $favorited = 1;
                            }
                        }
                        if ($favorited == 0){
                            echo '
                            <div class="starDiv">
                            <form class = "form" method="post" action="">
                                <input type="hidden" name="favorite" value="hello">
                                    <input type="image" style="width:50px;height:50px;" src="img/star-outline.png">
                                </input>
                            </form>
                            </div>
                            ';
                        }
                        if ($favorited == 1){
                            echo '
                            <div class = "starDiv">
                            <form class = "form" method="post" action="">
                                <input type="hidden" name="unfavorite" value="hello">
                                    <input type="image" style="width:50px;height:50px;" src="img/star-outline-filled.png">
                                </input>
                            </form>
                            </div>
                            ';
                        }
                        echo '
                        <div class="playlistDiv">
                        <form class = "subform" method="post" action="">
                                <input type="text" id="playlistName" name="playlistName" placeholder="Playlist Name" required/>
                                <button type="submit" id="playlistNameButton" name="playlistNameButton">Add to Playlist/Create Playlist</button>
                        </form>
                        </div>
                        <div class="removePlaylistDiv">
                        <form class = "subform" method="post" action="">
                                <input type="text" id="remove" name="remove" placeholder="Name" required/>
                                <button type="submit" id="removePlaylistNameButton" name="removePlaylistNameButton">Remove from Playlist</button>
                        </form>
                        </div>
                        ';
                    }    
                    echo '<div id="spacer" class = "spacer">';
                    $comment_array = array();
                    
                    $stmt = $mysqli->prepare("SELECT Comment, User_ID, Comment_ID FROM Comments WHERE Video_ID = '$var_value'");
                    if(!$stmt->execute()) { print("Error video information."); } else {
                        $stmt->bind_result($comment, $userid, $commentid);
                        while($stmt->fetch()) {
                            array_push($comment_array, array($comment, $userid, $commentid));
                        }
                        $stmt->close();
                    }
                    $length = count($comment_array);
      
                    for ($i = 0; $i < $length; $i++) {
                        $tempID = $comment_array[$i][1];
                        $content = $comment_array[$i][0];
                        $temp_commentID = $comment_array[$i][2];
                        $stmt = $mysqli->prepare("SELECT username FROM User WHERE User_ID = '$tempID'");
                        if(!$stmt->execute()) { print("Error user information."); } else {
                            $stmt->bind_result($usernameComment);
                            $stmt->fetch();
                        }
                        echo ' 
                            <div class="commentsDiv">
                                <form method="get" action="userchannel.php">
                                    <input type="hidden" name="varname" value="'.$tempID.'">
                                    <button type="submit" class="commentsButton">
                                        '.$usernameComment.'
                                    </button>
                                </form>
                                <p class="comments">
                                    '.$content.'
                                </p>
                            </div>

                        ';
                        $stmt->close();

                        $sub_comment_array = array();
                        $stmt = $mysqli->prepare("SELECT Comment, User_ID FROM SubComments WHERE Above_Comment_ID = '$temp_commentID'");
                        if(!$stmt->execute()) { print("Error video information."); } else {
                            $stmt->bind_result($sub_comment, $sub_userid);
                            while($stmt->fetch()) {
                                array_push($sub_comment_array, array($sub_comment, $sub_userid));
                            }
                            $stmt->close();
                        }
                        $length_sub = count($sub_comment_array);
                        for($j = 0; $j < $length_sub; $j++){
                            $sub_tempID = $sub_comment_array[$j][1];
                            $sub_content = $sub_comment_array[$j][0];
                            $stmt = $mysqli->prepare("SELECT username FROM User WHERE User_ID = '$sub_tempID'");
                            if(!$stmt->execute()) { print("Error user information."); } else {
                                $stmt->bind_result($sub_usernameComment);
                                $stmt->fetch();
                            }
                            $stmt->close();
                            echo ' 
                            <div class="commentsDiv">
                                <form method="get" action="userchannel.php">
                                    <input type="hidden" name="varname" value="'.$sub_tempID.'">
                                    <button type="submit" class="subcommentsButton">
                                        '.$sub_usernameComment.'
                                    </button>
                                </form>
                                <p class="subcomments">
                                    '.$sub_content.'
                                </p>
                            </div>

                            ';
                        }
                        if(isset($_SESSION['userid']) && !empty($tempID)) {
                            $tempUserID = $_SESSION['userid'];
                            echo '
                            <form class = "subform" method="post" action="">
                                    <input type="hidden" name="sub_id_user" value="'.$tempUserID.'">
                                    <input type="hidden" name="above_commentID" value="'.$temp_commentID.'">
                                    <input type="text" id="commentBox" name="sub_commentBox" placeholder="Comment" required/>
                                    <button type="submit" id="commentButton" name="commentButton">Reply</button>
                            </form>
                            ';
                        }
                        
                    }
                    if(isset($_SESSION['userid'])) {
                        $tempUserID = $_SESSION['userid'];
                        echo '
                        <form class = "form" method="post" action="">
                                <input type="hidden" name="id_user" value="'.$tempUserID.'">
                                <input type="text" id="commentBox" name="commentBox" placeholder="Comment" required/>
                                <button type="submit" id="commentButton" name="commentButton">Post Comment</button>
                        </form>
                        ';
                    } 
                ?>
            </div>
          </div>
      </div>
    </main>
  </body>
</html>