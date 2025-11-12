<?php require_once __DIR__ . '/header.php'; ?>
<?php
// helpers
function slugify($text){
  $text = preg_replace('~[\p{Pd}\s]+~u','-',$text);
  $text = trim($text,'-');
  $text = strtolower($text);
  $text = preg_replace('~[^a-z0-9-]+~','',$text);
  return $text ?: uniqid('post-');
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$post = [
  'type' => 'news',
  'title' => '',
  'slug' => '',
  'excerpt' => '',
  'content' => '',
  'cover_image' => '',
  'published' => 1,
];

if ($id) {
  $stmt = db()->prepare('SELECT * FROM posts WHERE id=?');
  $stmt->bind_param('i', $id);
  $stmt->execute();
  $res = $stmt->get_result();
  if ($row = $res->fetch_assoc()) $post = $row; else $id = 0;
}

$msg = '';$err = '';
if ($_SERVER['REQUEST_METHOD']==='POST'){
  $type = $_POST['type'] === 'article' ? 'article' : 'news';
  $title = trim($_POST['title'] ?? '');
  $slug = trim($_POST['slug'] ?? '') ?: slugify($title);
  $excerpt = trim($_POST['excerpt'] ?? '');
  $content = trim($_POST['content'] ?? '');
  $published = isset($_POST['published']) ? 1 : 0;

  if ($title === '') { $err = 'Judul wajib diisi.'; }

  // handle cover upload
  $cover = $post['cover_image'] ?? '';
  if (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
    $f = $_FILES['cover'];
    $allowed = ['image/jpeg'=>'.jpg','image/png'=>'.png','image/webp'=>'.webp'];
    if (!isset($allowed[$f['type']])) {
      $err = 'Format gambar tidak didukung.';
    } else {
      $ext = $allowed[$f['type']];
      if (!is_dir($config['app']['uploads_dir'])) @mkdir($config['app']['uploads_dir'], 0777, true);
      $filename = 'post_'.date('Ymd_His').'_'.bin2hex(random_bytes(4)).$ext;
      $dest = rtrim($config['app']['uploads_dir'],'/\\').DIRECTORY_SEPARATOR.$filename;
      if (move_uploaded_file($f['tmp_name'], $dest)) {
        // delete old cover if exists
        if (!empty($cover)) {
          $old = str_replace($config['app']['uploads_url'], $config['app']['uploads_dir'], $cover);
          if (is_file($old)) @unlink($old);
        }
        $cover = rtrim($config['app']['uploads_url'],'/').'/'.$filename;
      } else {
        $err = 'Gagal upload gambar.';
      }
    }
  }

  if (!$err) {
    if ($id) {
      $stmt = db()->prepare('UPDATE posts SET type=?, title=?, slug=?, excerpt=?, content=?, cover_image=?, published=? WHERE id=?');
      $stmt->bind_param('ssssssii', $type,$title,$slug,$excerpt,$content,$cover,$published,$id);
      $stmt->execute();
      $msg = 'Post berhasil diperbarui.';
    } else {
      $stmt = db()->prepare('INSERT INTO posts(type,title,slug,excerpt,content,cover_image,published) VALUES(?,?,?,?,?,?,?)');
      $stmt->bind_param('ssssssi', $type,$title,$slug,$excerpt,$content,$cover,$published);
      $stmt->execute();
      $id = db()->insert_id;
      $msg = 'Post berhasil ditambahkan.';
    }
    // reload data
    $post = [
      'type'=>$type,'title'=>$title,'slug'=>$slug,'excerpt'=>$excerpt,'content'=>$content,'cover_image'=>$cover,'published'=>$published
    ];
  }
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0"><?= $id? 'Edit Artikel' : 'Tambah Artikel' ?></h3>
  <a class="btn btn-outline-secondary" href="/admin/posts">Kembali</a>
</div>

<?php if ($err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endif; ?>
<?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

<form method="post" enctype="multipart/form-data">
  <div class="row g-3">
    <div class="col-md-3">
      <label class="form-label">Jenis</label>
      <select name="type" class="form-select">
        <option value="news" <?= $post['type']==='news'?'selected':'' ?>>News</option>
        <option value="article" <?= $post['type']==='article'?'selected':'' ?>>Article</option>
      </select>
    </div>
    <div class="col-md-9">
      <label class="form-label">Judul</label>
      <input type="text" name="title" class="form-control" value="<?= e($post['title']) ?>" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Slug</label>
      <input type="text" name="slug" class="form-control" value="<?= e($post['slug']) ?>" placeholder="otomatis dari judul jika dikosongkan">
    </div>
    <div class="col-md-6">
      <label class="form-label">Cover Image</label>
      <input type="file" name="cover" accept="image/*" class="form-control">
      <?php if (!empty($post['cover_image'])): ?>
        <div class="mt-2"><img src="<?= e($post['cover_image']) ?>" alt="cover" style="max-height:90px"></div>
      <?php endif; ?>
    </div>
    <div class="col-12">
      <label class="form-label">Intro</label>
      <textarea name="excerpt" id="excerpt" rows="3" class="form-control"><?= e($post['excerpt']) ?></textarea>
      <small class="text-muted">Ringkasan singkat artikel (opsional)</small>
    </div>
    <div class="col-12">
      <label class="form-label">Content (Isi Artikel)</label>
      <textarea name="content" id="content" rows="8" class="form-control"><?= e($post['content']) ?></textarea>
    </div>
    <div class="col-12 form-check">
      <input class="form-check-input" type="checkbox" id="published" name="published" <?= $post['published']? 'checked':'' ?>>
      <label class="form-check-label" for="published">Published</label>
    </div>
  </div>
  <div class="mt-3 d-flex gap-2">
    <button class="btn btn-success" type="submit">Simpan</button>
    <a class="btn btn-outline-secondary" href="posts.php">Batal</a>
  </div>
</form>

<!-- CKEditor 5 Script -->
<script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>
<script>
// CKEditor configuration
const editorConfig = {
    toolbar: {
        items: [
            'heading', '|',
            'bold', 'italic', 'underline', 'strikethrough', '|',
            'link', 'bulletedList', 'numberedList', '|',
            'indent', 'outdent', '|',
            'blockQuote', 'insertTable', '|',
            'undo', 'redo'
        ]
    },
    heading: {
        options: [
            { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
            { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
            { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
            { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' }
        ]
    },
    table: {
        contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
    }
};

// Initialize CKEditor for Excerpt (simpler toolbar)
ClassicEditor
    .create(document.querySelector('#excerpt'), {
        toolbar: {
            items: [
                'bold', 'italic', '|',
                'link', '|',
                'undo', 'redo'
            ]
        }
    })
    .catch(error => {
        console.error('Error initializing excerpt editor:', error);
    });

// Initialize CKEditor for Content (full toolbar)
ClassicEditor
    .create(document.querySelector('#content'), editorConfig)
    .then(editor => {
        // Set editor height to be 3x larger (default is ~200px, so we set to 600px)
        editor.editing.view.change(writer => {
            writer.setStyle('min-height', '600px', editor.editing.view.document.getRoot());
        });
    })
    .catch(error => {
        console.error('Error initializing content editor:', error);
    });
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
