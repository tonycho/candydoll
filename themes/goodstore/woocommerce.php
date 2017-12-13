<?php
get_header();
?>
<!-- Row for main content area -->
<?php
    if (function_exists('is_product_category') && is_product_category()) {
        $cat = get_term_by('slug', get_query_var('term'), 'product_cat');

        if (isset($cat->term_id)) {
            $sliderid = jwOpt::get_option('slider', 'off', 'category', $cat->term_id);
            if (isset($sliderid) && $sliderid != 'off' && strlen($sliderid) > 0) {
                echo '<div class="col-lg-12 builder-section">';
                echo '<div class="row category-rev-slider">';
                echo '<div class="col-lg-12">';
                echo do_shortcode('[rev_slider ' . $sliderid . ']');
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }


            if (jwOpt::get_option('_cat_description_mode', 'layout', 'category', $cat->term_id) == 'fullwidth') {
                if (isset($cat->description)) {
                    if (strlen($cat->description) > 0) {
                        echo '<div class="col-lg-12 builder-section ">';
                        echo '<div class="row">';
                        echo '<div class="col-lg-12">'; ?>

                        <?php echo do_shortcode($cat->description); ?>

                        <?php
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                }
            }
        }
    }

    if (is_shop()) {
        echo '<div class="col-lg-12 builder-section ">';
        echo '<div class="row">';
        echo '<div class="col-lg-12">'; ?>

        <?php
        // na shop page to printovalo titulek 2x
        //do_action('woocommerce_archive_description');?>

        <?php
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }


    $content_width = jwLayout::content_width();
    echo '<div id="content" class="' . implode(' ', $content_width) . ' ' . jwLayout::content_layout() . ' archive-content">';
?>
<?php if ((function_exists('is_shop') && is_shop()) && get_post_meta(get_option('woocommerce_shop_page_id'), '_display_page_name', true) == '1') {
    ?>
        <h1><?php echo get_the_title(get_option('woocommerce_shop_page_id')); ?></h1>
        <hr>
<?php

}
?>


<?php
    if (function_exists('is_product_category') && is_product_category()) {
      ?>
    <div class="product-cat-title col-lg-<?php echo $col; ?>">
        <?php
        if (isset($cat->name)) {
            $name = $cat->name;
        } else {
            $name = woocommerce_page_title(false);
        }
        jaw_template_set_var('bar_type', jwOpt::get_option('woo_bar_type', 'big'));
        jaw_template_set_var('box_title', $name);
        echo jaw_get_template_part('section_bar', 'simple-shortcodes');
        ?>

    </div>
<?php

}
?>
<div class="row">
<?php

if (is_product_category()) {
    $cat = get_term_by('slug', get_query_var('term'), 'product_cat');
    do_action('woocommerce_before_main_content');
    if (jwOpt::get_option('_cat_description_mode', 'layout', 'category', $cat->term_id) == 'layout') {
        if (isset($cat->description)) {
            if (strlen($cat->description) > 0) {
                echo '<div class="' . implode(' ', $content_width) . ' product-cat-description builder-section ">';
                ?>

                <?php echo do_shortcode(_e($cat->description)); ?>

                <?php
                echo '</div>';
            }
        }
    }
} else if (is_product_tag()) {
    $cat = get_term_by('slug', get_query_var('term'), 'product_tag');
    if (isset($cat->description)) {
        if (strlen($cat->description) > 0) {
            echo '<div class="' . implode(' ', $content_width) . ' builder-section ">';
            ?>

            <?php echo do_shortcode(_e($cat->description)); ?>

            <?php
            echo '</div>';
        }
    }
} else {
    echo '<div class="' . implode(' ', $content_width) . ' builder-section before_main_content ">';
    echo '<div class="row">';
    echo '<div class="' . implode(' ', $content_width) . ' woo_before_main_content">';
    do_action('woocommerce_before_main_content');
    echo '</div>';
    echo '</div>';
    echo '</div>';
}
 ?>
 </div>
<?php
    if (function_exists('is_product_category') && is_product_category()) {
        $args = array(
            'parent' => $cat->term_id,
            'hide_empty' => false
        );

        $terms = get_terms('product_cat', $args);

        if ($terms) {
            echo '<div class="row">';
            echo '<div class="' . implode(' ', $content_width) . ' builder-section before_main_content ">';
            echo '<ul class="product-cats">';

            foreach ($terms as $term) {
                echo '<li>';
                echo '<a href="' .  esc_url(get_term_link($term)) . '" class="' . $term->slug . '">';
                echo $term->name;
                echo "&nbsp;(";
                echo $term->count;
                echo ")";
                echo '</a>';
                echo '</li>';
            }
            echo '</ul>';
            echo '</div>';
            echo '</div>';
        }
    }
?>
<div class="row">




    <?php get_template_part('loop', 'products'); ?>

    <?php if (function_exists('is_product') && !is_product()) {
    ?>
        <div class="clear"></div>
        <?php echo jwRender::pagination(jaw_template_get_var('pagination', jwOpt::get_option('blog_pagination', 'number'))); ?>
    <?php

} ?>

    <?php if ((function_exists('is_product') && is_product())) {
    ?>
        <nav id="nav-single" class="<?php echo implode(' ', $content_width); ?>">
            <span class="nav-previous"><?php previous_post_link('%link', '<i class="icon-arrow-slide-left"></i> ' . __('Previous', 'jawtemplates')); ?></span>
            <span class="nav-next"><?php next_post_link('%link', __('Next', 'jawtemplates') . ' <i class="icon-arrow-slide-right"></i>'); ?></span>
        </nav><!-- #nav-single -->
        <div class="clear"></div>
<?php

} ?>


</div>
</div><!-- End Content row -->
<?php get_sidebar(); ?>


<?php get_footer(); ?>
