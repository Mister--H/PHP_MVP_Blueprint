<div class="container my-4">
    <div class="row">
        <!-- Form on Left Side -->
        <div class="col-md-6">
            <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger" role="alert">
            <?= $_SESSION['error']; ?>
        </div>
        <?php unset($_SESSION['error']); // Clear the error message from the session ?>
    <?php endif; ?>
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
                    <label for="name" class="form-label">Setting Name</label>
                    <input type="text" class="form-control" id="name" name="name">
                </div>
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" class="form-control" id="title" name="title">
                </div>
                <div class="mb-3">
                    <label for="category" class="form-label">Category</label>
                    <input type="text" class="form-control" id="category" name="category">
                </div>
                <div class="mb-3">
                    <label for="price" class="form-label">Price</label>
                    <input type="text" class="form-control" id="price" name="price">
                </div>
                <div class="mb-3">
                    <label for="offPrice" class="form-label">Off Price</label>
                    <input type="text" class="form-control" id="offPrice" name="off_price">
                </div>
                <div class="mb-3">
                    <label for="image" class="form-label">Image</label>
                    <input type="text" class="form-control" id="image" name="image">
                </div>
                <div class="mb-3">
                    <label for="gallery" class="form-label">Gallery</label>
                    <input type="text" multiple class="form-control" id="gallery" name="gallery">
                </div>
                <div class="mb-3">
                    <label for="attributeContainer" class="form-label">Attribute Container</label>
                    <input type="text" class="form-control" id="attributeContainer" name="attribute_container">
                </div>
                <div class="mb-3">
                    <label for="attributeLabel" class="form-label">Attribute Label</label>
                    <input type="text" class="form-control" id="attributeLabel" name="attribute_label">
                </div>
                <div class="mb-3">
                    <label for="attributeValue" class="form-label">Attribute Value</label>
                    <input type="text" class="form-control" id="attributeValue" name="attribute_value">
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>

        <!-- List of Settings on Right Side -->
        <div class="col-md-6">
            <!-- Settings list goes here. Example given below -->
            <div class="list-group">
                <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    Setting 1
                    <span>
                        <button class="btn btn-sm btn-primary mx-1"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                    </span>
                </a>
                <!-- Repeat for each setting -->
            </div>
        </div>
    </div>
</div>
