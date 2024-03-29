<?php 
    include 'header.php';
?>

<title>My wall</title> 

<div id="wall">
    <main>
    <!-- <aside> -->
        <?php
            if (isset($_GET['user_id'])){
                $userId = intval($_GET['user_id']);
            } else {
                $userId = intval($_SESSION['connected_id']);           
            } 
            $sQlRequestForProfilePic = "SELECT profile_picture as profilePic FROM users WHERE id = " . $userId;
            $sqlInfosForDisplayPP = $mysqli->query($sQlRequestForProfilePic);
            $profilePictureInfos = $sqlInfosForDisplayPP->fetch_assoc();
                ?><img src="./profile_pictures/<?php echo $profilePictureInfos['profilePic']; ?>" alt="Portrait de l'utilisatrice"/>
                <form action="wall.php" method="post" enctype="multipart/form-data">
                    <label for="profile_picture">Fichier</label>
                    <input type="file" name="profile_picture">
                    <button type="submit">Enregistrer</button>
                </form>
                <?php
        if (isset($_FILES['profile_picture'])){
                $tmpNamePP = $_FILES['profile_picture']['tmp_name'];
                $namePP = $_FILES['profile_picture']['name'];
                $sizePP = $_FILES['profile_picture']['size'];
                $errorPP = $_FILES['profile_picture']['error'];
                $typePP = $_FILES['profil_picture']['type'];

                // Check picture extension and size
                $tabExtensionPP = explode('.', $namePP);
                $extensionPP = strtolower(end($tabExtensionPP));

                $authorizedExtension = ['jpg', 'jpeg', 'gif', 'png'];
                $maxSize = 40000000;

                if (in_array($extensionPP, $authorizedExtension) && $sizePP <= $maxSize && $errorPP == 0){

                    $uniqueNamePP = uniqid('', true);
                    $fileNamePP = $uniqueNamePP.'.'.$extensionPP;

                    move_uploaded_file($tmpNamePP, './profile_pictures/'.$fileNamePP);
                    var_dump($fileNamePP);
                } else {
                    echo 'Wrong extension or max size exceeded.';
                }
                $sQlStatementForProfilePic = "UPDATE users "
                . "SET profile_picture = '". $fileNamePP . "'"
                . "WHERE id = " . $userId;
                $sQlInformationsPP = $mysqli->query($sQlStatementForProfilePic);
                $profilePic = $sQlInformationsPP->fetch_assoc();
                header('Location: wall.php?user_id=' . $userId);
        } else {
        }


        
        $laQuestionEnSql = "SELECT * FROM users WHERE id= '$userId' ";
        $lesInformations = $mysqli->query($laQuestionEnSql);
        $user = $lesInformations->fetch_assoc();
        ?>
        <section>
            <!-- <h3>Description</h3> -->
            <p><?php echo $user['alias']; ?>
            </p>
            <?php 
                if (isset($_GET['user_id'])){
                    if (isset($_POST['follow'])) {
                        $userId = intval($_GET['user_id']); 
                        $followingId = intval($_SESSION['connected_id']);
                        $followersSql = "INSERT INTO followers "
                        . "(id, followed_user_id, following_user_id) "
                        . "VALUES (NULL, "
                        . $userId . ", "
                        . $followingId . ")";
                        $ok = $mysqli->query($followersSql);
                        if ( ! $ok){
                            echo "You can't follow this user." . $mysqli->error;
                        } else {
                            echo "You are now following this user.";
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
                $followedSql = "
                    SELECT COUNT(followed_user_id) as totalfollowed FROM followers WHERE followed_user_id='$userId'
                ";
                $infos = $mysqli->query($followedSql);
                if ( ! $infos)
                {
                    echo("Request failed : " . $mysqli->error . $followedSql);
                }
                $numberOfFollowedUsers = $infos->fetch_assoc()['totalfollowed'];

                $followingSql = "
                    SELECT COUNT(following_user_id) as totalfollowing FROM followers WHERE following_user_id='$userId'
                ";
                $infos = $mysqli->query($followingSql);
                if ( ! $infos)
                {
                    echo("Request failed : " . $mysqli->error . $followingSql);
                }
                $numberOfFollowingUsers = $infos->fetch_assoc()['totalfollowing'];
             ?>
             <p>Followed by : <?php echo $numberOfFollowedUsers?></p>
             <p>Following : <?php echo $numberOfFollowingUsers?></p>
        </section>
    <!-- </aside> -->
    <!-- <main> -->
        <?php
        $laQuestionEnSql = "
            SELECT posts.content, posts.created, posts.id as postID, posts.picture_name as picture_name, users.alias as author_name, users.id as user_id, 
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
            echo("Request failed : " . $mysqli->error);
        }
        if (isset($_GET['user_id'])){
        } else {
            if (empty($_POST['message'])){
                echo "You can't add an empty message.";
            } else {
                // Add picture to post
                $tmpName = $_FILES['file']['tmp_name'];
                $name = $_FILES['file']['name'];
                $size = $_FILES['file']['size'];
                $error = $_FILES['file']['error'];
                $type = $_FILES['file']['type'];

                // Check picture extension and size
                $tabExtension = explode('.', $name);
                $extension = strtolower(end($tabExtension));

                $authorizedExtension = ['jpg', 'jpeg', 'gif', 'png'];
                $maxSize = 40000000;

                if (in_array($extension, $authorizedExtension) && $size <= $maxSize && $error == 0){

                    $uniqueName = uniqid('', true);
                    $fileName = $uniqueName.'.'.$extension;

                    move_uploaded_file($tmpName, './upload/'.$fileName);
                } else {
                    'Wrong extension or max size exceeded.';
                }
                $cityHashTagContent = $_POST['cityHashtag'];
                $cityHashTagContent = $mysqli->real_escape_string($cityHashTagContent);
                $lInstructionSqlHashtag = "SELECT id as location_id, label as location_label FROM tags WHERE label = '" . $cityHashTagContent . "'";
                $okHashTag = $mysqli->query($lInstructionSqlHashtag);
                if ( ! $okHashTag){
                    echo ":HashTagProblem " . $mysqli->error;
                } else {
                    echo "Posted in: ";
                }
                $checkingLocationInfos = $okHashTag->fetch_assoc();
                $tag_id = $checkingLocationInfos['location_id'];
                $tagInfo = $checkingLocationInfos['location_label'];
                if ($cityHashTagContent == $tagInfo){
                    $postContent = $_POST['message'];
                    $postContent = $mysqli->real_escape_string($postContent);
                    $lInstructionSql = "INSERT INTO posts "
                    . "(id, user_id, content, created, picture_name)"
                    . "VALUES (NULL, "
                    . $userId . ", "
                    . "'" . $postContent . "', "
                    . "NOW(), '" . $fileName  . "')";
                    $ok = $mysqli->query($lInstructionSql);
                    if ( ! $ok){
                        echo "You can't add the message : " . $mysqli->error;
                    } else {
                        echo "Message posted by :" . $userId;   
                    }
                    $requestPostIdInfos = "SELECT LAST_INSERT_ID() as postTagId";
                    $informationPostId = $mysqli->query($requestPostIdInfos);
                    $postIdInfos = $informationPostId->fetch_assoc();
                    $post_id = $postIdInfos['postTagId'];
                    $lInstructionSqlPostHashtag = "INSERT INTO posts_tags "
                    . "(id, post_id, tag_id) "
                    . "VALUES(NULL, " . $post_id . ", " . $tag_id . ")";
                    $okPostTag = $mysqli->query($lInstructionSqlPostHashtag);
                    if ( ! $okPostTag){
                        echo "You can't add the tag: " . $mysqli->error;
                    } else {
                        echo "Tag posted by :";
                    }
                } else {
                    $lInstructionSqlHashtag = "INSERT INTO tags "
                    . "(id, label) "
                    . "VALUES (NULL, '" . $cityHashTagContent . "')";
                    $okHashTag = $mysqli->query($lInstructionSqlHashtag);
                    if ( ! $okHashTag){
                        echo "You can't add the htag: " . $mysqli->error;
                    } else {
                        echo "Htag posted by :";
                    }
                    $requestTagIdInfos = "SELECT LAST_INSERT_ID() as tagPostId";
                    $informationTagId = $mysqli->query($requestTagIdInfos);
                    $tagIdInfos = $informationTagId->fetch_assoc();
                    $tag_id = $tagIdInfos['tagPostId'];
                    $postContent = $_POST['message'];
                    $postContent = $mysqli->real_escape_string($postContent);
                    $lInstructionSql = "INSERT INTO posts "
                    . "(id, user_id, content, created, picture_name) "
                    . "VALUES (NULL, "
                    . $userId . ", "
                    . "'" . $postContent . "', "
                    . "NOW(), '" . $fileName  . "')";
                    $ok = $mysqli->query($lInstructionSql);
                    if ( ! $ok){
                        echo "You can't add the message: " . $mysqli->error;
                    } else {
                        echo "Message posted by :" . $userId;   
                    }
                    $requestPostIdInfos = "SELECT LAST_INSERT_ID() as postTagId";
                    $informationPostId = $mysqli->query($requestPostIdInfos);
                    $postIdInfos = $informationPostId->fetch_assoc();
                    $post_id = $postIdInfos['postTagId'];
                    $lInstructionSqlPostHashtag = "INSERT INTO posts_tags "
                    . "(id, post_id, tag_id) "
                    . "VALUES(NULL, " . $post_id . ", " . $tag_id . ")";
                    $okPostTag = $mysqli->query($lInstructionSqlPostHashtag);
                    if ( ! $okPostTag){
                        echo "You can't add the tag: " . $mysqli->error;
                    } else {
                        echo "Tag posted by :";
                    }
                }
            }
            ?>
            <article>
                <form action="wall.php" method="post" enctype="multipart/form-data">
                    <input type='hidden' name='message' value='achanger'>
                        <dl>
                            <dt><label for='message'>Add a post</label></dt>
                            <dd> 
                                <div class="hastag-location">#Location</div>
                                <input type="text" name="cityHashtag">
                                <br><br>
                                <p>Share your experience here :</p>
                                <textarea name='message'></textarea>
                                <label for="file"></label>
                                <br><input type="file" name="file"></br>
                                <br><input type='submit' value="Post"></br>
                            </dd>
                        </dl>     
                </form>
            </article>
            <?php
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
                header("Location: wall.php");
            }
            $post = $lesInformations->fetch_assoc();
        }
            
        while ($post = $lesInformations->fetch_assoc()){
            $likeSessionID = $_SESSION['connected_id'];
            $postSessionID = $post['postID'];
            $hasBeenLikedSql = "SELECT likes.id FROM likes WHERE user_id = $likeSessionID AND post_id = $postSessionID";
            $informationsLikes = $mysqli->query($hasBeenLikedSql);
            $likeInfos = $informationsLikes->fetch_assoc();
            // echo "<pre>" . print_r($post, 1) . "</pre>";
            ?>                
            <article>
                <p>
                    <time datetime='2020-02-01 11:12:13' > <?php echo $post['created'];?> </time>
                </p>
                <address>by <a href="wall.php?user_id=<?php echo $post['user_id'] ?>"><?php echo $post['author_name'] ?></a></address>
                <div>
    
                    <p><?php echo $post['content'];?></p>
                    <img src="./upload/<?php echo $post['picture_name']; ?>">
                </div> 
                <footer></footer>                                           
                    <small>
                    <?php 
                        if (isset($likeInfos) == false){
                    ?>
                        <form action="wall.php" method="post">
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
                    <div id="hashtag">
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
        <?php 
            } 
        ?>
    </main>
</div>
<?php 
    include 'footer.php';
?>