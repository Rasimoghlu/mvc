<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details</title>
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>
    <div class="container">
        <h1>User Details</h1>
        
        <?php if (isset($success)): ?>
            <div class="success"><?= $success ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        
        <?php if (isset($user)): ?>
            <div class="user-details">
                <p><strong>ID:</strong> <?= $user->id ?? 'N/A' ?></p>
                <p><strong>Name:</strong> <?= $user->name ?? 'N/A' ?></p>
                <p><strong>Email:</strong> <?= $user->email ?? 'N/A' ?></p>
                
                <div class="actions" style="margin-top: 20px;">
                    <a href="/users/<?= $user->id ?? 0 ?>/edit" class="edit">Edit</a>
                    <form method="POST" action="/users/<?= $user->id ?? 0 ?>" style="display:inline;">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="delete" onclick="return confirm('Are you sure you want to delete this user?')" style="background: none; border: none; color: white; background-color: #e74c3c; padding: 6px 10px; border-radius: 3px; cursor: pointer;">Delete</button>
                    </form>
                    <a href="/users" style="background-color: #7f8c8d; margin-left: 10px;">Go to list</a>
                </div>
            </div>
        <?php else: ?>
            <p>User not found.</p>
            <a href="/users" style="display: inline-block; margin-top: 20px; background-color: #7f8c8d; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px;">Go to list</a>
        <?php endif; ?>
    </div>
</body>
</html> 