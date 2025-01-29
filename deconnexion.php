<?php
session_start();
if(session_unset() && session_destroy()){
    session_gc(); #garbage collection => nettoyer le cache
    session_register_shutdown(); # => fermer la session
    header("Location: login.php");
    exit; 
}
?>