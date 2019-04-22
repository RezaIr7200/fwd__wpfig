<?php



?>

<div class="wpfig-container">
<?php foreach( $galleryImages as $attachment_id ): ?>

    <?php

    $attachment     = $this->getImage($attachment_id);
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

