<?php global $yith_wcwl; ?>
<a  href="<?php echo $yith_wcwl->get_wishlist_url(); ?>">
    <span class="topbar-title-icon icon-star3 "></span>
    <span class="topbar-title-text">
        <?php _e('[:en]Wishlist[:zh]愿望清单[:hk]願望清單[:]', 'jawtemplates'); ?>
    </span>
    <span class="topbar-wishlist-count">
        (<?php echo $yith_wcwl->count_products(); ?>)
    </span>
</a>
