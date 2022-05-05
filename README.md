# MeTube
MeTube is a YouTube clone created for my database systems final project. This project was built using PHP, HTML, and CSS. Our code communicated to a MySQL database that was created and managed using PHPMyAdmin. MeTube was built to run on an Apache webserver.

## Features
- Registration
- Login
- Logout
- Profile updating
- Browse
  - By category
  - By channel
- Friends
- Messaging (between friends)
- Video upload (with custom thumbnail upload)
- Commenting
  - Replying to comments
- Subscribing
- Playlists
- Favorite list

## Notes
If you decide to use this project, you will need to reconfigure the php.ini file on your Apache server. This is due to the video upload feature requiring a larger maximum filesize for uploads than the default 2MB used by Apache.
