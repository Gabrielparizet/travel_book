<?php 
    session_start();
    if (isset($_POST['logout'])){
        session_destroy();
        header('Location: login.php');
    }
    // print_r ($_SESSION['connected_id']);
?>
<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="author" content="Julien Falconnet">
        <link rel="stylesheet" href="style.css"/>
    </head>
    <body>
        <header>
            <a href='admin.php'><img src="resoc.jpg" alt="Logo de notre réseau social"/></a>
            <nav id="menu">
                <a href="news.php">News</a>
                <a href="feed.php">Feed</a>
                <a href="tags.php?tag_id=1">Keywords</a>
            </nav>
            <nav id="user">
                <a href="#">▾ Profile</a>
                <ul>
                    <li><a href="settings.php">Settings</a></li>
                    <li><a href="followers.php">My followers</a></li>
                    <li><a href="subscriptions.php">My subscriptions</a></li>
                    <li><a href="wall.php">My Wall</a></li>
                    <li>
                        <form method='post'>
                            <input type='submit' name='logout' value='Se déconnecter'></input>
                        </form>
                    </li>
                </ul>
            </nav>
        </header>
            <main>
            <!-- Connexion à la base de donnée -->
            <?php
                $mysqli = new mysqli("localhost", "root", "root", "socialnetwork");
            ?>
        </main>
    </body>
</html>
