<?php
// vvv DO NOT MODIFY/REMOVE vvv

// check current php version to ensure it meets 2300's requirements
function check_php_version()
{
  if (version_compare(phpversion(), '7.0', '<')) {
    define(VERSION_MESSAGE, "PHP version 7.0 or higher is required for 2300. Make sure you have installed PHP 7 on your computer and have set the correct PHP path in VS Code.");
    echo VERSION_MESSAGE;
    throw VERSION_MESSAGE;
  }
}
check_php_version();

function config_php_errors()
{
  ini_set('display_startup_errors', 1);
  ini_set('display_errors', 0);
  error_reporting(E_ALL);
}
config_php_errors();

// open connection to database
function open_or_init_sqlite_db($db_filename, $init_sql_filename)
{
  if (!file_exists($db_filename)) {
    $db = new PDO('sqlite:' . $db_filename);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (file_exists($init_sql_filename)) {
      $db_init_sql = file_get_contents($init_sql_filename);
      try {
        $result = $db->exec($db_init_sql);
        if ($result) {
          return $db;
        }
      } catch (PDOException $exception) {
        // If we had an error, then the DB did not initialize properly,
        // so let's delete it!
        unlink($db_filename);
        throw $exception;
      }
    } else {
      unlink($db_filename);
    }
  } else {
    $db = new PDO('sqlite:' . $db_filename);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
  }
  return null;
}

function exec_sql_query($db, $sql, $params = array())
{
  $query = $db->prepare($sql);
  if ($query and $query->execute($params)) {
    return $query;
  }
  return null;
}
// ^^^ DO NOT MODIFY/REMOVE ^^^

// You may place any of your code here.

$db = open_or_init_sqlite_db('secure/site.sqlite', 'secure/init.sql');

//code below for logging in and out referenced from lab 8(provided by Instructor)

define('SESSION_COOKIE_DURATION', 3600); // an hour in seconds
$session_messages = array(); // to store error messages and such

function log_in($username, $password){
  global $db;
  global $current_user;
  global $session_messages;// important complete later

  if(isset($username) && isset($password)){
    //check if username is in databse
    $sql = "SELECT * FROM users WHERE username = :username;";
    $params = array(
      ':username' => $username
    );
    $records = exec_sql_query($db,$sql,$params)->fetchAll();
    if($records){
      //username record was found and there can only be one record due to unique constraint
      $account = $records[0];
      //check the password against the hashed one in the db
      if(password_verify($password, $account['password'])){
        //generate session
        $session = session_create_id();

        //store session id cookie thing in database
        $sql = "INSERT INTO sessions (user_id, session) VALUES (:user_id, :session);";
        $params = array(
          ':user_id' => $account['id'],
          ':session' => $session
        );

        $result = exec_sql_query($db, $sql, $params);
        if($result){
          //session was sucessfully stored in db so send the cookie thing back for browser
          setcookie("session", $session, time() + SESSION_COOKIE_DURATION);

          $current_user = $account;
          return $current_user;
        }else{
          array_push($session_messages, "Unfortunately login failed.");
        }
      }else{
        array_push($session_messages, "Username or password is incorrect.");
      }
    }else{
      array_push($session_messages, "Username or password is incorrect.");
    }
  }else{
    array_push($session_messages, "Username or password is blank.");
  }
  $current_user = NULL;
  return NULL;
}

function find_user($user_id){
  global $db;

  $sql = "SELECT * FROM users WHERE id = :user_id;";
  $params = array(
    ':user_id' => $user_id
  );
  $records = exec_sql_query($db,$sql, $params)->fetchAll();
  if($records) {
    //should only be one record bc users are unique
    return $records[0];
  }
  return NULL;
}

function find_session($session){
  global $db;

  if(isset($session)){
    $sql = "SELECT * FROM sessions WHERE session = :session;";
    $params = array(
      ':session' => $session
    );
    $records = exec_sql_query($db, $sql, $params)->fetchAll();
    if($records) {
      //should only be one record
      return $records[0];
    }
  }
  return NULL;
}

function session_login(){
  global $current_user;

  if(isset($_COOKIE['session'])) {
    $session = $_COOKIE['session'];

    $session_record = find_session($session);

    if(isset($session_record)){
      $current_user = find_user($session_record['user_id']);

      //renew the cookie again for an hour when user does something like change page
      setcookie("session", $session, time() + SESSION_COOKIE_DURATION);

      return $current_user;
    }
  }
  //otherwise user is null
  $current_user = NULL;
  return NULL;
}
function is_user_logged_in() {
  global $current_user;

  // if $current_user is not NULL, then someone is logged in
  return ($current_user != NULL);
}

function log_out(){
  global $current_user;
  global $db;

  //subteact time from the current bc then time must have passed. otherwise youre a time traveler
  $session = $_COOKIE['session'];

  setcookie('session', '', time() - 1000);
  exec_sql_query($db, "DELETE FROM sessions WHERE session = :session;", array( ':session' => $session));
  $current_user = NULL;
}

// check for login or logout requests, or check to allow user to stay logged in

if (isset($_POST['login']) && isset($_POST['username']) && isset($_POST['password'])){

  $username = trim($_POST['username']);
  $password = trim($_POST['password']);

  log_in($username, $password);
}
else{
  // check if we are already logged in
  session_login();
}

// Check to see if we need to log out the user
if(isset($current_user) && (isset($_GET['logout']) || isset($_POST['logout']))){
  log_out();
}

//delete image
if(isset($_POST['delete']) && isset($_POST['id']) && isset($_POST['name'])){
  $filename= basename($_POST["name"]);
  $path_parts = pathinfo($filename);
  $ext = strtolower($path_parts["extension"]);
  $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
  $params = array(
    ':id' => $id
  );
  exec_sql_query($db, "DELETE FROM imgs WHERE id = :id;", $params);//delete record from img table
  unlink('uploads/imgs/'.$id.".".$ext); // remove from uploads/imgs
  exec_sql_query($db, "DELETE FROM img_tags WHERE img_id = :id;", $params); //delete record from img tags
}

//delete tag from image

$imgsid = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
$tags_params = array(
    ':tag_id' => $imgsid
);
$tags_of_this_img_sql = "SELECT DISTINCT tags.id, tags.name FROM img_tags LEFT OUTER JOIN imgs on img_tags.img_id = imgs.id LEFT OUTER JOIN tags on img_tags.tag_id = tags.id WHERE imgs.id = :tag_id;";
$tags_of_this_img_records = exec_sql_query($db,$tags_of_this_img_sql, $tags_params)->fetchAll();
foreach($tags_of_this_img_records as $t){
  if(isset($_POST[$t[1]])){
    $tag_id = filter_input(INPUT_POST, $t[1], FILTER_VALIDATE_INT);
    $img_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $params = array(
      ':img_id' => $img_id,
      ':tag_id' => $tag_id
    );
    $sql = "DELETE FROM img_tags WHERE img_id = :img_id AND tag_id = :tag_id;";
    exec_sql_query($db, $sql, $params);
  }
}
// add tag
$taggedmsg = "";
if(isset($_POST['add_tag']) && trim($_POST['tag_name']) != ""){
  $add_tag = filter_input(INPUT_POST, 'tag_name', FILTER_SANITIZE_STRING);
  $params = array(
    ':tag' => $add_tag
  );
  $checktagsql = "SELECT * FROM tags WHERE name = :tag"; // see if tag is already in database
  $checktagrecord = exec_sql_query($db,$checktagsql,$params)->fetchAll();
  if(sizeof($checktagrecord) == 0){// if tag doesnt exist insert new tag
    $newtagsql= "INSERT INTO tags (name) VALUES (:tag);";
    exec_sql_query($db,$newtagsql,$params);
    $tagid = $db->lastInsertId(); //grab tag id if jsut inserted
  }else{//get the existing tag's id
    $tagid = $checktagrecord[0]['id'];
  }
  $imgsid = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
  $params = array(
    ':tag' => $tagid,
    ':img' => $imgsid
  );
  $checkiftagged = exec_sql_query($db,"SELECT id FROM img_tags WHERE img_id = :img AND tag_id =:tag;",$params)->fetchAll(); //check if img already has tag
  if(sizeof($checkiftagged) == 0){
    exec_sql_query($db, "INSERT INTO img_tags (img_id, tag_id) VALUES (:img,:tag);",$params); // if not, tag it
  }else{
    $taggedmsg = "<h3>Tag already is on this image</h3>"; // tell user tag exists
  }

}else if(isset($_POST['add_tag']) && trim($_POST['tag_name']) == ""){
  $taggedmsg = "<h3>No tag was entered</h3>"; // if spaces or nothing was entered for a tag
}



?>
