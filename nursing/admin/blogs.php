<?php
require_once __DIR__ . '/../config.php';
requireLogin();

$type = 'blogs';
$blogs = getData($type);
$msg = '';
$error = '';

// Handle Delete Action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $deleteId = (int)$_GET['id'];
    $blogs = array_filter($blogs, function($b) use ($deleteId) {
        return (int)$b['id'] !== $deleteId;
    });
    $blogs = array_values($blogs);
    saveData($type, $blogs);
    header('Location: blogs.php?msg=deleted');
    exit;
}

// Handle Add / Edit POST Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id       = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $title    = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? 'Health Tips');
    $author   = trim($_POST['author'] ?? 'LifeCare Team');
    $date     = trim($_POST['date'] ?? date('d M Y'));
    $excerpt  = trim($_POST['excerpt'] ?? '');
    $content  = trim($_POST['content'] ?? '');

    if (empty($title) || empty($excerpt)) {
        $error = 'Blog Title and Excerpt are required.';
    } else {
        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploaded = uploadImage($_FILES['image']);
            if ($uploaded) {
                $imagePath = $uploaded;
            }
        }

        if ($id > 0) {
            foreach ($blogs as &$post) {
                if ((int)$post['id'] === $id) {
                    $post['title']    = $title;
                    $post['category'] = $category;
                    $post['author']   = $author;
                    $post['date']     = $date;
                    $post['excerpt']  = $excerpt;
                    $post['content']  = $content;
                    if ($imagePath) {
                        $post['image'] = $imagePath;
                    }
                    break;
                }
            }
            saveData($type, $blogs);
            header('Location: blogs.php?msg=updated');
            exit;
        } else {
            $newId = !empty($blogs) ? max(array_column($blogs, 'id')) + 1 : 1;
            $newPost = [
                'id'       => $newId,
                'title'    => $title,
                'category' => $category,
                'date'     => $date,
                'author'   => $author,
                'image'    => $imagePath ?: 'assets/ChatGPT Image Aug 25, 2026, 05_59_14 PM.png',
                'excerpt'  => $excerpt,
                'content'  => $content
            ];
            $blogs[] = $newPost;
            saveData($type, $blogs);
            header('Location: blogs.php?msg=added');
            exit;
        }
    }
}

if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'added') $msg = 'New Blog Article created successfully!';
    if ($_GET['msg'] === 'updated') $msg = 'Blog Article updated successfully!';
    if ($_GET['msg'] === 'deleted') $msg = 'Blog Article deleted successfully!';
}

require_once __DIR__ . '/inc/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="m-0 text-dark">Blog Posts & Articles Management</h2>
        <p class="text-muted m-0">Add, edit, or delete health articles displayed on the homepage and blog.html page.</p>
    </div>
    <button class="btn-care" data-bs-toggle="modal" data-bs-target="#blogModal" onclick="resetForm()">
        <i class="bi bi-plus-lg me-1"></i> Add New Blog Article
    </button>
</div>

<?php if ($msg): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($msg) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card-custom">
    <div class="card-custom-header">
        <h5 class="m-0 font-weight-bold text-dark"><i class="bi bi-journal-richtext text-teal me-2" style="color:var(--teal-700);"></i> Active Blog Posts (<?= count($blogs) ?>)</h5>
    </div>
    <div class="table-responsive p-0">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
            <thead class="table-light">
                <tr>
                    <th style="width: 70px;">Cover</th>
                    <th>Title & Excerpt</th>
                    <th>Category</th>
                    <th>Author</th>
                    <th>Date</th>
                    <th class="text-end" style="width: 130px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($blogs)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No blog posts found. Click "Add New Blog Article" to create your first article.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($blogs as $post): ?>
                        <tr>
                            <td>
                                <img src="../<?= htmlspecialchars($post['image']) ?>" alt="Cover" class="thumb-preview" onerror="this.src='../assets/logo.png'">
                            </td>
                            <td>
                                <div class="font-weight-bold text-dark" style="font-size: 0.95rem;"><?= htmlspecialchars($post['title']) ?></div>
                                <div class="text-muted" style="font-size: 0.82rem; max-width: 380px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= htmlspecialchars($post['excerpt']) ?></div>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1"><?= htmlspecialchars($post['category'] ?? 'Health') ?></span>
                            </td>
                            <td>
                                <span class="text-secondary fw-semibold"><?= htmlspecialchars($post['author'] ?? 'LifeCare Team') ?></span>
                            </td>
                            <td>
                                <span class="text-muted small"><?= htmlspecialchars($post['date'] ?? '') ?></span>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-primary me-1" onclick="editPost(<?= htmlspecialchars(json_encode($post)) ?>)" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <a href="blogs.php?action=delete&id=<?= $post['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this article?');" title="Delete">
                                    <i class="bi bi-trash-fill"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ADD / EDIT BLOG MODAL -->
<div class="modal fade" id="blogModal" tabindex="-1" aria-labelledby="blogModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header text-white p-4" style="background: var(--teal-900);">
                <h4 class="modal-title font-weight-bold m-0" id="blogModalTitle">
                    <i class="bi bi-journal-plus me-2" style="color: var(--amber-500);"></i> Add New Blog Article
                </h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="background: #FAF8F3;">
                <form method="POST" enctype="multipart/form-data" id="blogForm">
                    <input type="hidden" name="id" id="formPostId" value="0">
                    
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="formTitle" class="form-label font-weight-bold text-uppercase" style="font-size: 0.8rem; color: var(--teal-900);">Article Title *</label>
                            <input type="text" class="form-control" name="title" id="formTitle" placeholder="e.g. When Home Nursing Can Make Recovery Easier" required>
                        </div>
                        <div class="col-md-4">
                            <label for="formCategory" class="form-label font-weight-bold text-uppercase" style="font-size: 0.8rem; color: var(--teal-900);">Category *</label>
                            <select class="form-select" name="category" id="formCategory">
                                <option value="Home Nursing">Home Nursing</option>
                                <option value="Elderly Care">Elderly Care</option>
                                <option value="Health Tips">Health Tips</option>
                                <option value="Medical Support">Medical Support</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="formAuthor" class="form-label font-weight-bold text-uppercase" style="font-size: 0.8rem; color: var(--teal-900);">Author Name</label>
                            <input type="text" class="form-control" name="author" id="formAuthor" placeholder="Dr. Haris Abbasi" value="LifeCare Team">
                        </div>
                        <div class="col-md-6">
                            <label for="formDate" class="form-label font-weight-bold text-uppercase" style="font-size: 0.8rem; color: var(--teal-900);">Publish Date</label>
                            <input type="text" class="form-control" name="date" id="formDate" placeholder="18 Aug 2026" value="<?= date('d M Y') ?>">
                        </div>

                        <div class="col-12">
                            <label for="formImage" class="form-label font-weight-bold text-uppercase" style="font-size: 0.8rem; color: var(--teal-900);">Article Image</label>
                            <input type="file" class="form-control" name="image" id="formImage" accept="image/*">
                            <div class="form-text">JPG, PNG, WebP supported. Leave blank when editing to keep existing image.</div>
                        </div>

                        <div class="col-12">
                            <label for="formExcerpt" class="form-label font-weight-bold text-uppercase" style="font-size: 0.8rem; color: var(--teal-900);">Short Excerpt *</label>
                            <textarea class="form-control" name="excerpt" id="formExcerpt" rows="2" placeholder="Brief 1-2 sentence summary of the article..." required></textarea>
                        </div>

                        <div class="col-12">
                            <label for="formContent" class="form-label font-weight-bold text-uppercase" style="font-size: 0.8rem; color: var(--teal-900);">Full Article Content (HTML / Paragraphs)</label>
                            <textarea class="form-control" name="content" id="formContent" rows="6" placeholder="<p>Full article text goes here...</p><h4>Subheading</h4><p>More details...</p>"></textarea>
                        </div>

                        <div class="col-12 mt-4 d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn-care px-4" style="border: none; cursor: pointer;">
                                <i class="bi bi-check-circle-fill me-1"></i> Save Article
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function resetForm() {
    document.getElementById('blogModalTitle').innerHTML = '<i class="bi bi-journal-plus me-2" style="color: var(--amber-500);"></i> Add New Blog Article';
    document.getElementById('formPostId').value = 0;
    document.getElementById('blogForm').reset();
    document.getElementById('formDate').value = '<?= date('d M Y') ?>';
    document.getElementById('formAuthor').value = 'LifeCare Team';
}

function editPost(post) {
    document.getElementById('blogModalTitle').innerHTML = '<i class="bi bi-pencil-square me-2" style="color: var(--amber-500);"></i> Edit Blog Article';
    document.getElementById('formPostId').value = post.id;
    document.getElementById('formTitle').value = post.title || '';
    document.getElementById('formCategory').value = post.category || 'Health Tips';
    document.getElementById('formAuthor').value = post.author || 'LifeCare Team';
    document.getElementById('formDate').value = post.date || '';
    document.getElementById('formExcerpt').value = post.excerpt || '';
    document.getElementById('formContent').value = post.content || '';

    const modal = new bootstrap.Modal(document.getElementById('blogModal'));
    modal.show();
}
</script>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
