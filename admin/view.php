<?php
    // Inclusion du fichier de connexion à la base de données
    require 'database.php';

    // Vérifie si un identifiant a été passé en paramètre GET
    if (!empty($_GET['id'])) {
        // Utilise la fonction checkInput pour sécuriser l'entrée
        $id = checkInput($_GET['id']);
    }
     
    // Connexion à la base de données
    $db = Database::connect();
    // Préparation de la requête SQL pour récupérer les détails de l'élément spécifié par l'id
    $statement = $db->prepare("SELECT items.id, items.name, items.description, items.price, items.image, categories.name AS category 
                               FROM items 
                               LEFT JOIN categories ON items.category = categories.id 
                               WHERE items.id = ?");
    // Exécution de la requête avec l'id comme paramètre
    $statement->execute(array($id));
    // Récupère les résultats de la requête
    $item = $statement->fetch();
    // Déconnexion de la base de données
    Database::disconnect();

    // Fonction pour nettoyer et sécuriser les données d'entrée
    function checkInput($data) {
        $data = trim($data); // Supprime les espaces en début et fin de chaîne
        $data = stripslashes($data); // Supprime les antislashes
        $data = htmlspecialchars($data); // Convertit les caractères spéciaux en entités HTML
        return $data;
    }
?>


<!DOCTYPE html>
<html>
    <head>
      <title>Burger Code</title>
      <meta charset="utf-8"/>
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet">
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"></script>
      <link href='http://fonts.googleapis.com/css?family=Holtwood+One+SC' rel='stylesheet' type='text/css'>
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
      <link rel="stylesheet" href="..\css\style.css">
    </head>
    
    <body>
      <h1 class="text-logo"><span class="bi-shop"></span> Burger Code <span class="bi-shop"></span></h1>
      <div class="container admin">
        <div class="row">
          <div class="col-md-6">
            <h1><strong>Voir un item</strong></h1>
            <br>
            <form>
              <div>
                <label>Nom:</label><?php echo '  '.$item['name'];?>
              </div>
              <br>
              <div>
                <label>Description:</label><?php echo '  '.$item['description'];?>
              </div>
              <br>
              <div>
                <label>Prix:</label><?php echo '  '.number_format((float)$item['price'], 2, '.', ''). ' €';?>
              </div>
              <br>
              <div>
                <label>Catégorie:</label><?php echo '  '.$item['category'];?>
              </div>
              <br>
              <div>
                <label>Image:</label><?php echo '  '.$item['image'];?>
              </div>
            </form>
            <br>
            <div class="form-actions">
              <a class="btn btn-primary" href="index.php"><span class="bi-arrow-left"></span> Retour</a>
            </div>
          </div>
          <div class="col-md-6 site">
            <div class="img-thumbnail">
              <img src="<?php echo '../images/'.$item['image'];?>" alt="...">
              <div class="price"><?php echo number_format((float)$item['price'], 2, '.', ''). ' €';?></div>
              <div class="caption">
                <h4><?php echo $item['name'];?></h4>
                <p><?php echo $item['description'];?></p>
                <a href="#" class="btn btn-order" role="button"><span class="bi-cart-fill"></span> Commander</a>
              </div>
            </div>
          </div>
        </div>
      </div>   
    </body>
</html>

