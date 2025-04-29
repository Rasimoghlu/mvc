<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Edit</title>
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>
    <div class="container">
        <h1>User Edit</h1>
        
        <?php if (isset($success)): ?>
            <div class="success"><?= $success ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        
        <?php if (isset($user)): ?>
            <form action="/users/<?= $user->id ?? 0 ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="PUT">
                
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= $user->name ?? '' ?>">
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= $user->email ?? '' ?>">
                </div>
                
                <div class="form-group" style="margin-top: 20px;">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="/users" style="margin-left: 10px; display: inline-block; background-color: #7f8c8d; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px;">Cancel</a>
                </div>
            </form>
        <?php else: ?>
            <p>User not found.</p>
            <a href="/users" style="display: inline-block; margin-top: 20px; background-color: #7f8c8d; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px;">Go to list</a>
        <?php endif; ?>
    </div>
</body>
</html> 