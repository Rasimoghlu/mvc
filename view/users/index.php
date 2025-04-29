<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List</title>
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>
    <div class="container">
        <h1>User List</h1>
        
        <div style="margin-bottom: 20px;">
            <a href="/users/create" style="display: inline-block; background-color: #27ae60; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px;">
                Add new user
            </a>
        </div>
        
        <?php if (isset($success)): ?>
            <div class="success"><?= $success ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        
        <?php if (isset($users) && count((array)$users) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ((array)$users as $user): ?>
                        <tr>
                            <td><?= $user->id ?? 'N/A' ?></td>
                            <td><?= $user->name ?? 'N/A' ?></td>
                            <td><?= $user->email ?? 'N/A' ?></td>
                            <td class="actions">
                                <a href="/users/<?= $user->id ?? 0 ?>/edit" class="edit">Edit</a>
                                <form method="POST" action="/users/<?= $user->id ?? 0 ?>" style="display:inline;">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="delete" onclick="return confirm('Are you sure you want to delete this user?')" style="background: none; border: none; color: white; background-color: #e74c3c; padding: 6px 10px; border-radius: 3px; cursor: pointer;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>User not found.</p>
        <?php endif; ?>
    </div>
</body>
</html> 