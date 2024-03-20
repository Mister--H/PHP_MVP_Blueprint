<div class="container my-4">
    <div class="row">
        <!-- Table with Last 5 Products -->
        <div class="col-md-12 ">
    <div class="row align-items-center">
        <!-- Title on the Left -->
        <div class="col">
            <h3>Latest Products Scrapped</h3>
        </div>

        <!-- Buttons on the Right -->
        <div class="col text-end">
            <a href="/dashboard/add-content" class="btn btn-primary mx-1"><i class="bi bi-plus-circle"></i> Add Content</a>
            <a href="/dashboard/woo/add-product" class="btn btn-primary mx-1"><i class="bi bi-plus-circle"></i> Add New Product</a>
            <a href="/dashboard/settings" class="btn btn-secondary mx-1"><i class="bi bi-gear"></i> Settings</a>
        </div>
    </div>

            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Photo</th>
                        <th scope="col">Title</th>
                        <th scope="col">SKU</th>
                        <th scope="col">URL</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Dynamically generated rows go here -->
                    <!-- Example Row -->
                    <tr>
                        <th scope="row">1</th>
                        <td><img src="path/to/image.jpg" alt="Product Photo" style="width: 50px; height: auto;"></td>
                        <td>Product Title</td>
                        <td>Product SKU</td>
                        <td><a href="product-url">Product URL</a></td>
                        <td><a href="product-url" class="btn btn-primary"><i class="bi bi-link"></i> View</a></td>
                    </tr>
                    <!-- Repeat for each product -->
                </tbody>
            </table>
        </div>

        <!-- Buttons on the Right -->
        
    </div>
</div>
