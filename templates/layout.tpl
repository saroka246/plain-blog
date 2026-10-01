<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{$title|default:'Блог'}</title>
    {include file='parts/styles.tpl'}
</head>
<body>
    <header class="site-header">
        <div class="container">
            <a class="site-title" href="/">Блог</a>
        </div>
    </header>

    <main class="site-main container">
        {block name='content'}{/block}
    </main>

    <footer class="site-footer">
        <div class="container">
            <p>Блог на PHP и Smarty.</p>
        </div>
    </footer>
</body>
</html>
