<div class="container my-4">
    <form method="post">
        <div class="mb-3">
            <label for="storeSelect" class="form-label"><i class="bi bi-shop"></i> Select Store</label>
            <select class="form-select" id="storeSelect" name="store_id">
                <?php if (isset($credentials) && !empty($credentials)): ?>
                    <?php foreach ($credentials as $index => $credential): ?>
                        <option value="<?= htmlspecialchars($credential['id'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($credential['store_url'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option>No Store</option>
                <?php endif; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="productURL" class="form-label"><i class="bi bi-link-45deg"></i> Product URL</label>
            <input type="text" class="form-control" id="productURL" name="product_url">
        </div>
        <div class="mb-3">
            <label for="SKU" class="form-label"><i class="bi bi-upc-scan"></i> SKU</label>
            <input type="text" class="form-control" id="SKU" name="sku">
        </div>
        <div class="mb-3">
            <label for="productContent" class="form-label"><i class="bi bi-file-text"></i> Product Content</label>
            <textarea class="form-control" id="productContent" name="product_content" rows="30"></textarea>
        </div>
        <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Submit</button>
    </form>
</div>

<!-- Include a text editor library like TinyMCE or CKEditor for the text area to transform it into a rich text editor -->
<script src="https://cdn.tiny.cloud/1/ex4k4njzqvnzmk2o1m274ftzw35wibhymzz0hougpl0sxiz0/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<script>
  tinymce.init({
    selector: '#productContent'
  });
</script>
