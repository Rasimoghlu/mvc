<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Server Error</title>
    <link rel="stylesheet" href="/assets/css/error.css">
</head>
<body>
    <div class="error-container">
        <h1>500</h1>
        <h2>Server Error</h2>
        <p><?= isset($message) ? $message : 'Sorry, something went wrong. Please try again later.' ?></p>
        <a href="/">Go to home</a>
    </div>
</body>
</html> 