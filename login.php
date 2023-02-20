<?php
    include "header.php";
?>

<div id="wrapper" >
    <aside>
        <h2>Description</h2>
        <p>Welcome on our social network</p>
    </aside>
    <main>
        <article>
            <h2>Connexion</h2>
            <?php
            /**
             * TRAITEMENT DU FORMULAIRE
             */
            // Etape 1 : vérifier si on est en train d'afficher ou de traiter le formulaire
            // si on recoit un champs email rempli il y a une chance que ce soit un traitement
            $enCoursDeTraitement = isset($_POST['email']);
            if ($enCoursDeTraitement)
            {
                // on ne fait ce qui suit que si un formulaire a été soumis.
                // Etape 2: récupérer ce qu'il y a dans le formulaire @todo: c'est là que votre travaille se situe
                // observez le résultat de cette ligne de débug (vous l'effacerez ensuite)
                // echo "<pre>" . print_r($_POST, 1) . "</pre>";
                // et complétez le code ci dessous en remplaçant les ???
                $emailAVerifier = $_POST['email'];
                $passwdAVerifier = $_POST['motpasse'];
                if (empty($_POST['email']) || empty($_POST['motpasse'])){
                    echo 'Veuillez entrer une adresse email et un mot de passe valide.';
                } else {

                //Etape 4 : Petite sécurité
                // pour éviter les injection sql : https://www.w3schools.com/sql/sql_injection.asp
                $emailAVerifier = $mysqli->real_escape_string($emailAVerifier);
                $passwdAVerifier = $mysqli->real_escape_string($passwdAVerifier);
                // on crypte le mot de passe pour éviter d'exposer notre utilisatrice en cas d'intrusion dans nos systèmes
                $passwdAVerifier = md5($passwdAVerifier);
                // NB: md5 est pédagogique mais n'est pas recommandée pour une vraies sécurité
                //Etape 5 : construction de la requete
                $lInstructionSql = "SELECT * "
                        . "FROM users "
                        . "WHERE "
                        . "email LIKE '" . $emailAVerifier . "'"
                        ;
                // Etape 6: Vérification de l'utilisateur
                $res = $mysqli->query($lInstructionSql);
                $user = $res->fetch_assoc();
                if ( ! $user OR $user["password"] != $passwdAVerifier)
                {
                    echo "La connexion a échouée. ";
                    
                } else
                {
                    echo "Votre connexion est un succès : " . $user['alias'] . ".";
                    // Etape 7 : Se souvenir que l'utilisateur s'est connecté pour la suite
                    // documentation: https://www.php.net/manual/fr/session.examples.basic.php
                    $_SESSION['connected_id']=$user['id'];
                }
            }
        }
            ?>                     
            <form action="login.php" method="post">
                <input type='hidden'name='???' value='achanger'>
                <dl>
                    <dt><label for='email'>E-Mail</label></dt>
                    <dd><input type='email'name='email'></dd>
                    <dt><label for='motpasse'>Password</label></dt>
                    <dd><input type='password'name='motpasse'></dd>
                </dl>
                <input type='submit'>
            </form>
            <p>
                No account yet ?
                <a href='registration.php'>Sign in.</a>
            </p>

        </article>
    </main>
</div>

