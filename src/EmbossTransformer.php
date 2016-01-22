<?php

namespace Majora\WorkshopReactPhp;

class EmbossTransformer implements ImageTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($imagePath)
    {
        $image = new \Imagick($imagePath);
        $image->thumbnailImage(300, 300, true);

        $image->embossImage( 0 , 1 );
        sleep(3);
        return $image->getImageBlob();
    }
}
