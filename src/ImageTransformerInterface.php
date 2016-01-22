<?php

namespace Majora\WorkshopReactPhp;

interface ImageTransformerInterface
{
    /**
     * @param $imagePath
     * @return bool
     */
    public function transform($imagePath);
}
