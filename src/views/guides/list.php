<?php require ROOT_PATH.'/src/views/layout/header.php'; ?>

<h1>Poradniki</h1>

<?php if(isset($_SESSION['user_id'])): ?>

<a href="?action=create-guide">Dodaj poradnik</a>

<?php endif; ?>

<?php foreach($guides as $g): ?>

<div class="card">

    <?php if($g['image']): ?>
        <img src="/uploads/<?= $g['image'] ?>" class="thumb">
    <?php endif; ?>

    <h3>
        <a href="?action=show-guide&id=<?= $g['id'] ?>">
            <?= htmlspecialchars($g['title']) ?>
        </a>
    </h3>

</div>

<?php endforeach; ?>



<?php require ROOT_PATH.'/src/views/layout/footer.php'; ?>

