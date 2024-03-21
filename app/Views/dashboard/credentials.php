<div class="container">
    <div class="row">
        <!-- Table for displaying credentials -->
        <div class="col-md-6">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Store URL</th>
                        <th scope="col">Action</th> <!-- Merged column for both actions -->
                    </tr>
                </thead>
                <tbody>
                    <!-- Example row, replicate this part based on actual data -->
                    <?php if (isset($credentials) && !empty($credentials)): ?>
                        <?php foreach ($credentials as $index => $credential): ?>
                            <tr>
                                <th scope="row"><?= $index + 1 ?></th>
                                <td><?= htmlspecialchars($credential['store_url'], ENT_QUOTES, 'UTF-8') ?></td>
                                <!-- Merged Actions Column -->
                                <td class="text-center">
                                    <!-- Delete Icon -->
                                    <a href="/dashboard/credentials/delete/<?= $credential['id'] ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this credential?');">
                                        <i class="bi bi-trash-fill"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>


                    <?php else: ?>
                        <tr>
                            <td colspan="4">No credentials found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Existing Form -->
        <div class="col-md-6">
            <form action="/dashboard/credentials" method="post">
                <?php if (!empty($_SESSION['error'])): ?>
                <div class="alert alert-danger" role="alert">
                    <?= $_SESSION['error']; ?>
                </div>
                <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
                <div class="mb-3">
                    <label for="storeURL" class="form-label">Store URL</label>
                    <input type="url" class="form-control" id="storeURL" name="storeURL" required>
                </div>
                <div class="mb-3">
                    <label for="consumerKey" class="form-label">Consumer Key</label>
                    <input type="text" class="form-control" id="consumerKey" name="consumerKey" required>
                </div>
                <div class="mb-3">
                    <label for="consumerSecret" class="form-label">Consumer Secret</label>
                    <input type="text" class="form-control" id="consumerSecret" name="consumerSecret" required>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
</div>
