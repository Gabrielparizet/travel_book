<?php 
    include 'header.php';
    if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['like_post_id'])) {
        // var_dump($_SESSION['connected_id'], $_POST['like_post_id']);
            $likeSqlRequest = "INSERT INTO likes"
            . "(id, user_id, post_id)"
            . "VALUES (NULL, " . $_SESSION['connected_id'] . ", " . $_POST['like_post_id'] . ")";
            $ok = $mysqli->query($likeSqlRequest);
    }
?>

<title>Mur</title> 

<div id="wrapper">
    <aside>
        <?php
            if (isset($_GET['user_id'])){
                $userId = intval($_GET['user_id']);
            } else {
                $userId = intval($_SESSION['connected_id']);
            } 
        $laQuestionEnSql = "SELECT * FROM users WHERE id= '$userId' ";
        $lesInformations = $mysqli->query($laQuestionEnSql);
        $user = $lesInformations->fetch_assoc();
        ?>
        <img src="user.jpg" alt="Portrait de l'utilisatrice"/>
        <section>
            <h3>Présentation</h3>
            <p>Sur cette page vous trouverez tous les message de l'utilisatrice : <?php echo $user['alias']; ?>
            </p>
            <?php 
                if (isset($_GET['user_id'])){
                    if ($_POST['follow']) {
                        $userId = intval($_GET['user_id']); 
                        $followingId = intval($_SESSION['connected_id']);
                        $followersSql = "INSERT INTO followers "
                        . "(id, followed_user_id, following_user_id) "
                        . "VALUES (NULL, "
                        . $userId . ", "
                        . $followingId . ")";
                        $ok = $mysqli->query($followersSql);
                        if ( ! $ok){
                            echo "Impossible de suivre cet utilisateur." . $mysqli->error;
                        } else {
                            echo "Vous suivez maintenant cet utilisateur.";
                        }
                    }
            ?>
            <form method='post'>
                <input type='submit' name='follow' value='suivre'>
                </input>
            </form>
            <?php 
                } else {
                } 
             ?>
        </section>
    </aside>
    <main>
        <?php
        $laQuestionEnSql = "
            SELECT posts.content, posts.created, posts.id as postID, users.alias as author_name, users.id as user_id, 
            COUNT(likes.id) as like_number, GROUP_CONCAT(DISTINCT tags.label) AS taglist 
            FROM posts
            JOIN users ON  users.id=posts.user_id
            LEFT JOIN posts_tags ON posts.id = posts_tags.post_id  
            LEFT JOIN tags       ON posts_tags.tag_id  = tags.id 
            LEFT JOIN likes      ON likes.post_id  = posts.id 
            WHERE posts.user_id='$userId' 
            GROUP BY posts.id
            ORDER BY posts.created DESC  
            ";
        $lesInformations = $mysqli->query($laQuestionEnSql);
        if ( ! $lesInformations)
        {
            echo("Échec de la requete : " . $mysqli->error);
        }
        if (isset($_GET['user_id'])){
        } else {
            if (empty($_POST['message'])){
                echo "Impossible d'ajouter le message sans contenu.";
            } else {
                $postContent = $_POST['message'];
                $postContent = $mysqli->real_escape_string($postContent);
                $lInstructionSql = "INSERT INTO posts "
                . "(id, user_id, content, created) "
                . "VALUES (NULL, "
                . $userId . ", "
                . "'" . $postContent . "', "
                . "NOW())";
                $ok = $mysqli->query($lInstructionSql);
                if ( ! $ok){
                    echo "Impossible d'ajouter le message: " . $mysqli->error;
                } else {
                    echo "Message posté en tant que :" . $userId;
                }
            }
            ?>
                <article>
                    <form action="wall.php" method="post">
                        <input type='hidden' name='message' value='achanger'>
                        <dl>
                            <dt><label for='message'>Message</label></dt>
                            <dd><textarea name='message'></textarea></dd>
                        </dl>
                        <input type='submit' value="Send">
                    </form>
                </article>
            <?php
        }
            
        while ($post = $lesInformations->fetch_assoc())
        {
            // echo "<pre>" . print_r($post, 1) . "</pre>";
            ?>                
            <article>
                <h3>
                    <time datetime='2020-02-01 11:12:13' > <?php echo $post['created'];?> </time>
                </h3>
                <address>par <a href="wall.php?user_id=<?php echo $post['user_id'] ?>"><?php echo $post['author_name'] ?></a></address>
                <div>
    
                    <p><?php echo $post['content'];?></p>
                </div>                                            
                <footer>
                    <small>
                    <form action="wall.php" method="post">
                        <input type="hidden" name="like_post_id" value=<?php echo $post['postID']?>>
                            <input type="submit" value="♥">
                                <?php 
                                    echo $post['like_number'];
                                ?>
                            </input>
                        </input>
                    </form>
                    </small>
                    <?php 
                    $tag = $post['taglist'];
                    $arrayOfTags = explode(",",$tag);
                    $index = 0;
                    for ($index = 0; $index < count($arrayOfTags); $index++) {
                        echo '<a href="">' . "#" . $arrayOfTags[$index] . '</a>' . ' ';
                    }
                    ?>
                </footer>
            </article>
        <?php 
            } 
        ?>
    </main>
</div>
