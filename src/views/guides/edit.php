<?php require ROOT_PATH.'/src/views/layout/header.php'; ?>

<h2>Edytuj poradnik</h2>

<form method="POST">

    <input type="text"
           name="title"
           value="<?= htmlspecialchars($guide['title']) ?>"
           required>

    <textarea id="editor"
    name="content"><?= htmlspecialchars($guide['content']) ?></textarea>

    <script>
    tinymce.init({
    selector: '#editor',
    height: 400,
    plugins: 'image link lists code table',
    toolbar: 'undo redo | bold italic | alignleft aligncenter | bullist numlist | image link | code',
    automatic_uploads: true
    });
    </script>

    <button>Zapisz zmiany</button>

</form>

<?php require ROOT_PATH.'/src/views/layout/footer.php'; ?>
