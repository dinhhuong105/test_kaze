<?php get_header(); ?>
        <div id="sb-site" class="wrapper category">
            <?php breadcrumb(); ?>
            <section class="categoryArea movie">
                <?php
                    $cat = get_queried_object();
                    $catName = $cat->name;
                    $catDiscription = $cat->description;
                    $catCount = $cat->count;
                    $catId = $cat->term_ID;
                ?>

                <h1><?php echo $catName; ?></h1>
                <p class="detail">
                    <?php echo $catDiscription; ?>
                </p>
                <p class="all">
                    全<?php echo $catCount; ?>件
                </p>
                <ul class="articleList movieList">
                    <?php
                    ?>
                    <?php
                        if (have_posts()) :
                        while(have_posts()) : the_post(); ?>
                        <li>
                            <a href="<?php the_permalink(); ?>">
                                <div class="imgArea">
                                    <?php the_post_thumbnail('movie_thumbnail'); ?>
                                </div>
                                <div class="content">
                                    <div class="articleData">
                                        <p class="data"><?php the_time('Y/m/d'); ?></p>
                                        <p class="cat"><?php echo $catName; ?></p>
                                    </div>
                                    <h2><?php the_title(); ?></h2>
                                    <div class="articleData">

                                        <p class="pv">
                                            <i class="fa fa-heart" aria-hidden="true"></i>
                                            <?php
                                                if ( function_exists ( 'wpp_get_views' ) ) {
                                                    echo wpp_get_views ( get_the_ID() ); }
                                            ?>
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </li>
                    <?php endwhile; else: ?>
                    <li class="none">該当する記事がありません。</li>
                    <?php endif; ?>
                </ul>
                <?php if (function_exists("pagination")) {
                        pagination($wp_query->max_num_pages);
//                        pagination($val->max_num_pages);
                    }
                ?>
                <?php wp_reset_postdata(); ?>
            </section>
    <?php get_footer(); ?>
