<?php
$pdo = new PDO('mysql:host=localhost;dbname=site-restauration', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// creating categorie
if (isset($_POST['send'])) {
    if (isset($_POST['nom_categorie']) and !empty($_POST['nom_categorie'])) {
        $nom_categorie = htmlspecialchars($_POST['nom_categorie']);
        $recup_categorie = $pdo->prepare('SELECT * FROM categorie WHERE nom_categorie = ?');
        $recup_categorie->execute(array($nom_categorie));

        if ($recup_categorie->rowCount() > 0) {

        } else {
            $insert_categorie = $pdo->prepare('INSERT INTO `categorie` (`nom_categorie`) VALUES (?)');
            $insert_categorie->execute(array($nom_categorie));

        }
    }
}
// delete categorie
if (isset($_POST['delete'])) {
    if (isset($_POST['nom_categorie']) and !empty($_POST['nom_categorie'])) {
        $nom_categorie = htmlspecialchars($_POST['nom_categorie']);
        $recup_categorie = $pdo->prepare('SELECT * FROM categorie WHERE nom_categorie = ?');
        $recup_categorie->execute(array($nom_categorie));

        if ($recup_categorie->rowCount() > 0) {
            $delete_categorie = $pdo->prepare('DELETE FROM categorie WHERE nom_categorie = ?');
            $delete_categorie->execute(array($nom_categorie));

        }
    }
}




// creating ingredient
if (isset($_POST['send'])) {
    if (isset($_POST['nom_ingredient']) and !empty($_POST['nom_ingredient'])) {
        $nom_ingredient = htmlspecialchars($_POST['nom_ingredient']);
        $recupingredient = $pdo->prepare('SELECT * FROM ingredient WHERE nom_ingredient = ?');
        $recupingredient->execute(array($nom_ingredient));

        if ($recupingredient->rowCount() > 0) {

        } else {
            $insert_ingredient = $pdo->prepare('INSERT INTO `ingredient` (`nom_ingredient`) VALUES (?)');
            $insert_ingredient->execute(array($nom_ingredient));
        }
    }
}
// delete ingredient
if (isset($_POST['supprimé'])) {
    if (isset($_POST['nom_ingredient']) and !empty($_POST['nom_ingredient'])) {
        $nom_ingredient = htmlspecialchars($_POST['nom_ingredient']);
        $recupingredient = $pdo->prepare('SELECT * FROM ingredient WHERE nom_ingredient = ?');
        $recupingredient->execute(array($nom_ingredient));

        if ($recupingredient->rowCount() > 0) {
            $deleteingredient = $pdo->prepare('DELETE FROM ingredient WHERE nom_ingredient = ?');
            $deleteingredient->execute(array($nom_ingredient));
        }
    }
}




// adding a new plat
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_plat'])) {
    $nom_plat = $_POST['nom_plat'];
    $prix_plat = $_POST['prix_plat'];
    $description_plat = $_POST['description_plat'];
    $nom_categorie = $_POST['nom_categorie']; 
    $image_plat = $_POST['image_plat'];

    // add url image
    $stmt = $pdo->prepare("INSERT INTO plat (nom_plat, prix, description_plat, image_plat) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nom_plat, $prix_plat, $description_plat, $image_plat]);

    // take the id of the plat
    $plat_id = $pdo->lastInsertId();

    // take the id of the categorie
    $stmt_categorie = $pdo->prepare("SELECT id FROM categorie WHERE nom_categorie = ?");
    $stmt_categorie->execute([$nom_categorie]);
    $categorie = $stmt_categorie->fetch(PDO::FETCH_ASSOC);

    if ($categorie) {
        // if the category exists, take the id
        $categorie_id = $categorie['id'];
// adding the plat to the category
        $stmt_associer = $pdo->prepare("INSERT INTO categorie_plat (id_plat, id_categorie) VALUES (?, ?)");
        $stmt_associer->execute([$plat_id, $categorie_id]);
    } else {
        // if the category does not exist, display an error message
        echo "La catégorie spécifiée n'existe pas.";
    }

    // redirect after adding the plat to avoid duplication due to page reload
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// take the id of the plat
$query_plats = $pdo->query("SELECT plat.*, GROUP_CONCAT(categorie.nom_categorie) AS categories
                             FROM plat
                             LEFT JOIN categorie_plat ON plat.id = categorie_plat.id_plat
                             LEFT JOIN categorie ON categorie_plat.id_categorie = categorie.id
                             GROUP BY plat.id");
$plats = $query_plats->fetchAll(PDO::FETCH_ASSOC);

// take all categories
$query_categories = $pdo->query("SELECT nom_categorie FROM categorie");
$categories = $query_categories->fetchAll(PDO::FETCH_ASSOC);

    
?>




<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Création</title>
</head>
<header>
    <a href="plat.php"><h2 class="text-header">Plat</h2></a>
    <a href="menu.php"><h2 class="text-header">Menu</h2></a>
    <a href="creation.php"><h2 class="text-header">Création</h2></a>
</header>
<body>


        <!-- add new categorie-->
     <div class="container-form-crea1">
        <form action="" method="post" class="form-categorie-add">
            <h2>Nouvelle catégorie</h2>
            <label for="nom_categorie">Nom de la catégorie</label>
            <input type="text" name="nom_categorie" placeholder="Entrer le nom de la catégorie" autocomplete="off" class="input-champ">
            <br>
            <button type="submit" name="send" class="bouton-creation">Ajouter</button>
        </form>
        <!-- delete categorie -->
        <form action="" method="post" class="form-categorie-delete">
            <h2>Supprimé une catégorie</h2>
            <label for="nom_categorie">Nom de la catégorie</label>
            <select name="nom_categorie" class="select-champ">
                <option value="">Sélectionner une catégorie</option>
                <?php
                $categories = $pdo->query('SELECT nom_categorie FROM categorie');
                while ($categorie = $categories->fetch()) {
                    echo '<option value="' . htmlspecialchars($categorie['nom_categorie']) . '">' . htmlspecialchars($categorie['nom_categorie']) . '</option>';
                }
                ?>
            </select>
            <br>
            <button type="submit" name="delete_menu" class="suppr">delete</button>
        </form>
    </div>


        <!-- add new ingredient -->
    <div class="container-form-crea2">
        <form action="" method="post" class="form-ingredient-add">
            <h2>Nouvelle ingrédient</h2>
            <label for="nom_ingredient">Nom de l'ingrédient</label>
            <input type="text" name="nom_ingredient" placeholder="Entrer le nom de l'ingrédient" autocomplete="off" class="input-champ">
            <br>
            <button type="submit" name="send" class="bouton-creation">Ajouter</button>
        </form>
        <!-- delete ingrédient -->
        <form action="" method="post" class="form-ingredient-delete">
            <h2>Supprimé un ingrédient</h2>
            <label for="nom_ingredient">Nom de l'ingrédient</label>
            <select name="nom_ingredient" class="select-champ">
            <option value="">Sélectionner un ingrédient</option>
                <?php
                $categories = $pdo->query('SELECT nom_ingredient FROM ingredient');
                while ($categorie = $categories->fetch()) {
                    echo '<option value="' . htmlspecialchars($categorie['nom_ingredient']) . '">' . htmlspecialchars($categorie['nom_ingredient']) . '</option>';
                }
                ?>
            </select>
            <br>
            <button type="submit" name="supprimé" class="suppr">Supprimé</button>
        </form>
    </div>


<!-- Form for adding a new dish -->
    <section class="container-form-crea4">
        <form method="POST" class="form-ajout-plat">
            <h2>Ajouter un Nouveau Plat</h2>
            <input type="text" name="nom_plat" placeholder="Nom du plat" required autocomplete="off" class="input-champ-2">
            <input type="number" step="0.01" name="prix_plat" placeholder="Prix du plat" required autocomplete="off" class="input-champ-2">
            <textarea name="description_plat" placeholder="Description du plat" required autocomplete="off" class="input-champ-2"></textarea>

            <!-- Replace the file field with a text field for the image URL -->
            <input type="text" name="image_plat" placeholder="URL de l'image" required autocomplete="off" class="input-champ-2">

            <!-- Select an existing category -->
            <label for="nom_categorie" class="label-categorie">Catégorie :</label>
            <select name="nom_categorie" required class="select-champ-2">
                <option value="">Sélectionner une catégorie</option>
                <?php
                $categories = $pdo->query('SELECT nom_categorie FROM categorie');
                while ($categorie = $categories->fetch()) {
                    echo '<option value="' . htmlspecialchars($categorie['nom_categorie']) . '">' . htmlspecialchars($categorie['nom_categorie']) . '</option>';
                }
                ?>
            </select>

            <button type="submit" name="ajouter_plat" class="bouton-plat">Ajouter Plat</button>
        </form>
        <form action="" method="post" class="form-plat-delete">
            <h2>Supprimé un plat</h2>
            <label for="nom_plat">Nom du plat</label>
            <select name="nom_plat" class="select-champ">
            <option value="">Sélectionner un plat</option>
                <?php
                $categories = $pdo->query('SELECT nom_plat FROM plat');
                while ($categorie = $categories->fetch()) {
                    echo '<option value="' . htmlspecialchars($categorie['nom_plat']) . '">' . htmlspecialchars($categorie['nom_plat']) . '</option>';
                }
                ?>
            </select>
            <br>
            <button type="submit" name="supprimé" class="suppr">Supprimé </button>
        </form>
    </section>
</body>
<footer>
    <p class="text-footer1">© Touts droits réservé ©</p>
    <p class="text-footer2">Site de restauration</p>
</footer>
</html>