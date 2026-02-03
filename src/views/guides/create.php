<?php require ROOT_PATH.'/src/views/layout/header.php'; ?>

<h2>Nowy poradnik</h2>

<form method="POST" enctype="multipart/form-data">

<input type="text"
       name="title"
       placeholder="Tytuł"
       required>

<br><br>

<input type="file" name="image">

<br><br>

<textarea id="editor"
          name="content"></textarea>

<br>

<button>Zapisz</button>

</form>

<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js"></script>

<script>
tinymce.init({
    selector: '#editor',
    height: 400,
    plugins: 'image link lists code table',
    toolbar: 'undo redo | bold italic | alignleft aligncenter | bullist numlist | image link | code',
    automatic_uploads: true
});
</script>

<?php require ROOT_PATH.'/src/views/layout/footer.php'; ?>
