<?php 
    session_start();
    if ($_POST['logout']){
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
                <a href="news.php">Actualités</a>
                <a href="wall.php">Mur</a>
                <a href="feed.php">Flux</a>
                <a href="tags.php?tag_id=1">Mots-clés</a>
            </nav>
            <nav id="user">
                <a href="#">▾ Profil</a>
                <ul>
                    <li><a href="settings.php">Paramètres</a></li>
                    <li><a href="followers.php">Mes suiveurs</a></li>
                    <li><a href="subscriptions.php">Mes abonnements</a></li>
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
