<div class="container">
    <h2>Aktuellt</h2>
    <hr>
        <?php

        $news_query = new WP_Query(array(
            "post_type" => 'post',
            "posts_per_page" => 5
        ));

        while ($news_query->have_posts()) : $news_query->the_post();
        ?>
        <div class="container-news-item">
            <div class="meta-information"><em><?php echo get_the_date(); ?></em></div>
            <h3><a href="<?php the_permalink() ?>" title="<?php the_title_attribute();?>"><?php the_title(); ?></a></h3>
            <?php the_excerpt(); ?>
            <a href="<?php the_permalink() ?>" class="button">Läs vidare...</a>
        </div>
        <hr>
        <? endwhile;?>
</div>