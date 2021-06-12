<?php
// Function definitions
function connectDatabase() {
    $connection = new mysqli("localhost", "root", "", "video_archive_db");

    if ($connection->connect_error) {
        die("Error: ".$connection->connect_error);
    }
    return $connection;
}

function showData($connection, $res){

    echo "<table class='table table-hover'>
                    <thead>
                        <tr>
                          <th scope='col'>primary key</th>
                          <th scope='col'>unique id</th>
                          <th scope='col'>title</th>
                          <th scope='col'>description</th>
                          <th scope='col'>datetime</th>
                          <th scope='col'>download</th>
                        </tr>
                    </thead>
                    <tbody>";


    if ($res == true) {
        if ($res->num_rows > 0) {
            while ($i = $res->fetch_assoc()) {
                echo " <tr>
                          <th scope='row'>" . $i["pk"] . "</th>
                          <td>" . $i["id"] . "</td>
                          <td><a target='blank' rel='noopener noreferrer' href='" . dirname($_SERVER['PHP_SELF']) . "/index.php/?id=" . $i["id"] . "' class='link-primary'>" . $i["title"] . "</a></td>
                          <td>" . $i["description"] . "</td>
                          <td>" . $i["datetime"] . "</td>
                          <td><a target='blank' rel='noopener noreferrer' href='" . dirname($_SERVER['PHP_SELF']) . "/uploads/" . $i["id"] . ".mp4' class='link-primary' download='" . $i["title"] . ".mp4'><i class='fa fa-download'></a></td>
                      </tr>";
            }
        }
    } else {
        echo "Error: " . $connection->error;
    }
    echo "        </tbody>
          </table>";

}

function searchData($connection) {
    $search = $_GET["search"];
    $res = $connection->query("SELECT * FROM videos where LOWER(title) like LOWER('%" . $search . "%') or LOWER(description) like LOWER('%" . $search . "%');");

    if ($res == true) {
        if ($res->num_rows > 0) {

            echo "<div class=\"card text-center\">
                        <div class=\"card-body\">
                        <h5 class=\"card-title\">Search results</h5>
                        <p class=\"card-text\">This are all found videos that contain the search term \"" . $search . "\".</p>";
            showData($connection, $res);
            echo "</div>
                    </div>";
        } else {
            echo "<div class=\"card text-center\">
                        <div class=\"card-body\">
                        <h5 class=\"card-title\">Search results</h5>
                        <p class=\"card-text\">No videos found for the search term \"" . $search . "\".</p>";
            echo "</div";
        }
    } else {
        echo "Error: " . $connection->error;
    }
}

function showVideo($connection, $id) {
    $sql = "SELECT * FROM videos where id='".$id."';";
    $res = $connection->query($sql);

    if ($res == true) {
        if ($res->num_rows > 0) {
            echo "<div class='row'>";
            $i = $res->fetch_assoc();
            echo "<div class='col-xs-12 col-md-10 col-lg-8'>";
            echo "<div class='card h-100'>";
            echo "";
            echo "<video src=\"" . dirname($_SERVER['PHP_SELF']) . "/uploads/" . $i["id"] . ".mp4\" controls autoplay loop muted></video>";
            echo "<div class='card-body''>";
            echo "<h5 class='card-title'>" . $i["title"] ;
            echo " <a target='blank' rel='noopener noreferrer' href='" . dirname($_SERVER['PHP_SELF']) . "/uploads/" . $i["id"] . ".mp4' class='link-primary' download='" . $i["title"] . ".mp4'><i class='fa fa-download'></i></a>". "</h5>";
            echo "<p class='card-text'>" . $i["description"] . "</p>";
            echo "</div>";
            echo "<div class='card-footer'>";
            echo "<small class='text-muted'>Uploaded on " . $i["datetime"] . "</small>";
            echo "</div>";
            echo "</div>";

            echo "</div";
        } else {
            echo "<div class=\"card text-center\">
                        <div class=\"card-body\">
                        <h5 class=\"card-title\">Search results</h5>
                        <p class=\"card-text\">No video has the id \"" . $id . "\".</p>";
            echo "</div";
        }
    } else {
        echo "Error: " . $connection->error;
    }
}

$connection = connectDatabase();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <link rel="stylesheet" href="//use.fontawesome.com/releases/v5.15.3/css/all.css">

    <title>Video Archive</title>
</head>
<body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo "http://".$_SERVER['SERVER_NAME']."/video-archive"; ?>"><i class="fas fa-film" style="color:#0D6EFC;"></i> Video Archive</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal"><i class="fas fa-upload"></i> Upload video</button>
            </ul>
            <form class="d-flex" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="get" enctype="multipart/form-data">
                <input class="form-control me-2" type="search" placeholder="Search for videos" aria-label="Search" name="search">
                <button class="btn btn-outline-primary" type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div>
    </div>
</nav>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">Upload new video</h5>
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
                        <input type="file" class="form-control" id="link" name="link" accept="video/mp4" placeholder="File" aria-describedby="linkHelp" required>
                        <div id="linkHelp" class="form-text">Select a mp4 file.</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="p-5">

<?php
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET["search"])) {
        searchData($connection);
    } else if (isset($_GET["id"])) {
        showVideo($connection, $_GET["id"]);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Generate a unique id for each video
    do {
        $uniqueId = bin2hex(random_bytes(4)) . "-" . bin2hex(random_bytes(4)). "-" . bin2hex(random_bytes(4));
        $res = $connection->query("SELECT * FROM videos WHERE id='".$uniqueId."'");
    } while ($res->num_rows > 0);

    // Get the video data type
    $videoDataType = strtolower(pathinfo("uploads/" . basename($_FILES["link"]["name"]), PATHINFO_EXTENSION));
    // Check if video data type is in the MP4 format
    if ($videoDataType != "mp4") {
        // Display an alert
        echo "<div class='alert alert-warning' role='alert'>
                Error: Wrong data format. Please use mp4 video files.
              </div>";
    } else {
        // Save the file in the folder video-archive/uploads/
        $videoFileName = "uploads/" . basename($uniqueId . "." . $videoDataType);   // Get the new filename
        // SQL query -> Create new entry in database
        $retVal = $connection->query("INSERT INTO videos (id, title, description, datetime) VALUES ('".$uniqueId."', '" . $_POST["title"] . "', '" . $_POST["description"] . "', NOW())");

        if ($retVal === true) {
            // Save the vidoe file
            move_uploaded_file($_FILES["link"]["tmp_name"], $videoFileName);
        } else {
            // Otherwise display an error
            echo "Error: " . $connection->error;
        }
    }
}

?>

<?php
if (!isset($_GET["search"]) AND !isset($_GET["id"])) {
    // Show all videos on home and if search is empty
    $sql = "SELECT * FROM videos";
    $res = $connection->query($sql);

    echo "<div class=\"card text-center\">
                <div class=\"card-body\">
                    <h5 class=\"card-title\">All videos</h5>
                    <p class=\"card-text\">This are all uploaded videos on the platform. Please click on the corresponding title to proceed.</p>";
    showData($connection, $res);
    echo "</div>
       </div>";
}
?>


</div>


<?php
    $connection->close();
?>


    </div>
</div>

</div>


</body>
</html>

