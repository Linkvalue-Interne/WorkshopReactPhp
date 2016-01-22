<?php

namespace Majora\WorkshopReactPhp;

class ThresholdTransformer implements ImageTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($imagePath)
    {
        $image = new \Imagick($imagePath);
        $image->thumbnailImage(300, 300, true);

        $max = $image->getQuantumRange();
        $max = $max["quantumRangeLong"];

        $image->thresholdImage(0.3 * $max);
        sleep(3);
        return $image->getImageBlob();
    }
}
