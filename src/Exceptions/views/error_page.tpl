<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Glide Error</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>

<h1><?=$this->title?></h1>

<ul>
<?php foreach ($this->errors as $error): ?>
    <li>
        <?=$error?>
    </li>
<?php endforeach ?>
</ul>

</body>
</html>