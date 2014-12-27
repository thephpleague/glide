<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Glide Error</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        html {
            background: #383839;
            font-family: 'Helvetica_Neue', 'Helvetica Neue', Helvetica, Arial, sans-serif;
        }

        h1 {
            margin: 50px 0 35px 0;
            color: #ee5a35;
            text-align: center;
            font-size: 40px;
            line-height: 1;
        }

        ul {
            list-style: none;
            margin: 0 auto;
            padding: 0;
            max-width: 600px
        }

        li {
            position: relative;
            margin: 0 0 15px 0;
            background: #2c2c2c;
            border-radius: 3px;
        }

        .function {
            box-sizing: border-box;
            position: absolute;
            width: 100px;
            padding: 15px 0;
            height: 100%;
            color: white;
            font-family: "Courier New", Courier, monospace;
            font-size: 16px;
            line-height: 1.5;
            font-weight: bold;
            text-align: center;
            background: #636466;
            border-radius: 3px 0 0 3px;
        }

        .error {
            box-sizing: border-box;
            padding: 15px 15px 15px 120px;
            min-height: 30px;
            color: #7c7d80;
            font-size: 16px;
            line-height: 1.5;
        }

        code {
            color: #88898c;
            background: #404040;
            padding: 0px 5px;
            border-radius: 3px;
        }
    </style>
</head>
<body>

<h1><?=$this->title?></h1>

<ul>
<?php foreach ($this->errors as $function => $error): ?>
    <li>
        <div class="function"><?=$function?></div>
        <div class="error"><?=preg_replace('/`(.*?)`/i', '<code>$1</code>', $error)?></div>
    </li>
<?php endforeach ?>
</ul>

</body>
</html>