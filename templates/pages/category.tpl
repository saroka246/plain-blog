{extends file='layout.tpl'}

{block name='content'}
    <h1>{$title}</h1>
    <p>{$category.description}</p>
    <p>Всего постов: {$totalPosts}</p>
    <form action="/category/{$category.id}" method="get" class="sort-controls">
        <input type="hidden" name="page" value="1">
        <fieldset>
            <legend>Сортировка</legend>
            <button type="submit" name="sort" value="views"{if $sort == 'views'} disabled{/if}>
                Сначала популярные
            </button>
            <button type="submit" name="sort" value="date_desc"{if $sort == 'date_desc'} disabled{/if}>
                Сначала новые
            </button>
            <button type="submit" name="sort" value="date_asc"{if $sort == 'date_asc'} disabled{/if}>
                Сначала старые
            </button>
        </fieldset>
    </form>
    <section>
        <h2 id="category-posts-title">Посты</h2>

        {foreach $posts as $post}
            {include file='parts/post-card.tpl' post=$post}
        {foreachelse}
            <p>В этой категории пока нет постов.</p>
        {/foreach}
    </section>
    {if $totalPages > 1}
        <nav class="pagination">
            {if $currentPage > 1}
                <a href="/category/{$category.id}?sort={$sort}&page={$currentPage - 1}" rel="prev">Назад</a>
            {/if}

            {for $pageNumber = 1 to $totalPages}
                {if $pageNumber == $currentPage}
                    <strong>{$pageNumber}</strong>
                {else}
                    <a href="/category/{$category.id}?sort={$sort}&page={$pageNumber}">{$pageNumber}</a>
                {/if}
            {/for}

            {if $currentPage < $totalPages}
                <a href="/category/{$category.id}?sort={$sort}&page={$currentPage + 1}" rel="next">Вперёд</a>
            {/if}
        </nav>
    {/if}
{/block}
