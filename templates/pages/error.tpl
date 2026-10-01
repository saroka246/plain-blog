{extends file='layout.tpl'}

{block name='content'}
    <section class="error-page">
        <h1>{$statusCode} — {$title}</h1>
        <p>{$message}</p>
        <p><a class="button" href="/">На главную</a></p>
    </section>
{/block}
