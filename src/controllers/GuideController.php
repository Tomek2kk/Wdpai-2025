<?php

require_once ROOT_PATH.'/src/models/Guide.php';
require_once ROOT_PATH.'/src/models/Category.php';
require_once ROOT_PATH.'/src/models/Comment.php';
require_once ROOT_PATH.'/src/models/Rating.php';

require_once ROOT_PATH.'/src/helpers/Auth.php';
require_once ROOT_PATH.'/src/helpers/View.php';


class GuideController {

    private Guide $guide;

    public function __construct(){
        $this->guide = new Guide();
    }

    public function index(){

        $catModel = new Category();
        $categories = $catModel->getAll();

        $catHtml = '';

        foreach($categories as $c){

            $catHtml .= "
            <a href='/?cat={$c['id']}'
               class='category-card'>
                ".htmlspecialchars($c['name'])."
            </a>
            ";
        }

        if(isset($_GET['cat'])){

            $guides = $this->guide
                ->getByCategory($_GET['cat']);

        }else{

            $guides = $this->guide->getAll();
        }

        $cards = '';

        foreach($guides as $g){

            $card = file_get_contents(
                ROOT_PATH.'/src/views/guides/guide_card.html'
            );

            foreach($g as $k=>$v){

                if($k === 'image'){

                    $v = !empty($v)
                        ? '/uploads/'.$v
                        : '/assets/img/no-image.png';
                }

                $card = str_replace(
                    '{{'.$k.'}}',
                    htmlspecialchars((string)$v),
                    $card
                );
            }

            $cards .= $card;
        }

        $addButton = '';

        if(Auth::isAdmin() || Auth::isModerator()){

            $addButton = "
            <a href='/?action=add-guide'
                class='btn btn-comment'>
                Dodaj poradnik
            </a>
            ";
        }


        View::render('home',[

            'title'      => 'AutoFix',

            'categories' => $catHtml,

            'guides'     => $cards,

            'add_button' => $addButton
        ]);
    }

    public function show(){

        $id = $_GET['id'] ?? null;

        if(!$id){
            die("Brak ID");
        }


        $guide = $this->guide->getById($id);

        if(!$guide){
            die("Nie istnieje");
        }

        $hasImage = !empty($guide['image']);

        $image = $hasImage
        ? '/uploads/'.$guide['image']
        : '/assets/img/no-image.png';


        $catModel = new Category();
        $cats = $catModel->getForGuide($id);

        $catHtml = '';

        foreach($cats as $c){
            $catHtml .= htmlspecialchars($c['name']).', ';
        }

        $catHtml = rtrim($catHtml, ', ');

        $commentModel = new Comment();
        $comments = $commentModel->getByGuide($id);

        $commentsHtml = '';

        foreach($comments as $c){

            $del = '';

            if(
                Auth::isAdmin() ||
                Auth::isModerator() ||
                $c['user_id'] == ($_SESSION['user_id'] ?? null)
            ){

                $del = "
                <button
                    class='btn btn-comment-delete'
                    onclick='deleteComment({$c['id']})'>
                    Usuń
                </button>
                ";
            }


            $commentsHtml .= "
            <div class='comment' id='comment-{$c['id']}'>
                <b>".htmlspecialchars($c['username'])."</b>
                <small>{$c['created_at']}</small>

                <p>".nl2br(htmlspecialchars($c['content']))."</p>

                $del
            </div>
            ";
        }

        if(Auth::check()){

            $commentForm = "
            <textarea
                id='commentContent'
                rows='3'
                placeholder='Napisz komentarz...'></textarea>

            <br><br>

            <button
                onclick='addComment()'
                class='btn btn-comment'>
                Dodaj
            </button>
            ";
        }
        else{

            $commentForm = "
            <p>
                <a href='/?action=login'>Zaloguj się</a>,
                aby dodać komentarz
            </p>
            ";
        }

        $ratingModel = new Rating();
        $avgRating = $ratingModel->getAverage($id);

        $ratingForm = '';

        if(Auth::check()){

            $ratingForm = "<select onchange='rateGuide(this.value)'>
            <option value=''>Oceń</option>";

            for($i=1;$i<=5;$i++){
                $ratingForm .= "<option value='$i'>$i ⭐</option>";
            }

            $ratingForm .= "</select>";
        }

        $actions = '';

        if(Auth::isAdmin() || Auth::isModerator()){

            $actions = "
            <a href='/?action=edit-guide&id=$id'
               class='btn btn-edit'>
               Edytuj
            </a>

            <a href='/?action=delete-guide&id=$id'
               class='btn btn-delete'
               onclick=\"return confirm('Usunąć?')\">
               Usuń
            </a>
            ";
        }

        $ajax = "

<script>

function addComment(){

    const content =
        document.getElementById('commentContent').value;

    if(!content.trim()) return;

    fetch('/?action=api-add-comment',{
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body:JSON.stringify({
            guide_id:$id,
            content:content
        })
    })
    .then(r=>r.json())
    .then(d=>location.reload());
}



function deleteComment(id){

    if(!confirm('Usunąć?')) return;

    fetch('/?action=api-delete-comment',{
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body:JSON.stringify({id:id})
    })
    .then(r=>r.json())
    .then(d=>{
        if(d.success){
            document
            .getElementById('comment-'+id)
            .remove();
        }
    });
}



function rateGuide(val){

    fetch('/?action=api-rate',{
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body:JSON.stringify({
            guide:$id,
            rating:val
        })
    });
}

</script>
";

        View::render('guides/show',[

            'title'        => $guide['title'],
            'username'     => htmlspecialchars($guide['username']),

            'image'     => $image,
            'has_image' => $hasImage ? 'has-image' : 'no-image',

            'content'      => $guide['content'],

            'categories'   => $catHtml,

            'rating'       => $avgRating ?? 'Brak',
            'rating_form'  => $ratingForm,

            'actions'      => $actions,

            'comment_form' => $commentForm,
            'comments'     => $commentsHtml,

            'ajax'         => $ajax
        ]);
    }

    public function search(){

        $q = trim($_GET['q'] ?? '');

        if(empty($q)){
            header("Location: /");
            exit;
        }


        $guides = $this->guide->search($q);

        $cards = '';

        foreach($guides as $g){

            $card = file_get_contents(
                ROOT_PATH.'/src/views/guides/guide_card.html'
            );

            foreach($g as $k=>$v){

                if($k === 'image'){

                    $v = !empty($v)
                        ? '/uploads/'.$v
                        : '/assets/img/no-image.png';
                }

                $card = str_replace(
                    '{{'.$k.'}}',
                    htmlspecialchars((string)$v),
                    $card
                );
            }

            $cards .= $card;
        }


        View::render('guides/list',[

            'title'  => 'Wyniki: '.$q,

            'guides' => $cards
        ]);
    }

    public function create(){

    Auth::requireRole([2,3]);

    $catModel = new Category();
    $categories = $catModel->getAll();

    $error = '';


    if($_SERVER['REQUEST_METHOD']==='POST'){

        $title   = trim($_POST['title']);
        $content = $_POST['content'] ?? '';
        $cats    = $_POST['categories'] ?? [];


        if(empty($title)){
            $error = "Podaj tytuł";
        }
        elseif(empty(strip_tags($content))){
            $error = "Treść pusta";
        }
        else{

            $image = null;

            if(
                isset($_FILES['image']) &&
                $_FILES['image']['error'] === UPLOAD_ERR_OK
            ){

                $ext = strtolower(
                    pathinfo($_FILES['image']['name'],
                    PATHINFO_EXTENSION)
                );

                if(in_array($ext,['jpg','jpeg','png','webp'])){

                    $image = uniqid().".".$ext;

                    move_uploaded_file(
                        $_FILES['image']['tmp_name'],
                        ROOT_PATH.'/public/uploads/'.$image
                    );
                }
            }


            $id = $this->guide->create(
                $title,
                $content,
                $image,
                $_SESSION['user_id']
            );

            if(!$id){
                die("Błąd zapisu poradnika");
            }

            $catModel->setForGuide((int)$id,$cats);



            header("Location: /?action=show-guide&id=".$id);
            exit;
        }
    }

    $catHtml = '';

    foreach($categories as $c){

        $catHtml .= "
        <label>
        <input type='checkbox'
               name='categories[]'
               value='{$c['id']}'>
        {$c['name']}
        </label><br>";
    }


    View::render('guides/form',[

    'page_title' => 'Nowy poradnik',
    'header'     => 'Dodaj poradnik',
    'button'     => 'Zapisz',

    'error'      => $error,

    'title'      => '',
    'content'    => '',

    'categories' => $catHtml,

    'editor'     => $this->editorJS()
    ]);

    }

    public function edit(){

    Auth::requireRole([2,3]);

    $id = $_GET['id'] ?? null;

    if(!$id){
        die("Brak ID");
    }


    $guide = $this->guide->getById($id);

    if(!$guide){
        die("Nie istnieje");
    }


    $catModel = new Category();

    $categories   = $catModel->getAll();
    $selectedCats = $catModel->getForGuide($id);

    $selectedIds = array_column($selectedCats,'id');

    $error = '';


    if($_SERVER['REQUEST_METHOD']==='POST'){

        $title   = trim($_POST['title']);
        $content = $_POST['content'];
        $cats    = $_POST['categories'] ?? [];


        if(empty($title)){
            $error = "Podaj tytuł";
        }
        else{

            $image = $guide['image'];

            if(
                isset($_FILES['image']) &&
                $_FILES['image']['error']===UPLOAD_ERR_OK
            ){

                $ext = strtolower(
                    pathinfo($_FILES['image']['name'],
                    PATHINFO_EXTENSION)
                );

                if(in_array($ext,['jpg','jpeg','png','webp'])){

                    $image = uniqid().".".$ext;

                    move_uploaded_file(
                        $_FILES['image']['tmp_name'],
                        ROOT_PATH.'/public/uploads/'.$image
                    );
                }
            }


            $this->guide->update(
                $id,
                $title,
                $content,
                $image
            );


            $catModel->setForGuide($id,$cats);


            header("Location: /?action=show-guide&id=".$id);
            exit;
        }
    }

    $catHtml = '';

    foreach($categories as $c){

        $checked = in_array($c['id'],$selectedIds)
                   ? 'checked' : '';

        $catHtml .= "
        <label>
        <input type='checkbox'
               name='categories[]'
               value='{$c['id']}'
               $checked>
        {$c['name']}
        </label><br>";
    }


    View::render('guides/form',[

    'page_title' => 'Edycja poradnika',
    'header'     => 'Edytuj poradnik',
    'button'     => 'Zapisz zmiany',

    'error'      => $error,

    'title'      => htmlspecialchars($guide['title']),
    'content'    => htmlspecialchars($guide['content']),

    'categories' => $catHtml,

    'editor'     => $this->editorJS()
    ]);

    }

    public function delete(){

    Auth::requireRole([3]);

    $id = $_GET['id'] ?? null;

    if(!$id){
        die("Brak ID");
    }


    $guide = $this->guide->getById($id);


    if(!empty($guide['image'])){

        $path = ROOT_PATH.'/public/uploads/'.$guide['image'];

        if(file_exists($path)){
            unlink($path);
        }
    }


    $this->guide->delete($id);

    header("Location: /");
    exit;
    }

    private function editorJS(){

    return '
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

    <script>

    class MyUploadAdapter {

    constructor(loader){
        this.loader = loader;
    }

    upload(){
        return this.loader.file
            .then(file => new Promise((resolve,reject)=>{

                const data = new FormData();
                data.append("upload",file);

                fetch("/upload.php",{
                    method:"POST",
                    body:data
                })
                .then(r=>r.json())
                .then(res=>{

                    if(res.error){
                        reject(res.error.message);
                        return;
                    }

                    resolve({
                        default: res.url
                    });

                })
                .catch(err=>reject(err));

            }));
    }

    abort(){}
    }

    function MyAdapterPlugin(editor){

    editor.plugins
          .get("FileRepository")
          .createUploadAdapter = loader => {
            return new MyUploadAdapter(loader);
          };
    }

    ClassicEditor
    .create(document.querySelector("#editor"),{
        extraPlugins:[MyAdapterPlugin]
    })
    .catch(error => console.error(error));

    </script>
    ';
    }

}
