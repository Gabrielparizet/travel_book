<?php 
    include 'header.php';
?>

<title>Settings</title> 
<div id="wrapper" class='profile'>
    <main>
        <img src="user.jpg" alt="Portrait de l'utilisatrice"/>
    
        <h3>Settings :</h3>
        <p><?php intval($_SESSION['connected_id']) ?></p>
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
            echo("Request failed : " . $mysqli->error);
        }
        $user = $lesInformations->fetch_assoc();

        // echo "<pre>" . print_r($user, 1) . "</pre>";
        ?>                
        <article class='parameters'>
            <dl> Nickname : <?php echo $user['alias']?></dl>
            <dl> Email : <?php echo $user['email']?></dl>
            <dl> Number of posts : <?php echo $user['totalpost']?></dl>
            <dl> Number of likes given : <?php echo $user['totalgiven']?></dl>
            <dl> Number of likes received : <?php echo $user['totalrecieved']?></dl>
        </article>
    </main>
</div>
