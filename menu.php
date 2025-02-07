<?php
$pdo = new PDO("mysql:host=localhost;dbname=site-restauration", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// take all the menus
$query = $pdo->query("SELECT * FROM menu");
$menus = $query->fetchAll(PDO::FETCH_ASSOC);

// take all the dishes
$plats = [];
foreach ($menus as $menu) {
    $query_plats = $pdo->prepare("SELECT * FROM plat WHERE FIND_IN_SET(id, ?)");
    $query_plats->execute([$menu['plat_menu']]);
    $plats[$menu['id']] = $query_plats->fetchAll(PDO::FETCH_ASSOC);
}

// add a menu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_menu'])) {
    $nom_menu = $_POST['nom_menu'];
    $prix_menu = $_POST['prix_menu'];
    $plats_menu = implode(',', $_POST['plats_menu']);  // Enregistrer les plats sous forme de chaîne séparée par des virgules

    // put the menu in the database
    $stmt = $pdo->prepare("INSERT INTO menu (nom_menu, prix_menu, plat_menu) VALUES (?, ?, ?)");
    $stmt->execute([$nom_menu, $prix_menu, $plats_menu]);

    // redirect after adding the menu to avoid duplication due to page reloading
    header("Location: " . $_SERVER['PHP_SELF']); // Redirect to the same page after adding a menu
    exit(); // Make sure the script stops after the redirection
}

// take all the categories
$query_categories = $pdo->query("SELECT * FROM categorie");
$categories = $query_categories->fetchAll(PDO::FETCH_ASSOC);

// take all the dishes available for the selected category
if (isset($_POST['categorie_menu'])) {
    $categorie_id = $_POST['categorie_menu'];
    $query_plats = $pdo->prepare("
        SELECT plat.id, plat.nom_plat 
        FROM plat
        JOIN categorie_plat ON plat.id = categorie_plat.id_plat
        WHERE categorie_plat.id_categorie = ?");
    $query_plats->execute([$categorie_id]);
    $plats_disponibles = $query_plats->fetchAll(PDO::FETCH_ASSOC);
} else {
    $plats_disponibles = [];
}
// delete a menu
if (isset($_POST['supprimé'])) {
    if (isset($_POST['nom_menu']) and !empty($_POST['nom_menu'])) {
        $nom_menu = htmlspecialchars($_POST['nom_menu']);
        $recupmenu = $pdo->prepare('SELECT * FROM menu WHERE nom_menu = ?');
        $recupmenu->execute(array($nom_menu));

        if ($recupmenu->rowCount() > 0) {
            $deletemenu = $pdo->prepare('DELETE FROM menu WHERE nom_menu = ?');
            $deletemenu->execute(array($nom_menu));
        }
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Restaurant</title>
</head>
<header>
    <a href="index.php" class="text-header"><h2>Déconnection</h2></a>
    <a href="plat.php"><h2 class="text-header">Plat</h2></a>
    <a href="menu.php"><h2 class="text-header">Menu</h2></a>
    <a href="creation.php"><h2 class="text-header">Création</h2></a>

</header>
<body>

<!-- Body -->
<main>
    <!-- Form for adding a new menu -->
    <section class="container-form-menu">
        <div class="form-menu">
            <h2>Ajouter un Nouveau Menu</h2>
            <form method="POST">
                <input type="text" name="nom_menu" placeholder="Nom du menu" required class="champ-index2">
                <input type="number" step="0.01" name="prix_menu" placeholder="Prix" required class="champ-index2">

                <label for="categorie_menu">Choisissez une catégorie :</label>
                <select name="categorie_menu" onchange="this.form.submit()" required class="select-champ-4">
                    <option value="">Sélectionner une catégorie</option>
                    <?php foreach ($categories as $categorie): ?>
                        <option value="<?php echo $categorie['id']; ?>" <?php if (isset($_POST['categorie_menu']) && $_POST['categorie_menu'] == $categorie['id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($categorie['nom_categorie']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <?php if (!empty($plats_disponibles)): ?>
                    <label for="plats_menu">Plats disponibles :</label>
                    <select name="plats_menu[]" multiple required>
                        <?php foreach ($plats_disponibles as $plat): ?>
                            <option value="<?php echo $plat['id']; ?>"><?php echo htmlspecialchars($plat['nom_plat']); ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>

                <button type="submit" name="ajouter_menu">Ajouter Menu</button>
            </form>
        </div>
        <div class="form-menu">
            <form action="" method="post" class="form-menu-delete">
                <h2>Supprimer un menu</h2>
                <label for="nom_menu">Nom du plat</label>
                <select name="nom_menu" class="select-champ-4">
                <option value="">Sélectionner un menu</option>
                    <?php
                    $categories = $pdo->query('SELECT nom_menu FROM menu');
                    while ($categorie = $categories->fetch()) {
                        echo '<option value="' . htmlspecialchars($categorie['nom_menu']) . '">' . htmlspecialchars($categorie['nom_menu']) . '</option>';
                    }
                    ?>
                </select>
                <br>
                <button type="submit" name="supprimé" class="suppr">Supprimer</button>
            </form>
        </div>
    </section>
    <!-- Menu card display -->
    <h2>Menus Disponibles</h2>
    <section class="container-menu">
        <?php foreach ($menus as $menu): ?>
            <div class="menu-card">
                <h2 class="nom_menu"><?php echo htmlspecialchars($menu['nom_menu']); ?></h2>
                <p class="prix_menu">Prix : <?php echo htmlspecialchars($menu['prix_menu']); ?>€</p>

                <!-- Display of dishes associated with the menu -->
                <h4 class="plat">Plats :</h4>
                <ul class="image-nom">
                    <?php foreach ($plats[$menu['id']] as $plat): ?>
                        <li>
                            <h3><strong><?php echo htmlspecialchars($plat['nom_plat']); ?></strong></h3>
                            <img src="<?php echo htmlspecialchars($plat['image_plat']); ?>" alt="<?php echo htmlspecialchars($plat['nom_plat']); ?>" width="200" class="image-plat">
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    </section>
</main>

</body>
<footer>
    <p class="text-footer1">© Touts droits réservé ©</p>
    <p class="text-footer2">Site de restauration</p>
</footer>
</html>
