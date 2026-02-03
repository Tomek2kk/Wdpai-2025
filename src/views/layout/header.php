<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>AutoFix</title>

<link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<nav>
    <a href="/">AutoFix</a>

    <?php if(isset($_SESSION['user_id'])): ?>
        <a href="?action=logout">Wyloguj</a>
<?php else: ?>
    <a href="?action=login">Zaloguj</a>
    <a href="?action=register">Rejestracja</a>
<?php endif; ?>

</nav>

<main>
