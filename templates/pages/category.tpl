{extends file='layout.tpl'}

{block name='content'}
    <header class="page-header">
        <h1>{$title}</h1>
        <p>{$category.description}</p>
        <p class="post-meta">Всего постов: {$totalPosts}</p>
    </header>
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
    <section aria-labelledby="category-posts-title">
        <h2 id="category-posts-title">Посты</h2>

        <div class="post-grid">
            {foreach $posts as $post}
                {include file='parts/post-card.tpl' post=$post}
            {foreachelse}
                <p class="empty-state">В этой категории пока нет постов.</p>
            {/foreach}
        </div>
    </section>
    {if $totalPages > 1}
        <nav class="pagination" aria-label="Страницы категории">
            {if $currentPage > 1}
                <a href="/category/{$category.id}?sort={$sort}&page={$currentPage - 1}" rel="prev">Назад</a>
            {/if}

            {for $pageNumber = 1 to $totalPages}
                {if $pageNumber == $currentPage}
                    <strong aria-current="page">{$pageNumber}</strong>
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
