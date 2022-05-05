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
    // getting all related videos to search term
    // these videos will be printed in the body below

    $mysqli = OpenConnection();

    // if the get request was sent
    if(isset($_GET['searchBox'])) {
        $search = $_GET['searchBox'];

        // if the search box isn't empty
        if(!empty($search)) {
          // search box is not empty, process search
          $search_terms = explode(" ", $search);

          $video_array = array();

          // this will produce an array that contains arrays of video_id, video_name, video_description, and key_words
          $stmt = $mysqli->prepare("SELECT Video_ID, Video_Name, Video_Description, Key_Words FROM Video");
          if(!$stmt->execute()) { print("Error getting video information"); } else {
              $stmt->bind_result($videoid_entry, $videoname_entry, $videodescription_entry, $keywords_entry);
              while($stmt->fetch()) {
                  // contains video name, description, and keywords in one string
                  $megastring = $videoname_entry . ' ' . $videodescription_entry . ' ' . $keywords_entry;
                  $formatted_megastring = preg_replace("/(?![.=$'%-])\p{P}/u", "", $megastring);
                  array_push($video_array, array($videoid_entry, strtolower($formatted_megastring)));
              }
              $stmt->close();
          }

          // array to hold video_ids of videos that matched search criteria
          $matched_video_array = array();

          // variable to hold percentage
          $percent;

          // what we have currently:
          // $video_array - array that holds all of the currently uploaded video data
          // $search_terms - array that holds

          // foreach search_term as term
          foreach($search_terms as $term) {

            // for the count of videos in video_array
            for($i = 0; $i < count($video_array); $i++) {

              $search_array = explode(" ", $video_array[$i][1]);

              // search the megastring in $video_array[$i][1] to see if term matches
              // using simlar_term to look for even words that aren't exact matches
              for($j = 0; $j < count($search_array); $j++) {
                similar_text($term, $search_array[$j], $percent);
                if($percent > 80) {
                  array_push($matched_video_array, $video_array[$i][0]);
                }
              }

            }
          }
        }
      }

      // ensuring no duplicate values in matched array
      if($matched_video_array != NULL) {
        $matched_video_array = array_unique($matched_video_array);
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

        <!-- This will host the right video browsing pane-->
        <div class="right-column">
          <!-- Will be a PHP block here to generate the divs for each video -->
          <div class="wrapper">

          <?php
            // connect to database
            $mysqli = openConnection();

            // php to generate video blocks
            if(!empty($matched_video_array)) {
                // generate video blocks from array values (video ids)
                for($i = 0; $i < count($matched_video_array); $i++) {
                    $stmt = $mysqli->prepare("SELECT Video_ID, Video_Name, Video_Description, User_ID, Video_Filename, Video_Thumbnail_File FROM Video WHERE Video_ID = '$matched_video_array[$i]'");
                    if(!$stmt->execute()) { print("Error fetching video information for Video_ID '$matched_video_array[$i]'\n"); } else {
                      $stmt->bind_result($videoid_entry, $videoname_entry, $videodescription_entry, $userid_entry, $videofile_entry, $thumbnailfile_entry);
                      $stmt->fetch();
                      $stmt->close();

                      // print the video block
                      echo '<div class="video-block-search">
                              <form method="get" action="viewing.php">
                                <input type="hidden" name="varname" value='.$videoid_entry.'>
                                <input type="image" style="width:150px;height:125px;" border="2" border-color="black" src='.$thumbnailfile_entry.'></input>
                              </form>
                              <div class="video-search-text">
                                <p class="title-search">'.$videoname_entry.'</p>
                                <p class="description-search">'.$videodescription_entry.'</p>
                              </div>
                            </div>';

                    }
                }
            }

            closeConnection($mysqli);
          ?>

            <!-- Sample div for video search
            <div class="video-block-search">
              // Thumbnail will go here
              <canvas class="video-thumbnail-search">
                <img src="demo.png">
              </canvas>

              <div class="video-search-text">
                 // The title will go here
                <p class="title-search">This is a demo title.</p>
                <p class="description-search">This is a demo description.</p>
              </div>
            </div> -->

          </div>
        </div>
      </div>

    </main>

  </body>

</html>