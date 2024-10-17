<?php
// Inclusion du fichier de connexion à la base de données
require 'database.php';

// Vérification si un 'id' est passé via l'URL (méthode GET)
if(!empty($_GET['id'])) {
    // Sécurisation de l'entrée utilisateur avec la fonction checkInput
    $id = checkInput($_GET['id']);
}

// Initialisation des variables pour gérer les erreurs et les champs du formulaire
$nameError = $descriptionError = $priceError = $categoryError = $imageError = $name = $description = $price = $category = $image = "";

// Vérification si le formulaire a été soumis (via POST)
if(!empty($_POST)) {
    // Récupération et sécurisation des données du formulaire
    $name               = checkInput($_POST['name']);
    $description        = checkInput($_POST['description']);
    $price              = checkInput($_POST['price']);
    $category           = checkInput($_POST['category']); 
    $image              = checkInput($_FILES["image"]["name"]); // Image téléchargée
    $imagePath          = '../images/' . basename($image);      // Chemin où l'image sera sauvegardée
    $imageExtension     = pathinfo($imagePath, PATHINFO_EXTENSION); // Récupération de l'extension de l'image
    $isSuccess          = true; // Variable qui vérifie si toutes les étapes du processus se passent bien

    // Validation des champs du formulaire
    if(empty($name)) {
        $nameError = 'Ce champ ne peut pas être vide';
        $isSuccess = false;
    }
    if(empty($description)) {
        $descriptionError = 'Ce champ ne peut pas être vide';
        $isSuccess = false;
    } 
    if(empty($price)) {
        $priceError = 'Ce champ ne peut pas être vide';
        $isSuccess = false;
    } 
    if(empty($category)) {
        $categoryError = 'Ce champ ne peut pas être vide';
        $isSuccess = false;
    }

    // Gestion de l'image : si une image est téléchargée
    if(empty($image)) {
        $isImageUpdated = false; // Si aucune nouvelle image n'a été téléchargée
    } else {
        $isImageUpdated = true;
        $isUploadSuccess = true;

        // Validation de l'extension de l'image
        if($imageExtension != "jpg" && $imageExtension != "png" && $imageExtension != "jpeg" && $imageExtension != "gif") {
            $imageError = "Les fichiers autorisés sont: .jpg, .jpeg, .png, .gif";
            $isUploadSuccess = false;
        }

        // Vérification si l'image existe déjà
        if(file_exists($imagePath)) {
            $imageError = "Le fichier existe déjà";
            $isUploadSuccess = false;
        }

        // Vérification de la taille de l'image
        if($_FILES["image"]["size"] > 500000) {
            $imageError = "Le fichier ne doit pas dépasser les 500KB";
            $isUploadSuccess = false;
        }

        // Si toutes les validations sont passées, on tente de déplacer l'image dans le répertoire cible
        if($isUploadSuccess) {
            if(!move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath)) {
                $imageError = "Il y a eu une erreur lors de l'upload";
                $isUploadSuccess = false;
            }
        }
    }

    // Si le formulaire est valide, avec ou sans mise à jour de l'image
    if(($isSuccess && $isImageUpdated && $isUploadSuccess) || ($isSuccess && !$isImageUpdated)) {
        $db = Database::connect();

        // Préparation de la requête SQL pour mettre à jour l'item, avec ou sans image
        if($isImageUpdated) {
            $statement = $db->prepare("UPDATE items SET name=?, description=?, price=?, category=?, image=? WHERE id=?");
            $statement->execute(array($name, $description, $price, $category, $image, $id));
        } else {
            $statement = $db->prepare("UPDATE items SET name=?, description=?, price=?, category=? WHERE id=?");
            $statement->execute(array($name, $description, $price, $category, $id));
        }
        Database::disconnect();

        // Redirection vers la page d'accueil après mise à jour
        header("Location: index.php");

    // Si l'image a échoué à être téléchargée, on récupère l'ancienne image
    } else if ($isImageUpdated && !$isUploadSuccess) {
        $db = Database::connect();
        $statement = $db->prepare("SELECT image FROM items WHERE id=?");
        $statement->execute(array($id));
        $item = $statement->fetch();
        $image = $item['image']; // On récupère l'ancienne image
        Database::disconnect();
    }

} else {
    // Si le formulaire n'a pas été soumis, on récupère les données de l'item à partir de l'ID
    $db = Database::connect();
    $statement = $db->prepare("SELECT * FROM items WHERE id=?");
    $statement->execute(array($id));
    $item = $statement->fetch();
    $name           = $item['name'];
    $description    = $item['description'];
    $price          = $item['price'];
    $category       = $item['category'];
    $image          = $item['image'];
    Database::disconnect();
}

// Fonction pour sécuriser les entrées utilisateur
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
            <div class="col-sm-6">
                <h1><strong>Modifier un item</strong></h1>
                <br>
                <form class="form" role="form" action ="<?php echo 'update.php?id='.$id;?>" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name">Nom:</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Nom" value="<?php echo $name;?>">
                        <span class="help-inline"><?php echo $nameError;?></span>
                    </div>
                    <br>
                    <div class="form-group">
                        <label for="description">Description:</label>
                        <input type="text" class="form-control" id="description" name="description" placeholder="Description" value="<?php echo $description;?>">
                        <span class="help-inline"><?php echo $descriptionError;?></span>
                    </div>
                    <br>
                    <div class="form-group">
                        <label for="price">Prix: (en €)</label>
                        <input type="number" step ="0.01"class="form-control" id="price" name="price" placeholder="Prix" value="<?php echo $price;?>">
                        <span class="help-inline"><?php echo $priceError;?></span>
                    </div>
                    <br>
                    <div class="form-group">
                        <label for="category">Catégories: </label>
                    <select class="form-control" id="category" name="category">
                            <?php 
                                $db=Database::connect();
                                foreach($db->query('SELECT * FROM categories') as $row){
                                    if($row['id']==$category)
                                        echo '<option selected="selected" value="'.$row['id']. '">' .$row['name'].'<option>';
                                    else
                                        echo '<option value="'.$row['id']. '">' .$row['name'].'<option>';  
                                }
                                Database::disconnect();
                            ?>
                    </select>
                    <br>
                        <div class="form-group">
                            <label>Image:</label>
                            <p><?php echo $image?></p>
                            <label for="image">Sélectionner une image:</label>
                            <input type="file" id="image" name="image">
                            <span class="help-inline"><?php echo $imageError;?></span>
                        </div>
                        <span class="help-inline"><?php echo $priceError;?></span>
                    </div>
                
                <br>
                <div class="form-actions">
                    <button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-pencil"></span>Ajouter</button>
                    <a class="btn btn-primary" href="index.php"><span class="bi-arrow-left"></span> Retour</a>
                </div>
                </form>
            </div>
            <div class="col-md-6 site">
                    <div class="img-thumbnail">
                        <img src="<?php echo '../images/'.$image;?>" alt="...">
                        <div class="price"><?php echo number_format((float)$price, 2, '.', ''). ' €';?></div>
                          <div class="caption">
                            <h4><?php echo $name;?></h4>
                            <p><?php echo $description;?></p>
                            <a href="#" class="btn btn-order" role="button"><span class="bi-cart-fill"></span> Commander</a>
                          </div>
                    </div>
                </div>
        </div>
      </div>   
    </body>
</html>

