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
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <!-- <aside> -->
        <!-- <header> -->
            <!-- <a href='admin.php'><img src="resoc.jpg" alt="Logo de notre réseau social"/></a> -->
            <!-- <nav id="menu"> -->

        <div class="menu">
                 <!--Top menu -->
            <?php if (isset($_SESSION['connected_id'])) { ?>
             <div class="sidebar">
                <!--menu item-->
                <ul>
                    <li>
                        <a href="news.php">
                            <span class = "icon"><i class="fas fa-home"></i></span>
                            <span class = "item">Home</span>
                        </a>
                    </li>
                    <li>
                        <a href="wall.php">
                            <span class = "icon"><i class="fas fa-desktop"></i></span>
                            <span class = "item">Wall</span>
                        </a>
                    </li>
                    <li>
                        <a href='feed.php?user_id=<?php echo $_SESSION['connected_id']?>'>
                            <span class = "icon"><i class="fas fa-user-friends"></i></span>
                            <span class = "item">Feed</span>
                        </a>
                    </li>
                    <li>
                        <a href="tags.php?tag_id=1">
                            <span class = "icon"><i class="fas fa-database"></i></span>
                            <span class = "item">Tags</span>
                        </a>
                    
                    </li>
            <?php } ?>
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
                </ul> 
            </div>
        </div>
    
        <!-- Connexion à la base de donnée -->
        <?php                
            $mysqli = new mysqli("localhost", "root", "root", "socialnetwork");
        ?>

            