<?php
if(isset($_COOKIE['PrivatePageLogin'])){
    // delete cookie
    setcookie('PrivatePageLogin', null, time() - 1);
    // if you use sessions delete session variables as well
}
header('Location: index.php');
?>