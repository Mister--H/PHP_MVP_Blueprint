<form action="/login" method="post">
        <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger" role="alert">
            <?= $_SESSION['error']; ?>
        </div>
        <?php unset($_SESSION['error']); // Clear the error message from the session ?>
    <?php endif; ?>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="rememberMe" name="remember_me">
            <label class="form-check-label" for="rememberMe">Remember Me</label>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>