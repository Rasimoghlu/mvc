<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New user</title>
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>
    <div class="container">
        <h1>Create new user</h1>
        
        <?php if (isset($success)): ?>
            <div class="success"><?= $success ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        
        <form action="/users/store" method="post">
            <?= csrf_field() ?>
            
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= old('name') ?>">
                <?php if ($error = error('name')): ?>
                    <div class="error"><?= $error ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>">
                <?php if ($error = error('email')): ?>
                    <div class="error"><?= $error ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password">
                <?php if ($error = error('password')): ?>
                    <div class="error"><?= $error ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group" style="margin-top: 20px;">
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="/users" style="margin-left: 10px; display: inline-block; background-color: #7f8c8d; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px;">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html> 