<?php
 // INCLUDE ON EVERY TOP-LEVEL PAGE!
include("includes/init.php");
const MAX_FILE_SIZE = 1000000;
function print_records($records,$colname,$colid){//printing records, different function than the one in imp.php, adds the href
  if(isset($records[0])){
    foreach($records as $record){
      $filename= basename($record[$colname]);
      $path_parts = pathinfo($filename);
      $ext = strtolower($path_parts["extension"]);
      $citation = "";
      if($record[$colid] <= 10){
        $citation = "<figcaption><span>Source: Urael Xu</span></figcaption>"; //citing seed images
      }else{
        $citation = "";
      }
      echo "<div class = img_container><figure><a href = 'img.php?".http_build_query(array('id' => $record[$colid]))."'><img alt = 'gallery photo' src= 'uploads/imgs/" . $record[$colid] .".".$ext."'></a>".$citation."</figure></div>";
    }
  }
  else{
    echo "<h2>No images</h2>";
  }
}
$uploaded = FALSE;
if(isset($_POST["insert"]) && is_user_logged_in() && isset($_POST['insert_tag'])) { //  sql logic for where something is inserted after user is logged in
  if($_FILES['img']['error'] === UPLOAD_ERR_OK && trim($_POST['insert_tag']) != ""){
    //$description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $uploadinfo = $_FILES["img"];
    $filename= basename($uploadinfo["name"]);
    $path_parts = pathinfo($filename);
    $ext = strtolower($path_parts["extension"]);
    $params = array(
      ':user_id' => $current_user['id'], // change later when current user exists
      ':file_name' => $filename,
    );
    $sql = "INSERT INTO imgs (name, user_id) VALUES (:file_name,:user_id);";
    exec_sql_query($db, $sql, $params);
    $lid = $db->lastInsertId("id");
    $new_path = "uploads/imgs/".$lid.".".$ext;
    move_uploaded_file($_FILES["img"]["tmp_name"], $new_path);
    $checktagsql = "SELECT * FROM tags WHERE name = :tag"; // see if tag is already in database
    $insert_tag = filter_input(INPUT_POST, 'insert_tag', FILTER_SANITIZE_STRING);
    $params = array(
      ':tag' => $insert_tag
    );
    $checktagrecord = exec_sql_query($db,$checktagsql,$params)->fetchAll();
    if(sizeof($checktagrecord) == 0){// if tag doesnt exist insert new tag
      $newtagsql= "INSERT INTO tags (name) VALUES (:tag);";
      exec_sql_query($db,$newtagsql,$params);
    }
    $gettagidsql = "SELECT id FROM tags WHERE name = :tag;";
    $tagidquery = exec_sql_query($db,$gettagidsql,$params)->fetchAll(); // tag must be in database now guaranteed
    $params = array(
      ':img_id' => $lid,
      ':tag_id' => $tagidquery[0][0]
    );
    exec_sql_query($db,"INSERT INTO img_tags (img_id, tag_id) VALUES (:img_id, :tag_id)",$params);
    $uploaded = TRUE;
    $records = exec_sql_query($db,"SELECT * FROM imgs;")->fetchAll();
  }else{
    $records = exec_sql_query($db,"SELECT * FROM imgs;")->fetchAll();
    $uploaded = FALSE;
  }
}
else{
  $records = exec_sql_query($db,"SELECT * FROM imgs;")->fetchAll();
  $uploaded = FALSE;
}
$columnname = "name";// default param values unless searching for specific tag
$columnid = 'id';
if(isset($_GET['search'])){
  $sql = "SELECT imgs.name,imgs.id FROM img_tags LEFT OUTER JOIN tags ON img_tags.tag_id = tags.id LEFT OUTER JOIN imgs ON img_tags.img_id = imgs.id WHERE tags.name = :name;";
  $search_tag = filter_input(INPUT_GET, 'search_tag', FILTER_SANITIZE_STRING);
  $params = array(
    ':name' => $search_tag
  );
  $records = exec_sql_query($db, $sql, $params)->fetchAll();
  $columnname = "0"; // adjust column for specific tag
  $columnid = "1";
}
// grab records of all tags
$tagsql = "SELECT * FROM tags";
$tags = exec_sql_query($db,$tagsql)->fetchAll();
function print_tags($tags){
  foreach($tags as $tag){
    echo "<option value = '".$tag['name']."'>".$tag['name']."</option>";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
  <?php include("includes/head.php"); ?>
  </head>
  <body>
    <div class = "page_container">
    <div class = "head_container">
      <?php include("includes/header.php"); ?>
      <?php include("includes/login.php"); ?>
    </div>
    <?php /*include("includes/login.php");*/ ?>
    <form id = "search_form" method = "get" action = "index.php">
        <label for = "search_box">Search for tag</label>
        <select id = "search_box" name="search_tag">
          <?php print_tags($tags) ?>
        </select>
        <button id = "search" name = "search" value = "search">Search</button>
    </form>
    <form id = "view_all" action = "index.php">
          <button>View All</button>
    </form>
    <?php foreach($session_messages as $m){ //echo feedback
      echo $m;
    } ?>
    <div class = "white">
      <?php print_records($records, $columnname, $columnid) ?>
    </div>
    <!-- all photos are created by the club graphic designer Urael Xu -->
    <?php if(is_user_logged_in()){ /*can only upload when logged in*/?>
    <div id = "add_photo">
      <h2>Add Photo:</h2>
      <form id = "insert_form" method = "post" action = "index.php" enctype="multipart/form-data">
          <!-- <input id = "name" type = "text" placeholder = "Name (Required)" name="name"> -->
          <label for="img">Upload Photo:</label>
          <input type="hidden" name="MAX_FILE_SIZE" value= '<?php echo MAX_FILE_SIZE;?>' />
          <input id="img" type="file" name="img">
          <input id = "tag" type = "text" placeholder = "Tag(Required)" name="insert_tag"> <!-- may change to a drop down later -->
          <button id = "insert" name = "insert" value = "insert">Upload</button> <!--make sure name is correct later-->
      </form>
    </div>
    <?php } ?>
    </div>
    <?php include("includes/footer.php");?>
</body>
</html>
