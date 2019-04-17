<?php
 // INCLUDE ON EVERY TOP-LEVEL PAGE!
include("includes/init.php");
function print_records($records){//printing the one image without <a> tag
    foreach($records as $record){
      $filename= basename($record["name"]);
      $path_parts = pathinfo($filename);
      $ext = strtolower($path_parts["extension"]);
      $citation = "";
      if($record['id'] <= 10){
        $citation = "<figcaption><span>Source: Urael Xu</span></figcaption>"; //cite seed images
      }else{
        $citation = "";
      }
      echo "<div class = img_container><figure><img alt = 'single view image' src= 'uploads/imgs/" .$record["id"].".".$ext."'>".$citation."</figure></div>";
    }
}
$id= filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$params = array(
    ":id" => $id
);
$records = exec_sql_query($db,"SELECT * FROM imgs WHERE id = :id;",$params)->fetchAll(); // getting the single img record for viewing
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include("includes/head.php");?>
  </head>
  <body>
    <div class = "page_container">
      <div class = "head_container">
        <?php include("includes/header.php"); ?>
        <?php include("includes/login.php"); ?>
        <?php echo $taggedmsg; ?>
        <?php
            //list tags on this img
          $imgsid = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
          $tags_params = array(
              ':tag_id' => $imgsid
          );
          $tags_of_this_img_sql = "SELECT DISTINCT tags.id,tags.name FROM img_tags LEFT OUTER JOIN imgs on img_tags.img_id = imgs.id LEFT OUTER JOIN tags on img_tags.tag_id = tags.id WHERE imgs.id = :tag_id;";
          $tags_of_this_img_records = exec_sql_query($db,$tags_of_this_img_sql, $tags_params)->fetchAll(); ?>
          <!-- add tag area -->
          <form action = '#' method = 'post'>
            <input type="hidden" name="name" value="'<?php echo $records[0]['name']?>'">
            <ul id = "tag_list">
            <?php foreach($tags_of_this_img_records as $t){
              if(is_user_logged_in() && $current_user['id'] == $records[0]['user_id']){
                echo "<li>".$t[1]."<button class = 'delete_tag' name = '".$t[1]."' value = '".$t[0]."'>Delete this tag</button></li>"; // only visible if logged in and owns this img
              }
              else{
                echo "<li>".$t[1]."</li>"; // otherwise just display this img's tags
              }
            } ?>

          </ul>
          </form>
      </div>
      <div class = "white">
        <?php print_records($records) ?>
      </div>
      <!-- all photos are created by the club graphic designer Urael Xu -->
      <?php if(is_user_logged_in() && $current_user['id'] == $records[0]['user_id']){ /*can only delete image if logged and owns this image*/?>
        <!-- form for deleting img -->
        <form action="index.php" method="post">
        <!-- send inputs in a post about the img id and name -->
            <input type="hidden" name="id" value="<?php echo $records[0]['id']?>">
            <input type="hidden" name="name" value="<?php echo $records[0]['name']?>">
            <button id = "delete" name = 'delete' value = 'delete'>Delete This Image From Gallery</button>
        </form>
      <?php } ?>
      <!-- return to main page -->
      <form action = "index.php">
        <button id = "return">Return to Main Gallery</button>
      </form>
      <!-- add a tag form -->
      <form id = "add_tag" method = "post" action = "#">
        <label for="tag">Add tag: </label>
        <input id = "tag" type = "text" placeholder = "Tag" name="tag_name">
        <button name = "add_tag" value = "add_tag">Add</button> <!--make sure name is correct later-->
    </form>
      <?php include("includes/footer.php");?>
    </div>

</body>
</html>
