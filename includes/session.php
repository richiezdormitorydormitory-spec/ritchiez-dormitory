<?php
if(session_status()===PHP_SESSION_NONE) session_start();
function isLoggedIn(){return isset($_SESSION['user_id']);}
function isAdmin(){return isset($_SESSION['role']) && $_SESSION['role']==='admin';}
function requireLogin(){if(!isLoggedIn()){header("Location: /ritchiez_complete_upgrade/auth/login.php");exit;}}
function requireAdmin(){if(!isAdmin()){header("Location: /ritchiez_complete_upgrade/index.php");exit;}}
function e($v){return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');}
?>
