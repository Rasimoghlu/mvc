<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tasks - TaskApp</title>
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>
    <?php include __DIR__ . '/../partials/navbar.php'; ?>

    <div class="container">
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?= e($success) ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
        <?php endif; ?>

        <div class="card-header">
            <h1>My Tasks</h1>
            <a href="/tasks/create" class="btn btn-primary">+ New Task</a>
        </div>

        <!-- Filter Bar -->
        <form method="GET" action="/tasks" class="filter-bar">
            <select name="status" class="form-control" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="pending" <?= ($status ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="in_progress" <?= ($status ?? '') === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                <option value="done" <?= ($status ?? '') === 'done' ? 'selected' : '' ?>>Done</option>
            </select>
        </form>

        <?php $taskList = is_array($tasks) ? $tasks : (array)$tasks; ?>
        <?php if (!empty($taskList)): ?>
            <div class="task-list">
                <?php foreach ($taskList as $task): ?>
                    <div class="task-item">
                        <div class="task-info">
                            <h3><a href="/tasks/<?= e($task->id) ?>"><?= e($task->title) ?></a></h3>
                            <?php if (!empty($task->description)): ?>
                                <p><?= e($task->description) ?></p>
                            <?php endif; ?>
                        </div>
                        <span class="badge badge-<?= e($task->status) ?>">
                            <?= e(str_replace('_', ' ', $task->status)) ?>
                        </span>
                        <div class="task-actions">
                            <a href="/tasks/<?= e($task->id) ?>/edit" class="btn btn-sm btn-secondary">Edit</a>
                            <form method="POST" action="/tasks/<?= e($task->id) ?>" style="margin:0;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <p>No tasks found.</p>
                <a href="/tasks/create" class="btn btn-primary">Create your first task</a>
            </div>
        <?php endif; ?>
    </div>

    <?php clearErrors(); clearOld(); ?>
</body>
</html>
