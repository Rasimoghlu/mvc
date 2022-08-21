<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
</head>
<body>


<div class="container">
    <form action="<?= $_SERVER['APP_URL'] . '/test/store'; ?>" method="post">
        <input type="hidden" name="_token" value="<?= _token(); ?>">
        <div class="form-group col-md-3 mb-3">
            <label for="name">Name</label>
            <input type="text" class="form-control" name="name" id="name" placeholder="Enter name">
            <?= error('name')?>
        </div>

        <div class="form-group col-md-3 mb-3">
            <label for="email">Email address</label>
            <input type="email" class="form-control" name="email" id="email" placeholder="Enter email">
            <?= error('email')?>
        </div>

        <div class="form-group col-md-3 mb-3">
            <label for="password">Password</label>
            <input type="password" class="form-control" name="password" id="password" placeholder="Password">
            <?= error('password')?>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Submit</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>