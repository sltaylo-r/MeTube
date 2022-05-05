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

    // opening database connection
    $mysqli = OpenConnection();

    // POST variables:
    // titleBox = video title entry
    // descriptionBox = video description entry
    // tagsBox = video tags entry (comma separated)
    if(isset($_POST['uploadButton'])) {
        $video_name = $_POST['titleBox'];
        $video_description = $_POST['descriptionBox'];
        $video_tags = $_POST['tagsBox'];
        $curr_userid = $_SESSION['userid'];

        // used for debugging
        // echo "<pre>"; 
        // print_r($_FILES);

        if(isset($_SESSION['userid'])) {
            // only allowing mp4 files
            $allowedExt = "mp4";
    
            if(isset($_FILES['myFile']['name']) && isset($_FILES['tFile']['name'])) {
                $name = $_FILES['myFile']['name'];
                $tname = $_FILES['tFile']['name'];
                $curr_dir = getcwd();
                $target_dir = "/video/";
                $t_target_dir = "/img/";
                // extension of file
                $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                $tExtension = strtolower(pathinfo($tname, PATHINFO_EXTENSION));
    
                $stmt = $mysqli->prepare("SELECT MAX(Video_ID) FROM Video");
                if(!$stmt->execute()) { print("Error getting Video_ID."); } else {
                    $stmt->bind_result($videoid_largest);
                    $stmt->fetch();
                    $stmt->close();

                    // incrementing videoid for video name to be stored on server
                    $videoid_largest = $videoid_largest + 1;
    
                    // example would be '/var/www/html/public/HTML/' . 'video/5' . '.' . 'mp4'
                    $target_file =  $curr_dir . $target_dir . $videoid_largest . '.' . $extension;
                    $t_target_file =  $curr_dir . $t_target_dir . $videoid_largest . '.' . $tExtension;

                    print($target_file . "\n");
    
                    // array of compatible extensions
                    $extension_array = array("mp4", "avi", "3gp", "mov", "mpeg");
                    $t_extension_array = array("jpeg", "jpg", "png");
    
                    if(in_array($extension, $extension_array) && in_array($tExtension, $t_extension_array)) {
                        if($_FILES['myFile']['size'] >= 50000000) { /*|| ($_FILES['myFile']['size'] == 0*/
                            print("Error, file too large - must be less than 50MB.");
                        } else {
                            // file is appropriate size, upload
                            $temp_name = $_FILES['myFile']['tmp_name'];
                            $didUpload = move_uploaded_file($temp_name, $target_file);

                            $t_temp_name = $_FILES['tFile']['tmp_name'];
                            $t_didUpload = move_uploaded_file($t_temp_name, $t_target_file);

                            if($didUpload) {
                                // file moved, record entry in database
                                $filesize = $_FILES['myFile']['size'];
                                $thumbnail = 'img/' . $videoid_largest . '.' . $tExtension;
                                $video_filename = 'video/' . $videoid_largest . '.' . $extension;
                                $date = date("Y-m-d");
                                $time = date("H:i:s");
                                $curr_time = $date . ' ' . $time;
    
                                // SQL statement to insert video data into Video table
                                $stmt = "INSERT INTO Video (Video_ID, Video_Name, Video_Description, Key_Words, User_ID, Video_Filename, Video_Type, Video_Size, Timestamp, Video_Thumbnail_File) VALUES ('$videoid_largest', '$video_name', '$video_description', '$video_tags', '$curr_userid', '$video_filename', '$extension', '$filesize', '$curr_time', '$thumbnail')";
                            
                                if(mysqli_query($mysqli, $stmt)) {
                                    // success, video data pushed to database

                                    // insert tags into categories table
                                    $categories_array = explode(" ", $video_tags);

                                    // looping through caregories array to insert them into the table
                                    for($i = 0; $i < count($categories_array); $i++) {
                                        $stmt = "INSERT INTO Categories (Video_ID, Category) VALUES ('$videoid_largest', '$categories_array[$i]')";

                                        if(mysqli_query($mysqli, $stmt)) {
                                            // do nothing
                                        } else {
                                            print("Error inserting into table.");
                                        }
                                    }

                                    print("Video uploaded successfully, redirecting to browse page.");
                                    header('Location: browse.php');
                                } else {
                                    // failure, video data not pushed to database
                                    print("Error, video not uploaded.");
                                    echo "ERROR: Could not able to execute $stmt. " . mysqli_error($mysqli);
                                }
                            } else {
                                print("Error, file not moved.");
                            }
                        }
                    } else {
                        print("Error, file extension not allowed.");
                    }
                }
            } else {
                print("Error, FILES name not set.");
            }
        }
    }

    closeConnection($mysqli);
  ?>

    <main>
      <!-- Main navigation bar at the top of all pages -->
      <div class="topnav">
        <a style="background-color: #2196F3" href="browse.php">MeTube</a>
        <div class="search-container">
          <!-- Check my PHP files for how I did the report generation -->
          <form>
            <input type="text" placeholder="Search.." name="search" required>
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

            <div class="wrapper">

            <div class="new-video-container">
                    <p>To upload a video, click the "choose file" button below.</p>
                    <form enctype="multipart/form-data" class="upload-form" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <input type="file" id="myFile" name="myFile">
                        <label for = "myFile"> Video </label>
                        </br>
                        <input type="file" id="tFile" name="tFile">
                        <label for = "tFile"> Thumbnail </label>
                        <div class="video-upload-title">
                            <label for="titleBox">Title</label>
                            <input type="text" id="titleBox" name="titleBox" required/>
                        </div>
                        <div class="video-upload-description">
                            <label for="descriptionBox">Description</label>
                            <input type="text" id="descriptionBox" name="descriptionBox" required/>
                        </div>
                        <div class="video-upload-tags">
                            <label for="tagsBox">Tags (space seperated)</label>
                            <input type="text" id="tagsBox" name="tagsBox" required/>
                        </div>
                        <div class="login-wrapper">
                            <button class="regular-btn" type="submit" id="uploadButton" name="uploadButton">Upload</button>
                        </div>
                    </form>
                    <?= $errorMessage ?? '' ?>
                </div>
            </div>
          </div>
      </div>

    </main>

  </body>

</html>
