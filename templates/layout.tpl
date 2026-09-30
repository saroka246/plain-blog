<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{$title|default:'Блог'}</title>
</head>
<body>
    <header>
        <a href="/">Блог</a>
    </header>

    <main>
        {block name='content'}{/block}
    </main>

    <footer>
        <p>Блог на PHP и Smarty.</p>
    </footer>
</body>
</html>
