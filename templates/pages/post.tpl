{extends file='layout.tpl'}

{block name='content'}
    <article class="post">
        <header>
            <h1>{$post.title}</h1>
            <p>
                Опубликовано: <time datetime="{$post.published_at|replace:' ':'T'}">{$post.published_at}</time>
                Просмотры: {$post.views}
            </p>

            {if $categories}
                <nav class="post-categories">
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
            src="/{$post.image_path}"
            alt="{$post.title}"
            width="800"
            height="450"
        >
        <p>{$post.description}</p>
        <div class="post-body">{$post.body}</div>
    </article>

    {if $relatedPosts}
        <section class="related-posts" >
            <h2>Похожие посты</h2>

            {foreach $relatedPosts as $relatedPost}
                {include file='parts/post-card.tpl' post=$relatedPost}
            {/foreach}
        </section>
    {/if}
{/block}
