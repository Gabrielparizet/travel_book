<?php 
    include 'header.php';
?>

<title>Settings</title> 
<div id="wrapper" class='profile'>
    <aside>
        <img src="user.jpg" alt="Portrait de l'utilisatrice"/>
        <section>
            <h3>Description</h3>
            <p>On this page you will find the informations about user number :  <?php echo intval($_SESSION['connected_id']) ?></p>

        </section>
    </aside>
    <main>
        <?php
        $userId = intval($_SESSION['connected_id']);
        /**
         * Etape 3: récupérer le nom de l'utilisateur
         */
        $laQuestionEnSql = "
            SELECT users.*, 
            count(DISTINCT posts.id) as totalpost, 
            count(DISTINCT given.post_id) as totalgiven, 
            count(DISTINCT recieved.user_id) as totalrecieved 
            FROM users 
            LEFT JOIN posts ON posts.user_id=users.id 
            LEFT JOIN likes as given ON given.user_id=users.id 
            LEFT JOIN likes as recieved ON recieved.post_id=posts.id 
            WHERE users.id = '$userId' 
            GROUP BY users.id
            ";
        $lesInformations = $mysqli->query($laQuestionEnSql);
        if ( ! $lesInformations)
        {
            echo("Échec de la requete : " . $mysqli->error);
        }
        $user = $lesInformations->fetch_assoc();

        // echo "<pre>" . print_r($user, 1) . "</pre>";
        ?>                
        <article class='parameters'>
            <h3>My settings</h3>
            <dl>
                <dt>Nickname</dt>
                <dd><?php echo $user['alias']?></dd>
                <dt>Email</dt>
                <dd><?php echo $user['email']?></dd>
                <dt>Number of posts</dt>
                <dd><?php echo $user['totalpost']?></dd>
                <dt>Number of likes given </dt>
                <dd><?php echo $user['totalgiven']?></dd>
                <dt>Number of likes received</dt>
                <dd><?php echo $user['totalrecieved']?></dd>
            </dl>

        </article>
    </main>
</div>
