<?php
//code below for login form was provided by instructor and referenced from lab 8
    if ( is_user_logged_in() ) {
        // Add a logout query string parameter
        $logout_url = htmlspecialchars( $_SERVER['PHP_SELF'] ) . '?' . http_build_query( array( 'logout' => '' ) );
        if($_SERVER['PHP_SELF'] == "/img.php" && isset($_GET['id'])){
            $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            $logout_url = "img.php?logout=&id=".$id;
        }
        echo '<div id="sign_out"><a id = "signout_link" href="' . $logout_url . '">Sign Out ' . htmlspecialchars($current_user['username']) . '</a></div>';
    }else{ // if not logged in and viewing the img page
        if(isset($_GET['id']) && $_SERVER['PHP_SELF'] == "/img.php"){
            $idparam = "?id=".$id;
        }else{
            $id = "";
        }
?>
<!-- form for login -->
    <div class = "login_container">
    <form id = "login" action = "<?php echo htmlspecialchars($_SERVER['PHP_SELF'] ).$idparam;?>" method = "post">
        <ul id = "login_list">
            <li>
                <label for = "username">Username:</label>
                <input id = "username" type = "text" name="username" />
            </li>
            <li>
                <label for = "password">Password:</label>
                <input id = "password" type = "password" name = "password" />
            </li>
            <li>
                <button name = "login" type = "submit" value = "login"> Sign In</button>
            </li>
        </ul>
    </form>
    </div>
<?php } ?>
