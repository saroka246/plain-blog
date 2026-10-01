{extends file='layout.tpl'}

{block name='content'}
    <header class="page-header">
        <h1>{$title}</h1>
    </header>

    {foreach $categories as $category}
        {include file='parts/category-block.tpl' category=$category categoryPosts=$posts[$category.id]|default:[]}
    {foreachelse}
        <p class="empty-state">Нет постов.</p>
    {/foreach}
{/block}
