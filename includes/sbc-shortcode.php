<?php

/* Shortcode */

function sbc_slider_bootstrap_carousel_function( $atts )
{
    extract( shortcode_atts( array( 'category' => '', 'template' => '' ), $atts ) );

    $args = array(
        'post_type' => 'bootstrap_carousel',
        'orderby' => 'date',
        'status' => 'publish',
        'limit' => -1,
        'posts_per_page' => -1
    );

    if( $category != '' )
    {
        $args['tax_query'] = array (
            array(
                'taxonomy'  => 'taxonomy_category',
                'field'     => 'slug',
                'terms'     => $category
            )
        );
    }

    
    $banners_query = new WP_Query( $args );

    $countPosts = $banners_query->post_count;
    $first = true;
    $count = 0;

    ob_start();
    ?>

    <div id="slider-bootstrap-carousel-<?=$category?>" class="carousel slide w-100" data-ride="carousel">

        <?php if($countPosts > 1) : ?>
            <ol class="carousel-indicators">
                <?php
                if ( $banners_query->have_posts() ) : while ( $banners_query->have_posts() ) : $banners_query->the_post();
                    $bulletClass = "";
                    if ( $count == 0 ) $bulletClass = "active";
                    ?>
                        <li data-target="#slider-bootstrap-carousel-<?=$category?>" data-slide-to="<?=$count?>" class="<?=$bulletClass?>"></li>
                    <?php
                    $count++;
                endwhile; endif;
                wp_reset_query();
                ?>
            </ol>
        <?php endif; ?>
        
        <div class="carousel-inner">
            <?php
            if ( $banners_query->have_posts() ) : while ( $banners_query->have_posts() ) : $banners_query->the_post();
                
                $link           = get_post_meta( get_the_ID(), 'image_link', true );
                $target         = get_post_meta( get_the_ID(), 'target_link', true );
                $image_size     = get_post_meta( get_the_ID(), 'image_size', true );
                $classTemplate  = ($template == '') ? 'carousel-caption' : $template;

                $item_class = $first ? "carousel-item active" : "carousel-item";

                if ($first) $first = false;
                if(wp_is_mobile()){
                    ?>
                        <div class="<?=$item_class?>">
                            <?php if ( !empty ($link ) ) : ?>
                                <a href="<?=$link;?>" target="<?=$target;?>">
                                    <img alt="<?php get_the_title()?>" src="<?php echo MultiPostThumbnails::get_post_thumbnail_url(get_post_type(), 'secondary-image', NULL, 'medium');?>" class="img-responsive"/>
                                </a>
                            <?php else: ?>
                            <img alt="<?php get_the_title()?>" src="<?php echo MultiPostThumbnails::get_post_thumbnail_url(get_post_type(), 'secondary-image', NULL, 'medium');?>" class="img-responsive"/>
                            <?php endif; ?>
                        </div><!-- carousel-item -->
                    <?php
                }
                else {
                    ?>
                        <div class="<?=$item_class?>">
                            <?php if ( !empty ($link ) ) : ?>
                                <a href="<?=$link;?>" target="<?=$target;?>">
                                    <?php the_post_thumbnail('full', array('class' => 'img-fluid ' . $image_size)); ?>
                                </a>
                            <?php else: ?>
                                <?php the_post_thumbnail('full', array('class' => 'img-fluid ' . $image_size)); ?>
                            <?php endif; ?>

                            <?php if ( has_excerpt() ) : ?>
                                <div class="<?=$classTemplate?> d-none d-md-block">
                                    <h5><?=the_title();?></h5>
                                    <p><?=the_excerpt();?></p>
                                </div>
                            <?php endif; ?>

                        </div><!-- carousel-item -->
                    <?php
                }

            endwhile; endif;
            wp_reset_postdata();
            ?>
        </div><!-- carousel-inner -->

        <?php if($countPosts > 1) : ?>
            <a class="carousel-control-prev" href="#slider-bootstrap-carousel-<?=$category?>" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#slider-bootstrap-carousel-<?=$category?>" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        <?php endif; ?>

    </div><!-- #slider-bootstrap-carousel -->
    
<?php

return ob_get_clean();

}
add_shortcode('slider_bootstrap_carousel', 'sbc_slider_bootstrap_carousel_function');
