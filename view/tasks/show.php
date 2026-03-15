<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($task->title ?? 'Task') ?> - TaskApp</title>
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>
    <?php include __DIR__ . '/../partials/navbar.php'; ?>

    <div class="container" style="max-width:600px;">
        <div class="card task-detail">
            <div class="card-header">
                <h1 style="margin-bottom:0;"><?= e($task->title ?? '') ?></h1>
                <span class="badge badge-<?= e($task->status ?? 'pending') ?>">
                    <?= e(str_replace('_', ' ', $task->status ?? 'pending')) ?>
                </span>
            </div>

            <div class="meta">
                <span>Created: <?= e($task->created_at ?? '') ?></span>
                <span>Updated: <?= e($task->updated_at ?? '') ?></span>
            </div>

            <?php if (!empty($task->description)): ?>
                <div class="description"><?= e($task->description) ?></div>
            <?php else: ?>
                <p style="color:#94a3b8; font-style:italic;">No description.</p>
            <?php endif; ?>

            <div class="flex gap-2 mt-3">
                <a href="/tasks/<?= e($task->id) ?>/edit" class="btn btn-primary">Edit</a>
                <form method="POST" action="/tasks/<?= e($task->id) ?>" style="margin:0;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                </form>
                <a href="/tasks" class="btn btn-secondary">Back to List</a>
            </div>
        </div>
    </div>
</body>
</html>
