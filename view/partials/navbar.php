<nav class="navbar">
    <a href="/tasks" class="navbar-brand">TaskApp</a>

    <?php if (\Src\Facades\Auth::check()): ?>
        <ul class="navbar-nav">
            <li><a href="/tasks" class="<?= ($_SERVER['REQUEST_URI'] ?? '') === '/tasks' ? 'active' : '' ?>">Tasks</a></li>
            <li><a href="/tasks/create" class="<?= ($_SERVER['REQUEST_URI'] ?? '') === '/tasks/create' ? 'active' : '' ?>">New Task</a></li>
        </ul>
        <div class="navbar-user">
            <span><?= e(\Src\Facades\Auth::user()->name ?? '') ?></span>
            <form method="POST" action="/logout" style="margin:0;">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-sm btn-outline">Logout</button>
            </form>
        </div>
    <?php else: ?>
        <ul class="navbar-nav">
            <li><a href="/login">Login</a></li>
            <li><a href="/register">Register</a></li>
        </ul>
        <div></div>
    <?php endif; ?>
</nav>
