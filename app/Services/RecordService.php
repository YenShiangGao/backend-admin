<?php

namespace App\Services;


use App\Support\Utils\Time;
use Carbon\Carbon;

/**
 * Class RecordService
 * @package App\Services
 */
class RecordService
{
    /**
     * @param array $attributes
     * @return array
     */
    public function getOperationRecordsConditions(array $attributes): array
    {
        $where = [];

        if (filled($attributes['start_at']) && filled($attributes['end_at'])) {
            // format to 00:00:00 & 23:59:59
            $startAt = Carbon::parse($attributes['start_at'])->startOfDay()->toDateTimeString();
            $endAt = Carbon::parse($attributes['end_at'])->endOfDay()->toDateTimeString();

            $startAt = Time::fromEstToTaipei($startAt);
            $endAt = Time::fromEstToTaipei($endAt);
        } else {
            $startAt = now()->subWeeks(2)->startOfDay();
            $endAt = now()->endOfDay();
        }

        $where[] = ['record_time', '>=', $startAt->toDateTimeString()];
        $where[] = ['record_time', '<=', $endAt->toDateTimeString()];

        if (filled($attributes['category'])) {
            $where['category'] = $attributes['category'];
        }
        if (filled($attributes['project'])) {
            $where['project'] = $attributes['project'];
        }
        if (filled($attributes['subproject'])) {
            $where['subproject'] = $attributes['subproject'];
        }

        if (isset($attributes['operator_id'])) {
            $where['operator_id'] = $attributes['operator_id'];
        }

        return $where;
    }
}
