<?php
// Retrieves the stored value from the database
$seo_title = get_post_meta( get_the_ID(), 'seo_title', true );
$seo_desc = get_post_meta( get_the_ID(), 'seo_desc', true );
$seo_keyword = get_post_meta( get_the_ID(), 'seo_keyword', true );
$seo_image_url_holder= get_post_meta( get_the_ID(), 'seo_image_url_holder', true );
// Checks and displays the retrieved value
// if your title is empty nothing will show on page or post
if( !empty( $seo_title) ) { ?>
<div class="meta-info">
<h3 class="">seo_title: <?php echo $seo_title; ?></h3>
</div>
<div class="meta-info">
<h3 class="">seo_description: <?php echo $seo_desc; ?></h3>
</div>
<div class="meta-info">
<h3 class="">seo_keyword: <?php echo $seo_keyword; ?></h3>
</div>
<div class="meta-info">
<img class="" src=" <?php echo $seo_image_url_holder; ?>" />
</div>
<?php }?>