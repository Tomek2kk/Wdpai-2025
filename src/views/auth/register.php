<?php require ROOT_PATH . '/src/views/layout/header.php'; ?>

<h2>Rejestracja</h2>

<?php if(isset($error)): ?>
<p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST">

    <input type="text"
           name="username"
           placeholder="Login"
           required>

    <input type="email"
           name="email"
           placeholder="Email"
           required>

    <input type="password"
           name="password"
           placeholder="Hasło"
           required>

    <input type="password"
           name="confirm"
           placeholder="Powtórz hasło"
           required>

    <button type="submit">Zarejestruj</button>

</form>

<p>
Masz konto?
<a href="?action=login">Zaloguj się</a>
</p>

<?php require ROOT_PATH . '/src/views/layout/footer.php'; ?>
