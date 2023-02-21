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
            <nav id="menu">
            <?php if (isset($_SESSION['connected_id'])) { ?>
                <a href='admin.php'><img src="resoc.jpg" alt="Logo de notre réseau social"/></a>
                <a href='news.php'>News</a>
                <a href='feed.php?user_id=<?php echo $_SESSION['connected_id']?>'>Feed</a>
                <a href='tags.php?tag_id=1'>Keywords</a>
                <?php } ?>
            </nav>
            <nav id="user">
                <?php if (isset($_SESSION['connected_id'])) { ?>
                    <a href="#">▾ Profile</a>
                    <ul>
                        <li><a href='settings.php'>Settings</a></li>
                        <li><a href='wall.php'>My Wall</a></li>
                        <li>
                            <form method='post'>
                                <input type='submit' name='logout' value='Se déconnecter'></input>
                            </form>
                        </li> 
                    </ul>
                    <?php } ?>
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
