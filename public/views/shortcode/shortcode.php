<?php

/**
 * custom function to get attachment data
 */
function wp_get_attachment( $attachment_id ) {

    $attachment = get_post( $attachment_id );
    return array(
        'alt' => get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true ),
        'caption' => $attachment->post_excerpt,
        'description' => $attachment->post_content,
        'href' => get_permalink( $attachment->ID ),
        'src' => $attachment->guid,
        'title' => $attachment->post_title
    );
}


?>

<div class="wpfig-container">
<?php foreach( $galleryImages as $attachment_id ): ?>

    <?php

    $attachment     = wp_get_attachment($attachment_id);
    $image_thumb    = wp_get_attachment_image($attachment_id, 'large');
    $image_caption  = wp_get_attachment_caption($attachment_id);
    $image_desc     = $attachment['description'];

    ?>
    <div class="wpfig__item">

        <div class="wpfig__image-wrap">
            <?php echo $image_thumb; ?>
        </div>

        <?php if($image_caption) : ?>
        <div class="wpfig__caption-wrap">
            <?php echo $image_caption; ?>
        </div>
        <?php endif; ?>

        <?php if($image_desc) : ?>
        <div class="wpfig__desc-wrap">
            <?php echo $image_desc; ?>
        </div>
        <?php endif; ?>

    </div>
<?php endforeach; ?>


</div>

