<?php
    include 'header.php';
?>
<div id="wrapper" >

    <aside>
        <h2>Description</h2>
        <p>On this page you can post a message pretending you are someone else</p>
    </aside>
    <main>
        <article>
            <h2>Post a message</h2>
            <?php
            /**
             * Récupération de la liste des auteurs
             */
            $listAuteurs = [];
            $laQuestionEnSql = "SELECT * FROM users";
            $lesInformations = $mysqli->query($laQuestionEnSql);
            while ($user = $lesInformations->fetch_assoc())
            {
                $listAuteurs[$user['id']] = $user['alias'];
            }


            /**
             * TRAITEMENT DU FORMULAIRE
             */
            // Etape 1 : vérifier si on est en train d'afficher ou de traiter le formulaire
            // si on recoit un champs email rempli il y a une chance que ce soit un traitement
            $enCoursDeTraitement = isset($_POST['auteur']);
            if ($enCoursDeTraitement)
            {
                // on ne fait ce qui suit que si un formulaire a été soumis.
                // Etape 2: récupérer ce qu'il y a dans le formulaire @todo: c'est là que votre travaille se situe
                // observez le résultat de cette ligne de débug (vous l'effacerez ensuite)
                echo "<pre>" . print_r($_POST, 1) . "</pre>";
                // et complétez le code ci dessous en remplaçant les ???
                $authorId = $_POST['auteur'];
                $postContent = $_POST['message'];
                if (empty($_POST['message'])){
                    echo "Veuillez entrer un message.";
                } else {


                //Etape 3 : Petite sécurité
                // pour éviter les injection sql : https://www.w3schools.com/sql/sql_injection.asp
                $authorId = intval($mysqli->real_escape_string($authorId));
                $postContent = $mysqli->real_escape_string($postContent);
                //Etape 4 : construction de la requete
                $lInstructionSql = "INSERT INTO posts "
                        . "(id, user_id, content, created) "
                        . "VALUES (NULL, "
                        . $authorId . ", "
                        . "'" . $postContent . "', "
                        . "NOW())";
                        
                // echo $lInstructionSql;
                // Etape 5 : execution
                $ok = $mysqli->query($lInstructionSql);
                if ( ! $ok)
                {
                    echo "Impossible d'ajouter le message: " . $mysqli->error;
                } else
                {
                    echo "Message posté en tant que :" . $listAuteurs[$authorId];
                }
                }   
            }

            ?>                     
            <form action="usurpedpost.php" method="post">
                <input type='hidden' name='???' value='achanger'>
                <dl>
                    <dt><label for='auteur'>Author</label></dt>
                    <dd><select name='auteur'>
                            <?php
                            foreach ($listAuteurs as $id => $alias)
                                echo "<option value='$id'>$alias</option>";
                            ?>
                        </select></dd>
                    <dt><label for='message'>Post</label></dt>
                    <dd><textarea name='message'></textarea></dd>
                </dl>
                <input type='submit'>
            </form>               
        </article>
    </main>
</div>

