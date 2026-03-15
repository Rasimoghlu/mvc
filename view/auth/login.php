<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TaskApp</title>
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>
    <?php include __DIR__ . '/../partials/navbar.php'; ?>

    <div class="auth-wrapper">
        <div class="auth-card">
            <h1>Login</h1>

            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?= e($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="/login">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control <?= error('email') ? 'is-invalid' : '' ?>" value="<?= e(old('email')) ?>" placeholder="you@example.com">
                    <?php if ($err = error('email')): ?>
                        <div class="field-error"><?= e($err) ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control <?= error('password') ? 'is-invalid' : '' ?>" placeholder="Enter password">
                    <?php if ($err = error('password')): ?>
                        <div class="field-error"><?= e($err) ?></div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%;">Login</button>
            </form>

            <div class="auth-footer">
                Don't have an account? <a href="/register">Register</a>
            </div>
        </div>
    </div>

    <?php clearErrors(); clearOld(); ?>
</body>
</html>
