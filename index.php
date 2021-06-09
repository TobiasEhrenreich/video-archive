<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <link rel="stylesheet" href="//use.fontawesome.com/releases/v5.15.3/css/all.css">
    <!-- Own Css -->
    <link rel="stylesheet" href="<?php echo "http://".$_SERVER['SERVER_NAME']."/video-archive"; ?>/style.css">

    <title>Video Archive</title>
</head>
<body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo "http://".$_SERVER['SERVER_NAME']."/video-archive"; ?>">Video Archive</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                    Upload video
                </button>
            </ul>
            <form class="d-flex" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="get" enctype="multipart/form-data">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" name="search">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form>
        </div>
    </div>
</nav>

<!-- Button trigger modal -->


<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Upload new video</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="title" class="form-label">Video title</label>
                        <input type="text" class="form-control" id="title" name="title" aria-describedby="titleHelp" placeholder="Title" required>
                        <div id="titleHelp" class="form-text">Set your video title.</div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" placeholder="Description" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="link">Video file</label>
                        <input type="file" class="form-control" id="link" name="link" accept="video/mp4, video/flv" placeholder="File" aria-describedby="linkHelp" required>
                        <div id="linkHelp" class="form-text">Select a mp4 or flv file.</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="p-5">

<?php
$search = null;
$servername = "localhost";
$user = "root";
$pw = "";
$db = "sensitivedaten";

$con = new mysqli($servername, $user, $pw, $db);

if ($con->connect_error) {
    die("Ein Verbindungsfehler ist aufgetreten. ".$con->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET["search"])) {
        $search = $_GET["search"];
        $sql = "SELECT * FROM videos where LOWER(title) like LOWER('%" . $search . "%') or LOWER(description) like LOWER('%" . $search . "%');";
        $res = $con->query($sql);

        if ($res == true) {
            if ($res->num_rows > 0) {
                echo "<div class=\"card text-center\">
                        <div class=\"card-body\">
                        <h5 class=\"card-title\">Search results</h5>
                        <p class=\"card-text\">This are all found videos that contain the search term \"" . $search . "\".</p>";
                echo "<div class='row row-cols-1 row-cols-md-3 g-4'>";
                while ($i = $res->fetch_assoc()) {
                    echo "<div class='col'>";
                    echo "<div class='card h-100'>";
                    echo "";
                    echo "<video src=\"" . dirname($_SERVER['PHP_SELF']) . "/uploads/" . $i["id"] . "." . $i["type"] . "\" controls autoplay loop muted></video>";
                    echo "<div class='card-body''>";
                    echo "<a href='".dirname($_SERVER['PHP_SELF'])."/index.php/?id=".$i["id"]."' class='link-primary'><h5 class='card-title'>".$i["title"]."</h5></a>";
                    echo "<p class='card-text'>" . $i["description"] . "</p>";
                    echo "</div>";
                    echo "<div class='card-footer'>";
                    echo "<small class='text-muted'>Uploaded on " . $i["datetime"] . "</small>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                }
                echo "</div";
                echo "</div";
            } else {
                echo "<div class=\"card text-center\">
                        <div class=\"card-body\">
                        <h5 class=\"card-title\">Search results</h5>
                        <p class=\"card-text\">No videos found for the search term \"" . $search . "\".</p>";
                echo "</div";
            }
        } else {
            echo "Du bist ein Lappen. " . $con->error;
        }
    } else if (isset($_GET["id"])) {
        $sql = "SELECT * FROM videos where id='".$_GET["id"]."';";
        $res = $con->query($sql);

        if ($res == true) {
            if ($res->num_rows > 0) {
                echo "<div class='row'>";
                while ($i = $res->fetch_assoc()) {
                    echo "<div class='col'>";
                    echo "<div class='card h-100'>";
                    echo "";
                    echo "<video src=\"" . dirname($_SERVER['PHP_SELF']) . "/uploads/" . $i["id"] . "." . $i["type"] . "\" controls autoplay loop muted></video>";
                    echo "<div class='card-body''>";
                    echo "<h5 class='card-title'>" . $i["title"] . "</h5>";
                    echo "<p class='card-text'>" . $i["description"] . "</p>";
                    echo "</div>";
                    echo "<div class='card-footer'>";
                    echo "<small class='text-muted'>Uploaded on " . $i["datetime"] . "</small>";
                    echo "</div>";
                    echo "</div>";
                }
                echo "</div";
            } else {
                echo "<div class=\"card text-center\">
                        <div class=\"card-body\">
                        <h5 class=\"card-title\">Search results</h5>
                        <p class=\"card-text\">No videos found for the search term \"" . $search . "\".</p>";
                echo "</div";
            }
        } else {
            echo "Du bist ein Lappen. " . $con->error;
        }
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $title = $_POST["title"];
        $description = $_POST["description"];
        $link = $_FILES["link"]["name"];


        $type = strtolower(pathinfo($_FILES["link"]["name"], PATHINFO_EXTENSION));

//    $sql = "INSERT INTO videos (uid, title, description, link, datetime) VALUES (6, ".$title."', '".$description."', '".$link."', NOW())";
        $sql = "INSERT INTO videos (id, title, description, type, datetime) VALUES (UUID(), \"" . $title . "\", \"" . $description . "\", \"" . $type . "\", NOW())";
        $res = $con->query($sql);

        if ($res === true) {
            $sql = "SELECT id FROM videos WHERE pk=" . $con->insert_id;
            $res = $con->query($sql);

            if ($res->num_rows == 1) {
                while ($i = $res->fetch_assoc()) {
                    $link = $i["id"];
                }
            } else {
                echo "Du bist ein Lappen.2 " . $con->error;
            }

            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["link"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $target_file = $target_dir . basename($link . "." . $imageFileType);

            // Check if image file is a actual image or fake image
//        if(isset($_POST["submit"])) {
//            $check = getimagesize($_FILES["link"]["tmp_name"]);
//            if($check !== false) {
//                echo "File is an image - " . $check["mime"] . ".";
//                $uploadOk = 1;
//            } else {
//                echo "File is not an image.";
//                $uploadOk = 0;
//            }
//        }

            // Check if file already exists
            if (file_exists($target_file)) {
                echo "Sorry, file already exists.";
                $uploadOk = 0;
            }

            // Check file size
//            if ($_FILES["link"]["size"] > 10000000) {
//                echo "Sorry, your file is too large.";
//                $uploadOk = 0;
//            }
            // Allow certain file formats
            if ($imageFileType != "mp4" && $imageFileType != "flv") {
                echo "Sorry, only MP4 and FLV files are allowed.";
                $uploadOk = 0;
            }

            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                echo "Sorry, your file was not uploaded.";
                // if everything is ok, try to upload file
            } else {
                if (move_uploaded_file($_FILES["link"]["tmp_name"], $target_file)) {
                    echo "The file " . htmlspecialchars(basename($_FILES["link"]["name"])) . " has been uploaded.";
                } else {
                    echo "Sorry, there was an error uploading your file.";
                }
            }
        } else {
            echo "Du bist ein Lappen. " . $con->error;
        }
}

?>
<br>
<br>



    <?php
    if (!isset($_GET["search"]) AND !isset($_GET["id"])) {
        echo "<div class=\"card text-center\">
                <div class=\"card-body\">
                    <h5 class=\"card-title\">All videos</h5>
                    <p class=\"card-text\">This are all uploaded videos on the platform.</p>";

                    echo "<div class='row row-cols-1 row-cols-md-3 g-4'>";

                    $sql = "SELECT * FROM videos";
                    $res = $con->query($sql);

                    if ($res == true) {
                        if ($res->num_rows > 0) {
                            while ($i = $res->fetch_assoc()) {
                                echo "<div class='col'>";
                                    echo "<div class='card h-100'>";
                                        echo "";
                                        echo "<video src=\"" . dirname($_SERVER['PHP_SELF']) . "/uploads/" . $i["id"] . "." . $i["type"] . "\" controls autoplay loop muted></video>";
                                        echo "<div class='card-body''>";
                                            echo "<form action=".$_SERVER["PHP_SELF"]." method='delete' enctype='multipart/form-data'>";
                                                echo "<grid>";
                                                    echo "<div class='justify-content-center row'>";
                                                        echo "<div class='col col-sm-1'>";
                                                        echo "</div>";
                                                        echo "<div class='col col-sm-10'>";
                                                            echo "<a target='blank' rel='noopener noreferrer' href='".dirname($_SERVER['PHP_SELF'])."/index.php/?id=".$i["id"]."' class='link-primary'><h5 class='card-title'>".$i["title"]."</h5></a>";
                                                        echo "</div>";
                                                        echo "<div class='col col-sm-1 justify-content-right'>";
                                                            echo "<button id='trashVideo' type='submit' style='border: none; background-color: transparent;' <i class='fa fa-trash'></i></button>";
                                                        echo "</div>";
                                                    echo "</div>";
                                                    echo "<div class='row'>";
                                                         echo "<p class='card-text'>".$i["description"]."</p>";
                                                    echo "</div>";
                                                echo "</grid>";
                                            echo "</form>";

                                        echo "</div>";
                                        echo "<div class='card-footer'>";
                                            echo "<small class='text-muted'>Uploaded on ".$i["datetime"]."</small>";
                                        echo "</div>";
                                    echo "</div>";
                                echo "</div>";

                            }
                        }
                    } else {
                        echo "Du bist ein Lappen. " . $con->error;
                    }
                    echo "</div>";
        echo "</div>";
    }

    $con->close();
    ?>


    </div>
</div>

</div>


</body>
</html>

