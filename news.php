<?php 
    include 'index.php';
?>
<div id="wrapper">
    <aside>
        <img src="user.jpg" alt="Portrait de l'utilisatrice"/>
        <section>
            <h3>Présentation</h3>
            <p>Sur cette page vous trouverez les derniers messages de
                tous les utilisatrices du site.</p>
        </section>
    </aside>
    <main>
        <?php
        // Gestion d'erreurs
        if ($mysqli->connect_errno)
        {
            echo "<article>";
            echo("Échec de la connexion : " . $mysqli->connect_error);
            echo("<p>Indice: Vérifiez les parametres de <code>new mysqli(...</code></p>");
            echo "</article>";
            exit();
        }

        // Requête SQL articles
        $laQuestionEnSql = "
            SELECT posts.content,
            posts.created,
            users.alias as author_name,  
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
        if ( ! $lesInformations)
        {
            echo "<article>";
            echo("Échec de la requete : " . $mysqli->error);
            echo("<p>Indice: Vérifiez la requete  SQL suivante dans phpmyadmin<code>$laQuestionEnSql</code></p>");
            exit();
        }

        // Création des articles
        while ($post = $lesInformations->fetch_assoc())
        {
            ?>
            <article>
                <h3>
                    <time><?php echo $post['created'] ?></time>
                </h3>
                <address><?php echo $post['author_name'] ?></address>
                <div>
                    <p><?php echo $post['content'] ?></p>
                </div>
                <footer>
                    <small>♥<?php echo $post['like_number'] ?></small>
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

