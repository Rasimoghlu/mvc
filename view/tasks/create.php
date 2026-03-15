<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Task - TaskApp</title>
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>
    <?php include __DIR__ . '/../partials/navbar.php'; ?>

    <div class="container" style="max-width:600px;">
        <div class="card">
            <h1>Create Task</h1>

            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?= e($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="/tasks/store">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" class="form-control <?= error('title') ? 'is-invalid' : '' ?>" value="<?= e(old('title')) ?>" placeholder="Task title">
                    <?php if ($err = error('title')): ?>
                        <div class="field-error"><?= e($err) ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control <?= error('description') ? 'is-invalid' : '' ?>" placeholder="Optional description"><?= e(old('description')) ?></textarea>
                    <?php if ($err = error('description')): ?>
                        <div class="field-error"><?= e($err) ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-control <?= error('status') ? 'is-invalid' : '' ?>">
                        <option value="pending" <?= old('status', 'pending') === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="in_progress" <?= old('status') === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                        <option value="done" <?= old('status') === 'done' ? 'selected' : '' ?>>Done</option>
                    </select>
                    <?php if ($err = error('status')): ?>
                        <div class="field-error"><?= e($err) ?></div>
                    <?php endif; ?>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="btn btn-primary">Create Task</button>
                    <a href="/tasks" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <?php clearErrors(); clearOld(); ?>
</body>
</html>
