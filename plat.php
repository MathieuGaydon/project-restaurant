<?php
$conn = new PDO("mysql:host=localhost;dbname=site-restauration", "root", "");
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// take all the dishes
$query_plats = $conn->query("SELECT plat.*, GROUP_CONCAT(categorie.nom_categorie) AS categories
                             FROM plat
                             LEFT JOIN categorie_plat ON plat.id = categorie_plat.id_plat
                             LEFT JOIN categorie ON categorie_plat.id_categorie = categorie.id
                             GROUP BY plat.id");
$plats = $query_plats->fetchAll(PDO::FETCH_ASSOC);

// take all the categories
$query_categories = $conn->query("SELECT nom_categorie FROM categorie");
$categories = $query_categories->fetchAll(PDO::FETCH_ASSOC);
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
    <!-- Displaying existing dishes -->
    <h2>Plats Disponibles</h2>
    <section class="container-plat">
        <?php if (count($plats) > 0): ?>
            <?php foreach ($plats as $plat): ?>
                <div class="plat">
                    <?php if ($plat['image_plat']): ?>
                        <img src="<?php echo htmlspecialchars($plat['image_plat']); ?>" alt="<?php echo htmlspecialchars($plat['nom_plat']); ?>" class="image-plat" width="200">
                    <?php endif; ?>
                    <div class="nom_menu"><?php echo htmlspecialchars($plat['nom_plat']); ?></div>
                    <div class="image-nom">
                        <p>Prix : <?php echo htmlspecialchars($plat['prix']); ?>€</p>
                        <p>Description : <?php echo htmlspecialchars($plat['description_plat']); ?></p>
                        <p>Catégorie : <?php echo htmlspecialchars($plat['categories']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucun plat disponible pour le moment.</p>
        <?php endif; ?>
    </section>
</main>
</body>
<footer>
    <p class="text-footer1">© Touts droits réservé ©</p>
    <p class="text-footer2">Site de restauration</p>
</footer>
</html>


</body>
</html>
