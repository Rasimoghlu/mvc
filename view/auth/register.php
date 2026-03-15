<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - TaskApp</title>
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>
    <?php include __DIR__ . '/../partials/navbar.php'; ?>

    <div class="auth-wrapper">
        <div class="auth-card">
            <h1>Register</h1>

            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?= e($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="/register">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" class="form-control <?= error('name') ? 'is-invalid' : '' ?>" value="<?= e(old('name')) ?>" placeholder="Your name">
                    <?php if ($err = error('name')): ?>
                        <div class="field-error"><?= e($err) ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control <?= error('email') ? 'is-invalid' : '' ?>" value="<?= e(old('email')) ?>" placeholder="you@example.com">
                    <?php if ($err = error('email')): ?>
                        <div class="field-error"><?= e($err) ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control <?= error('password') ? 'is-invalid' : '' ?>" placeholder="Min 8 characters">
                    <?php if ($err = error('password')): ?>
                        <div class="field-error"><?= e($err) ?></div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%;">Register</button>
            </form>

            <div class="auth-footer">
                Already have an account? <a href="/login">Login</a>
            </div>
        </div>
    </div>

    <?php clearErrors(); clearOld(); ?>
</body>
</html>
