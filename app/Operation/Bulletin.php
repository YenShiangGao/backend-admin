<?php

namespace App\Operation;

use App\Repositories\BulletinTypeRepository;

/**
 * Class Bulletin
 * @package App\Operation
 */
class Bulletin extends Record
{
    protected string $category = 'bulletin';
    protected string $project = 'bulletin_main';
    protected string $subproject = '';

    protected array $allowedFields = [
        'type_id',
        'subject',
        'status',
        'content',
    ];

    protected array $renameFields = [
        'type_id' => 'type',
    ];

    protected array $securityFields = ['content'];

    protected $bulletinType;

    /**
     * @param $field
     * @param $value
     * @return false|int|mixed|string
     */
    public function transferValue($field, $value)
    {
        switch ($field) {
            case 'status':
                // 將1轉成enable
                return array_search($value, config('constants.user.status'));
            case 'type_id':

                $type = $this->getBulletinType($value);

                // 將id轉成name
                return blank($type) ? '' : $type->name;
            default:
                return $value;
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    protected function getBulletinType($id)
    {
        if (blank($this->bulletinType)) {
            // type資料量少, 用單例模式處里
            $this->bulletinType = resolve(BulletinTypeRepository::class)->all();
        }

        return collect($this->bulletinType)->firstWhere('id', $id);
    }
}
