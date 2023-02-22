<?php 
    include 'header.php';
?>

<title>News</title> 

<div id="wrapper">
    <main>
        <div class="search">>
            <form class="citySearch" action="news.php" method="get">
                <input type="text" name="locationSearchBar" placeholder="Search for a location">
                <button type="submit">
                    <i class="fa fa-search"></i>
                </button>
            </form>
        </div>
        <?php
        // Gestion d'erreurs
        if ($mysqli->connect_errno){
            echo "<article>";
            echo("Connexion failed : " . $mysqli->connect_error);
            echo("<p>Indice: Check parameters of <code>new mysqli(...</code></p>");
            echo "</article>";
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['locationSearchBar'])){
            $locationSearchKeyWord = $_GET['locationSearchBar'];
            $laQuestionEnSql = "
            SELECT posts.content,
            posts.created,
            posts.id as postID,
            users.alias as author_name, 
            users.id as user_id, 
            count(likes.id) as like_number,  
            GROUP_CONCAT(DISTINCT tags.label) AS taglist 
            FROM posts
            JOIN users ON  users.id=posts.user_id
            LEFT JOIN posts_tags ON posts.id = posts_tags.post_id  
            LEFT JOIN tags       ON posts_tags.tag_id  = tags.id 
            LEFT JOIN likes      ON likes.post_id  = posts.id 
            WHERE tags.label = '" . $locationSearchKeyWord . "'
            GROUP BY posts.id
            ORDER BY posts.created DESC  
            ";
            $lesInformations = $mysqli->query($laQuestionEnSql);
            // Vérification
            if ( ! $lesInformations){
                echo "<article>";
                echo("Request failed : " . $mysqli->error);
                echo("<p>Indice: Vérifiez la requete  SQL suivante dans phpmyadmin<code>$laQuestionEnSql</code></p>");
                exit();
            }
        } else {
            // Requête SQL articles
            $laQuestionEnSql = "
                SELECT posts.content,
                posts.created,
                posts.id as postID,
                users.alias as author_name, 
                users.id as user_id, 
                count(likes.id) as like_number,  
                GROUP_CONCAT(DISTINCT tags.label) AS taglist 
                FROM posts
                JOIN users ON  users.id=posts.user_id
                LEFT JOIN posts_tags ON posts.id = posts_tags.post_id  
                LEFT JOIN tags       ON posts_tags.tag_id  = tags.id 
                LEFT JOIN likes      ON likes.post_id  = posts.id 
                GROUP BY posts.id
                ORDER BY posts.created DESC  
                LIMIT 5
                ";
            $lesInformations = $mysqli->query($laQuestionEnSql);
            // Vérification
            if ( ! $lesInformations){
                echo "<article>";
                echo("Request failed : " . $mysqli->error);
                echo("<p>Indice: Vérifiez la requete  SQL suivante dans phpmyadmin<code>$laQuestionEnSql</code></p>");
                exit();
            }
        }
        $sessionId = intval($_SESSION['connected_id']);

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
                header('Location: news.php');
        }
            
        // Création des articles
        while ($post = $lesInformations->fetch_assoc()) { 
            // echo "<pre>" . print_r($post, 1) . "</pre>";
            $likeSessionID = $_SESSION['connected_id'];
            $postSessionID = $post['postID'];
            $hasBeenLikedSql = "SELECT likes.id FROM likes WHERE user_id = $likeSessionID AND post_id = $postSessionID";
            $informationsLikes = $mysqli->query($hasBeenLikedSql);
            $likeInfos = $informationsLikes->fetch_assoc();
            // var_dump($_SESSION['connected_id'], $postSessionID);
            ?>
            <article>
                <h3>
                    <time><?php echo $post['created'] ?></time>
                </h3>
                <address><a href="wall.php?user_id=<?php echo $post['user_id'] ?>"><?php echo $post['author_name'] ?></a></address>
                <div>
                    <p><?php echo $post['content']?></p>
                </div>
          
                    <small>
                        <?php 
                            if (isset($likeInfos) == false){
                                ?>
                                <form action="news.php" method="post">
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
                    <?php 
                        $tag = $post['taglist'];
                        $arrayOfTags = explode(",",$tag);
                        $index = 0;
                        for ($index = 0; $index < count($arrayOfTags); $index++) {
                            echo '<a href="">' . "#" . $arrayOfTags[$index] . '</a>' . ' ';
                        }
                    ?>
            </article>
            <?php
        }
        ?>
        
    </main>
</div>
<?php 
    include 'footer.php';
?>
