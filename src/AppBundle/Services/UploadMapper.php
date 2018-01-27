<?php

namespace AppBundle\Services;

use Port\Steps\Step\MappingStep;

class UploadMapper
{
    const MAPPING = [
        '[Product name]' => '[name]',
        '[Cost]' => '[cost]',
        '[Discontinued]' => '[isDiscontinued]',
        '[Stock]' => '[stock]',
        '[Created]' => '[createdAt]',
        '[Product code]' => '[code]',
    ];

    public function getMapper(): MappingStep
    {
        return new MappingStep(static::MAPPING);
    }
}