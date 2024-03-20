<div class="container my-4">
    <div class="row">
        <!-- Form for Adding New Product -->
        <div class="col-md-6">
            <?php if (isset($message['error'])) { 
                echo '<div class="alert alert-danger" role="alert">' . $message['error'] . '</div>';
            } elseif (isset($message['success'])) {
                echo '<div class="alert alert-success" role="alert">' . $message['success'] . '</div>';
            } ?>
            <form method="post">
                <div class="mb-3">
                    <label for="storeSelect" class="form-label">Select Store</label>
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
                    <label for="productURL" class="form-label">Product URL</label>
                    <input type="text" class="form-control" id="productURL" name="product_url">
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="haveContent" name="have_content">
                    <label class="form-check-label" for="haveContent">Have Content</label>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="needApproval" name="need_approval">
                    <label class="form-check-label" for="needApproval">Need Approval</label>
                </div>
                <div class="mb-3">
                    <label for="SKU" class="form-label">SKU</label>
                    <input type="text" class="form-control" id="SKU" name="sku">
                </div>
                <div class="mb-3">
                    <label for="Brand" class="form-label">Brand</label>
                    <input type="text" class="form-control" id="Brand" name="brand" value="بوش">
                </div>
                <div class="mb-3">
                    <label for="Price" class="form-label">Price</label>
                    <input type="number" class="form-control" id="Price" name="price">
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
</div>
