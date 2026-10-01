{extends file='layout.tpl'}

{block name='content'}
    <h1>{$title}</h1>

    {foreach $categories as $category}
        {include file='parts/category-block.tpl' category=$category categoryPosts=$posts[$category.id]|default:[]}
    {foreachelse}
        <p>Нет постов.</p>
    {/foreach}
{/block}
