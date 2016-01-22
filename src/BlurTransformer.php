<?php

namespace Majora\WorkshopReactPhp;

class BlurTransformer implements ImageTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($imagePath)
    {
        $image = new \Imagick($imagePath);
        $image->thumbnailImage(300, 300, true);

        $image->blurImage(5,3);
        sleep(3);
        return $image->getImageBlob();
    }
}
