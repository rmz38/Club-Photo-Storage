<div class = header_box>
<?php
echo "<h1>Esports at Cornell Photo Gallery</h1>";
// below are feedback messages and other messages
if($uploaded){ //after upload
    echo "<h2>Uploaded image successfully</h2>";
}
else if($_SERVER['PHP_SELF'] == "/img.php"){ // viewing single image message
    echo "<h2>Viewing this image with tags:</h2>";
}
if(isset($_POST['delete'])){ // afer delete
    echo "<h2>Image sucessfully deleted</h2>";
}
if(isset($_GET['search_tag'])){
    echo "<h2>Search results for ".$_GET['search_tag']." tag:</h2>";
}else if($_SERVER['PHP_SELF'] == "/index.php"){ // viewing all img on main page
    echo "<h2>Viewing all images:</h2>";
}
?>
</div>
