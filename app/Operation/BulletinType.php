<?php

namespace App\Operation;

/**
 * Class BulletinType
 * @package App\Operation
 */
class BulletinType extends Record
{
    protected string $category = 'bulletin';
    protected string $project = 'bulletin_type';
    protected string $subproject = '';

    protected array $allowedFields = [
        'name',
    ];
}
