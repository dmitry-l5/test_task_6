<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
        <div>Ошибка валидации</div>
        <div class="container">
            <?php
            $valid_err = static::getData('data');
            var_dump(($valid_err));
            ?>
        </div>
    </body>
</html>