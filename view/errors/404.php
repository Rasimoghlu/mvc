<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page not found</title>
    <link rel="stylesheet" href="/assets/css/error.css">
</head>
<body>
    <div class="error-container">
        <h1>404</h1>
        <h2>Page Not Found</h2>
        <p><?= isset($message) ? $message : 'The page you are looking for does not exist or has been moved.' ?></p>
        <a href="/">Go to home</a>
    </div>
</body>
</html> 