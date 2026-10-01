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

    <h3><a href="/post/{$post.id}">{$post.title}</a></h3>
    <p>{$post.description}</p>

    <p>
        Опубликовано: <time datetime="{$post.published_at|replace:' ':'T'}">{$post.published_at}</time>
        Просмотры: {$post.views}
    </p>
</article>
