<!DOCTYPE html>
<html>
    <head>
        <title>Superglue Login</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <h1>Superglue Login</h1>
        <form action="<?php echo \Superglue::callbackUrl('auth/login'); ?>" method="POST">
            <div>
                <input type="text" name="user" placeholder="Your username"/>
            </div>
            <div>
                <input type="password" name="pass" placeholder="Your password"/>
            </div>
            <div>
                <button type="submit">Login</button>
            </div>
        </form>
    </body>
</html>
