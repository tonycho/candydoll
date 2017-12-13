<?php get_header(); ?>

<!-- Row for main content area -->
<?php
$content_width = jwLayout::content_width();

echo '<div id="content" class="' . implode(' ', $content_width) . ' ' . jwLayout::content_layout() . '">';
?>
<div class="post-box row">


    <?php
    while (have_posts()) : the_post();
    
        if (jwLayout::content_layout() == 'fullwidth_sidebar') {
            $layout = 'fullwidth';
        } else {
            $layout = 'sidebar';
        }
        ?>
        <article  <?php post_class($layout . ' ' . 'content' . ' ' . implode(' ', jwLayout::content_width())) ?> id="post-<?php the_ID(); ?>">

            <header>
                <h1 class="entry-title"><?php the_title(); ?></h1>
            </header>

            <footer class="entry-meta">
                <?php
                $metadata = wp_get_attachment_metadata();
                printf('<span class="meta-prep meta-prep-entry-date">' . __('Published', 'jawtemplates') . ' </span> <span class="entry-date"><time class="entry-date" datetime="%1$s">%2$s</time></span>' . __(' at ', 'jawtemplates') . ' <a href="%3$s" title="Link to full-size image">%4$s &times; %5$s</a>' . __(' in ', 'jawtemplates') . ' <a href="%6$s" title="Return to %7$s" rel="gallery">%8$s</a>.', esc_attr(get_the_date('c')), esc_html(get_the_date()), esc_url(wp_get_attachment_url()), $metadata['width'], $metadata['height'], esc_url(get_permalink($post->post_parent)), esc_attr(strip_tags(get_the_title($post->post_parent))), get_the_title($post->post_parent)
                );
                ?>
                <?php edit_post_link(__('Edit', 'jawtemplates'), '<span class="edit-link">', '</span>'); ?>
            </footer><!-- .entry-meta -->

            <?php
            /**
             * Grab the IDs of all the image attachments in a gallery so we can get the URL of the next adjacent image in a gallery,
             * or the first image (if we're looking at the last image in a gallery), or, in a gallery of one, just the link to that image file
             */
            $attachments = array_values(get_children(array('post_parent' => $post->post_parent, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID')));
            foreach ($attachments as $k => $attachment) :
                if ($attachment->ID == $post->ID)
                    break;
            endforeach;

            $k++;
// If there is more than 1 attachment in a gallery
            if (count($attachments) > 1) :
                if (isset($attachments[$k])) :
                    // get the URL of the next image attachment
                    $next_attachment_url = get_attachment_link($attachments[$k]->ID);
                else :
                    // or get the URL of the first image attachment
                    $next_attachment_url = get_attachment_link($attachments[0]->ID);
                endif;
            else :
                // or, if there's only 1 image, get the URL of the image
                $next_attachment_url = wp_get_attachment_url();
            endif;
            ?>
            <a href="<?php echo esc_url($next_attachment_url); ?>" title="<?php the_title_attribute(); ?>" rel="attachment"><?php
                $attachment_size = apply_filters('twentytwelve_attachment_size', 'full-size');
                echo wp_get_attachment_image($post->ID, $attachment_size);
                ?>
            </a>

            <?php if (!empty($post->post_excerpt)) : ?>
                <div class="entry-caption">
                    <?php the_excerpt(); ?>
                </div>
            <?php endif; ?>


            <div class="entry-description">
                <?php the_content(); ?>
                <?php wp_link_pages(array('before' => '<div class="page-links">' . __('Pages', 'jawtemplates'), 'after' => '</div>')); ?>
            </div><!-- .entry-description -->
    </div><!-- .post box -->
    </div><!-- .content -->

    </article><!-- #post -->

<?php endwhile; // end of the loop.   ?>


<?php get_sidebar(); ?>


<?php
get_footer();
