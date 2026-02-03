<?php require ROOT_PATH.'/src/views/layout/header.php'; ?>

<div class="guide-page">

<h1><?= htmlspecialchars($guide['title']) ?></h1>

<p class="author">
Autor: <?= htmlspecialchars($guide['username']) ?>
</p>

<?php if($guide['image']): ?>
<img src="/uploads/<?= $guide['image'] ?>" class="main-image">
<?php endif; ?>

<div class="content">

    <?= $guide['content'] ?> 

</div>


<?php if(isset($_SESSION['user_id']) && 
$_SESSION['user_id']==$guide['user_id']): ?>

<hr>

<a href="?action=edit-guide&id=<?= $guide['id'] ?>">Edytuj</a>

<?php endif; ?>

</div>

<?php require ROOT_PATH.'/src/views/layout/footer.php'; ?>
