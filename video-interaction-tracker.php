<!DOCTYPE html>
<html>
<head>
    <title>Vimeo Video Player Custom Tracking</title>
    <style>
        /* CSS for video container */
        .video-container {
            display: flex;
        }

        /* CSS for the video element */
        #vimeo-player {
            flex: 1;
        }

        /* CSS for the message log */
        #message-log {
            flex: 0.5;
            margin-left: 20px;
            padding: 10px;
            background-color: white;
            max-height: 80vh;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <?php
    // Start a PHP session to handle user authentication
    session_start();

    // Authentication Logic (Replace with your actual authentication logic)
    $authenticated = false;
    $username = 'anonymous'; // Default username

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Simulated login (replace with your login logic)
        $input_username = $_POST['username'];
        $password = $_POST['password'];

        // Verify the username and password (replace with your authentication logic)
        if (($input_username === 'Monica' && $password === 'password') ||
            ($input_username === 'Jose' && $password === 'password2')) {
            // Authentication successful
            $authenticated = true;

            // Set the username to the authenticated user's name
            $username = $input_username;
            $_SESSION['username'] = $username;
        } else {
            // Authentication failed
            echo 'Invalid username or password';
        }
    }

    // Logout Logic
    if (isset($_GET['logout'])) {
        // Destroy the session to log out the user
        session_destroy();
        // Redirect the user to the logger.php page
        echo '<script>window.location.href = "EVENTS_ULA.php";</script>';
        exit;
    }
    ?>

    <?php if ($authenticated): ?>
    <div>
        <h2>Hello, <?php echo $username; ?></h2>
        <a href="?logout=true">Log Out</a> <!-- Log Out Button -->
    </div>

    <div class="video-container">
        <!-- Vimeo Video Embed Code -->
        <iframe
            id="vimeo-player"
            src="https://player.vimeo.com/video/849258825"
            frameborder="0"
            allow="autoplay; fullscreen; picture-in-picture"
            style="width: 100%; height: 100%;"
            title="TEST"
        ></iframe>
    </div>

    <!-- Message Log Element -->
    <div id="message-log">
        <h2>Message Log</h2>
        <ul id="log-list"></ul>
    </div>

    <script src="https://player.vimeo.com/api/player.js"></script>

    <script>
        // Initialize the Vimeo player
        const player = new Vimeo.Player(document.getElementById('vimeo-player'));

        // Get the message log element
        const messageLog = document.getElementById('log-list');

        // Variables to track video play start and end times
        let videoDuration = null;
        let totalPlayTime = 0;
        let isPlaying = false;
        let seekStartTime = null;

        // Function to add a message to the log with timestamp and username
        function logMessage(message) {
            const timestamp = new Date().toLocaleTimeString();
            const data = {
                user: '<?php echo $username; ?>', // Display the username
                timestamp: timestamp,
                event: message
            };

            // Send data to the server using AJAX
            fetch('/store_event.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data),
            });

            // Display the message in the log
            const logMessage = `${timestamp} - <?php echo $username; ?> ${message}`;
            const listItem = document.createElement('li');
            listItem.textContent = logMessage;
            messageLog.appendChild(listItem);
        }

        // Add event listeners for play, pause, and ended events
        player.on('play', () => {
            logMessage('Video started playing');
            isPlaying = true;
            seekStartTime = null;
        });

        player.on('pause', () => {
            logMessage('Video paused');
            isPlaying = false;
        });

        player.on('ended', () => {
            logMessage('Video ended');

            // Calculate and display the percentage watched
            const percentageWatched = (totalPlayTime / videoDuration) * 100;
            logMessage(`Percentage watched: ${percentageWatched.toFixed(2)}%`);

            isPlaying = false;
        });

        // Get video duration
        player.getDuration().then(duration => {
            videoDuration = duration;

            // Add event listener for timeupdate (fired constantly while playing)
            player.on('timeupdate', ({ seconds }) => {
                if (isPlaying) {
                    if (seekStartTime === null) {
                        seekStartTime = seconds;
                    } else {
                        totalPlayTime += seconds - seekStartTime;
                        seekStartTime = seconds;
                    }
                    // Ensure totalPlayTime doesn't exceed video duration
                    totalPlayTime = Math.min(totalPlayTime, videoDuration);
                }
            });
        });
    </script>
    <?php endif; ?>

    <?php if (!$authenticated && $_SERVER['REQUEST_METHOD'] !== 'POST'): ?>
    <h2>Login</h2>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Log In</button>
    </form>
    <?php endif; ?>
</body>
</html>
