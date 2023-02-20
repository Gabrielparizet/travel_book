<?php 
    if ($_POST('like'))
    $likeSqlRequest = $mysqli->real_escape_string($_POST['like']);
        $likeSql = "INSERT INTO likes"
        . "(id, user_id, post_id)"
        . "VALUES (NULL, " . $sessionId . ", " . $post['postID'] . ")";
        $ok = $mysqli->query($likeSql);
        if ( ! $ok){
        echo "Impossible d'aimer ce poste." . $mysqli->error;
            } else {
            }
            header('Location: news.php');
?>