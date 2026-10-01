<article class="post-card">
    <a href="/post/{$post.id}">
        <img
            src="/{$post.image_path}"
            alt="{$post.title}"
            width="400"
            height="225"
            loading="lazy"
        >
    </a>

    <div class="post-card-content">
        <h3><a href="/post/{$post.id}">{$post.title}</a></h3>
        <p>{$post.description}</p>

        <p class="post-meta">
            <span>Опубликовано: <time datetime="{$post.published_at|replace:' ':'T'}">{$post.published_at}</time></span>
            <span>Просмотры: {$post.views}</span>
        </p>
    </div>
</article>
