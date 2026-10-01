{extends file='layout.tpl'}

{block name='content'}
    <article class="post">
        <header>
            <h1>{$post.title}</h1>
            <p class="post-meta">
                <span>Опубликовано: <time datetime="{$post.published_at|replace:' ':'T'}">{$post.published_at}</time></span>
                <span>Просмотры: {$post.views}</span>
            </p>

            {if $categories}
                <nav class="post-categories" aria-label="Категории поста">
                    <p>Категории:</p>
                    <ul>
                        {foreach $categories as $category}
                            <li><a href="/category/{$category.id}">{$category.name}</a></li>
                        {/foreach}
                    </ul>
                </nav>
            {/if}
        </header>

        <img
            class="post-image"
            src="/{$post.image_path}"
            alt="{$post.title}"
            width="800"
            height="450"
        >
        <p class="post-description">{$post.description}</p>
        <div class="post-body">{$post.body}</div>
    </article>

    {if $relatedPosts}
        <section class="related-posts" aria-labelledby="related-posts-title">
            <h2 id="related-posts-title">Похожие посты</h2>

            <div class="post-grid">
                {foreach $relatedPosts as $relatedPost}
                    {include file='parts/post-card.tpl' post=$relatedPost}
                {/foreach}
            </div>
        </section>
    {/if}
{/block}
