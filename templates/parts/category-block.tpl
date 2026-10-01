<section class="category-block" aria-labelledby="category-{$category.id}">
    <header>
        <h2 id="category-{$category.id}">
            <a href="/category/{$category.id}">{$category.name}</a>
        </h2>
        <p>{$category.description}</p>
    </header>

    <div class="post-grid">
        {foreach $categoryPosts as $post}
            {include file='parts/post-card.tpl' post=$post}
        {foreachelse}
            <p class="empty-state">В этой категории пока нет постов.</p>
        {/foreach}
    </div>

    <p class="category-link"><a class="button" href="/category/{$category.id}">Все посты</a></p>
</section>
