<?php 
    include 'header.php';
?>

<title>Feed</title> 

<div id="wrapper">
    <?php
        $userId = intval($_SESSION['connected_id']);
    ?>
    <main>
    
        <?php
        /**
         * Etape 3: récupérer tous les messages des abonnements
         */
        $laQuestionEnSql = "
            SELECT posts.content,
            posts.created,
            posts.id as postID,
            posts.picture_name,
            users.alias as author_name,  
            count(likes.id) as like_number,  
            users.id as user_id,
            GROUP_CONCAT(DISTINCT tags.label) AS taglist 
            FROM followers 
            JOIN users ON users.id=followers.followed_user_id
            JOIN posts ON posts.user_id=users.id
            LEFT JOIN posts_tags ON posts.id = posts_tags.post_id  
            LEFT JOIN tags       ON posts_tags.tag_id  = tags.id 
            LEFT JOIN likes      ON likes.post_id  = posts.id 
            WHERE followers.following_user_id='$userId' 
            GROUP BY posts.id
            ORDER BY posts.created DESC  
            ";
        $lesInformations = $mysqli->query($laQuestionEnSql);
        if ( ! $lesInformations)
        {
            echo("Request failed : " . $mysqli->error);
        }

            // Création des likes
            if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['like_post_id'])) {
                $likeSqlRequest = "INSERT INTO likes"
                . "(id, user_id, post_id)"
                . "VALUES (NULL, " . $_SESSION['connected_id'] . ", " . $_POST['like_post_id'] . ")";
                $ok = $mysqli->query($likeSqlRequest);
                if ( ! $ok){
                    echo "You can't like this post." . $mysqli->error;
                } else {
                }
                header('Location: feed.php');
            }

        while ($post = $lesInformations->fetch_assoc()) {
            // echo "<pre>" . print_r($post, 1) . "</pre>";
            $likeSessionID = $_SESSION['connected_id'];
            $postSessionID = $post['postID'];
            $hasBeenLikedSql = "SELECT likes.id FROM likes WHERE user_id = $likeSessionID AND post_id = $postSessionID";
            $informationsLikes = $mysqli->query($hasBeenLikedSql);
            $likeInfos = $informationsLikes->fetch_assoc();
        ?>                
        <article>
            <p>
                <time datetime='2020-02-01 11:12:13' ><?php echo $post['created'] ?></time>
            <p>
            <address>by <a href="wall.php?user_id=<?php echo $post['user_id'] ?>"><?php echo $post['author_name'] ?></a></address>
            <div>
                <p><?php echo $post['content'] ?></p>
                <img src="./upload/<?php echo $post['picture_name']; ?>">
            </div>                                            
            <footer>
                <small>
                    <?php 
                        if (isset($likeInfos) == false){
                            ?>
                            <form action="feed.php" method="post">
                                <input type="hidden" name="like_post_id" value="<?php echo $post['postID']?>"/>
                                    <input type="submit" value="♥"/>
                                        <?php 
                                            echo $post['like_number'] ;
                                        ?>
                            </form>
                            <?php
                        } else {
                            ?>
                                <div>
                                    <?php echo $post['like_number'];?>♥
                                </div>
                            <?php
                        }
                    ?>
                </small>
            <div id="hastag">
                <?php 
                        $tag = $post['taglist'];
                        $arrayOfTags = explode(",",$tag);
                        $index = 0;
                        for ($index = 0; $index < count($arrayOfTags); $index++) {
                            echo '<a href="">' . "#" . $arrayOfTags[$index] . '</a>' . ' ';
                        }
                        ?>
            </div>
            </footer>
        </article>
        <?php } ?>
    </main>
</div>
<?php 
    include 'footer.php';
?>