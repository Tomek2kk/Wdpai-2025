<?php require __DIR__ . '/../layout/header.php'; ?>

<h2>Logowanie</h2>

<?php if(isset($error)): ?>
<p class="error"><?= $error ?></p>
<?php endif; ?>

<form method="POST">

    <input type="email" name="email"
           placeholder="Email" required>

    <input type="password" name="password"
           placeholder="Hasło" required>

    <button type="submit">Zaloguj</button>

</form>

<?php require __DIR__ . '/../layout/footer.php'; ?>
