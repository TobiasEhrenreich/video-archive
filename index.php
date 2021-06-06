<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Video Archive</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="navbar"><a class="navbar" href="index.php">Video Archive</a></div>

<h2>Upload new video</h2>
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post" enctype="multipart/form-data">
    Title: <input type="text" name="title"><br>
    Description: <input type="text" name="description"><br>
    Video link: <input type="file" name="link" ccept="image/png, image/jpeg"><br>
    <input type="submit">
</form>

<?php
$servername = "localhost";
$user = "root";
$pw = "";
$db = "sensitivedaten";

$con = new mysqli($servername, $user, $pw, $db);

if ($con->connect_error) {
    die("Ein Verbindungsfehler ist aufgetreten. ".$con->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST["title"];
    $description = $_POST["description"];
    $link = $_FILES["link"]["name"];


    $sql = "INSERT INTO videos (title, description, link, datetime) VALUES ('".$title."', '".$description."', '".$link."', NOW())";
    $res = $con->query($sql);

    if ($res === true) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["link"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        if(isset($_POST["submit"])) {
            $check = getimagesize($_FILES["link"]["tmp_name"]);
            if($check !== false) {
                echo "File is an image - " . $check["mime"] . ".";
                $uploadOk = 1;
            } else {
                echo "File is not an image.";
                $uploadOk = 0;
            }
        }

        // Check if file already exists
        if (file_exists($target_file)) {
            echo "Sorry, file already exists.";
            $uploadOk = 0;
        }

        // Check file size
        if ($_FILES["link"]["size"] > 500000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif" ) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["link"]["tmp_name"], $target_file)) {
                echo "The file ". htmlspecialchars( basename( $_FILES["link"]["name"])). " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    } else {
        echo "Du bist ein Lappen. " . $con->error;
    }

}

?>

<h2>All Videos</h2>

<?php
//$sql = "INSERT INTO user (Name, Passwort) VALUES ('Bruce Wayne', 'I like Bats')";
$sql = "SELECT * FROM videos";
$res = $con->query($sql);

if ($res->num_rows > 0) {
    while ($i = $res->fetch_assoc()) {
        echo "id: " . $i["id"] . ", Title: " . $i["title"]  . ", Description: " . $i["description"] . ", Link: " . $i["link"] . ", Datetime: " . $i["datetime"];
        $link = $i["link"];
        $dir = getcwd();
        echo "<img src=\"".getcwd()."/uploads/".$link."\">";
        echo "<br>";
    }
} else {
    echo "Du bist ein Lappen. " . $con->error;
}

$con->close();
?>

<br>





</body>
</html>

