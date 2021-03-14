<?php
/*** UNCOMMENT THIS LINE TO DISPLAY ALL PHP ERRORS ***/
error_reporting(E_ALL);

init();

function authenticate()
{
   $username = htmlspecialchars($_POST["username"]);
   $password = htmlspecialchars($_POST["password"]);

   $user = get_user_by('login', $username);

   /*** COMPARE FORM PASSWORD WITH WORDPRESS PASSWORD ***/
   if(!wp_check_password($password, $user->data->user_pass, $user->ID)):
      return false;
   endif;

   wp_set_current_user($user->ID, $username);

   /*** SET PERMANENT COOKIE IF NEEDED ***/
   if(isset($_POST["remember"]) && ($_POST["remember"] == "1"))
      wp_set_auth_cookie($user->ID, true);
   else
      wp_set_auth_cookie($user->ID);
		$_SESSION['userName'] = $user->user_nicename;
        $_SESSION['cap'] = $user->cap;
   /*** REDIRECT USER TO PREVIOUS PAGE ***/
   if(isset($_SESSION["return_to"])):
      $url = $_SESSION["return_to"];
      unset($_SESSION["return_to"]);
      header("location: $url");
   else:
      header("location: /program/");
   endif;
}

function login()
{
   if(!is_user_logged_in()):

      /*** REMEMBER THE PAGE TO RETURN TO ONCE LOGGED IN ***/
      $_SESSION["return_to"] = $_SERVER['REQUEST_URI'];

      /*** REDIRECT TO LOGIN PAGE ***/
      header("location: /program/login.php");

   endif;
}

function init()
{
   /*** INITIATING PHP SESSION ***/
   if(!session_id())
      session_start();

   /*** LOADING WORDPRESS LIBRARIES ***/
   define('WP_USE_THEMES', false);
   include(dirname(__FILE__)."/../projects/wp-load.php");
   //require_once("../projects/wp-load.php");
}

?>