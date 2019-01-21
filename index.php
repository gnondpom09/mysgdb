<?php

    // Connection to database
    require('connexion.php');

    try {

        // create Pays
        $sql = "CREATE TABLE IF NOT EXISTS `pays` (
            `id` INT AUTO_INCREMENT NOT NULL,
            `nom` VARCHAR(80) NOT NULL,
            `code` VARCHAR(2) NOT NULL,
            PRIMARY KEY (`id`)
            ) CHARACTER SET utf8 COLLATE utf8_general_ci;
            ALTER TABLE `pays` ADD `flag` LONGBLOB;
            ";
        $connection->exec($sql);
        // Clean table
        $reset = "TRUNCATE TABLE `pays`";
        //$connection->exec($reset);

        // Querys
        $query   = 'pays';
        $results = $connection->prepare("SELECT * FROM " . $query);
        $insert  = $connection->prepare("INSERT INTO pays (id, nom, code) VALUES (?, ?, ?)");
        $update  = $connection->prepare("UPDATE pays SET flag=? WHERE code=?");

    } catch (PDOException $error) {
        
        echo "Echec des requettes : " . $error->getMessage();

    }

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>MY SGDB</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <?php 

        // Get file name and set options
        $filePath = 'resources/pays.csv';
        $lines = 240;
        $separator = ',';

        // Get event submit
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['submit'])) {
                if (file_exists($filePath)) {
                    // Open file and read content
                    $file = fopen($filePath, 'r');
                    while ($tab = fgetcsv($file, $lines, $separator)) {
                        // Insert values in database
                        $insert->execute($tab);
                        // execute query to get values of database
                        $results->execute();
                    }
                    fclose($file);
                }
            } else if (isset($_POST['reset'])) {
                // Empty database and clean list
                $connection->exec($reset);
                $results = false;
            } else if (isset($_POST['upload'])) {
                // Get directory of flags
                $directory = "./resources/flags";
                $content = opendir($directory) or die('Erreur');
                // display list of flags
                while ($flag = @readdir($content)) {
                    // extract name of flag
                    $code = explode(".", $flag);
                    // update flag
                    $update->execute($flag, $code[0]);   
                }
                closedir($content);  
            }
        }

    ?>

    <section class="container">

        <div class="left col">
            <form action="" method="post" enctype="multipart/form-data">
                <h2>MY SGDB</h2>
                <fieldset>
                    <legend>Base de données</legend>
                    <h4>Actions sur la base</h4>
                    <div class="form-group">
                        <input type="submit" value="Remplir" class="submit" name="submit">
                        <input type="submit" value="Vider" class="reset" name="reset">
                    </div>
                    <div class="fomr-group">
                        <input type="submit" name="upload" value="Ajouter les drapeaux" />
                    </div>
                    <h4 for="">Afficher les drapeaux</h4>
                    <p>
                        <input type="radio" name="flag" value="1"> Oui
                        <input type="radio" name="flag" value="0" checked> Non
                    </p>
                </fieldset>
            </form>

            <div class="debug col">
                <p>
                    <span>DEBUG > </span>
                    <?php
                        // Display arrays for debug
                        if ($results->execute()) {
                            while ($tab = $results->fetch()) {
                                print_r($tab);
                            }
                        }
                    ?>
                </p>
            </div>
        </div>
        
        <div class="right col">
            <table collap>
                <th colspan="4">Pays</th>
                    <?php  
                        // Display results from database
                        if ($results->execute()) :
                            while ($row = $results->fetch()) :
                    ?> 
                                <tr>
                                    <td>
                                        <?= $row['id']; ?>
                                    </td>
                                    <td>
                                        <input type="text" value="<?= $row['nom']; ?>">
                                    </td>
                                    <td>
                                        <input type="text" value="<?= $row['code']; ?>">
                                    </td>
                                    <td width="40" height="40">
                                        <img src="<?= $img = ($row['flag']) ? $row['flag'] : ''; ?>" width="20" height="20" alt="">
                                    </td>
                                </tr>
                    <?php
                            endwhile;
                        endif;
                    ?>
            </table>
        </div>

    </section>

</body>
</html>