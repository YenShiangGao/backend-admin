<?php

namespace App\Http\Api\Resources;

use App\Http\Api\Resources\Traits\SuccessResourceTrait;
use App\Operation\Record as OperationRecord;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Support\Utils\Time;
use Illuminate\Support\Str;

/**
 * Class OperationListResource
 * @package App\Http\Api\Resources
 */
class OperationListResource extends JsonResource
{
    use SuccessResourceTrait;

    public function toArray($request)
    {
        $record = $this->resource;

        $operation = OperationRecord::get($record->model);

        return [
            'id'         => $record->id,
            'operator'   => $record->operator->account,
            'category'   => $record->category,
            'project'    => $record->project,
            'subproject' => $record->subproject,
            'record_at'  => Time::fromTaipeiToEst($record->record_time)->toDateTimeString(),
            'action'     => array_search($record->action, config('constants.operation_records.action')),
            'model_id'   => $record->model_id,
            'model'      => Str::of($record->model)->snake(),
            'content'    => $operation->formatChangeContent($record),
        ];
    }
}
