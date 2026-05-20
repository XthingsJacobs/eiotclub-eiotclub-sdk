<?php

declare(strict_types=1);

namespace EIOTClub\Sdk\Services;

use EIOTClub\Sdk\Client;

final class PoolsService
{
    public function __construct(private readonly Client $client)
    {
    }

    public function request(string $path, array $params = []): array
    {
        return $this->client->post($path, $params);
    }

    public function byIccid(string $iccid): array
    {
        return $this->client->post('/api/v3/pool/by_iccid', ['iccid' => $iccid]);
    }

    public function create(string $poolTypeCode, ?string $memo = null): array
    {
        $params = [
            'pool_type_code' => $poolTypeCode,
            'memo' => $memo,
        ];

        return $this->client->post('/api/v3/pool/create', $this->filterNull($params));
    }

    public function list(?string $poolObjectIdArr = null, ?string $memo = null): array
    {
        $params = [
            'pool_object_id_arr' => $poolObjectIdArr,
            'memo' => $memo,
        ];

        return $this->client->post('/api/v3/pool/list', $this->filterNull($params));
    }

    public function delete(int $poolObjectId): array
    {
        return $this->client->post('/api/v3/pool/delete', ['pool_object_id' => $poolObjectId]);
    }

    public function pkgList(?string $iccid = null, ?int $poolObjectId = null): array
    {
        $params = [
            'iccid' => $iccid,
            'pool_object_id' => $poolObjectId,
        ];

        return $this->client->post('/api/v3/pool/pkg/list', $this->filterNull($params));
    }

    public function recordList(
        int $poolObjectId,
        int $page,
        int $pageSize,
        ?string $orderNoArr = null,
        ?string $outTradeNoArr = null,
        ?string $poolTypeCodeArr = null,
        ?string $statusCodeArr = null,
        ?int $createAtStart = null,
        ?int $createAtEnd = null,
        ?int $activeTimestampStart = null,
        ?int $activeTimestampEnd = null,
        ?int $expireTimestampStart = null,
        ?int $expireTimestampEnd = null
    ): array
    {
        $params = [
            'pool_object_id' => $poolObjectId,
            'page' => $page,
            'page_size' => $pageSize,
            'order_no_arr' => $orderNoArr,
            'out_trade_no_arr' => $outTradeNoArr,
            'pool_type_code_arr' => $poolTypeCodeArr,
            'status_code_arr' => $statusCodeArr,
            'create_at_start' => $createAtStart,
            'create_at_end' => $createAtEnd,
            'active_timestamp_start' => $activeTimestampStart,
            'active_timestamp_end' => $activeTimestampEnd,
            'expire_timestamp_start' => $expireTimestampStart,
            'expire_timestamp_end' => $expireTimestampEnd,
        ];

        return $this->client->post('/api/v3/pool/record/list', $this->filterNull($params));
    }

    public function sub(int $poolObjectId, string $poolTypeCode, string $outTradeNo): array
    {
        return $this->client->post('/api/v3/pool/sub', [
            'pool_object_id' => $poolObjectId,
            'pool_type_code' => $poolTypeCode,
            'out_trade_no' => $outTradeNo,
        ]);
    }

    public function unsub(string $poolObjectId, ?string $orderNo = null, ?string $outTradeNo = null): array
    {
        $params = [
            'pool_object_id' => $poolObjectId,
            'order_no' => $orderNo,
            'out_trade_no' => $outTradeNo,
        ];

        return $this->client->post('/api/v3/pool/unsub', $this->filterNull($params));
    }

    public function cardList(int $poolObjectId): array
    {
        return $this->client->post('/api/v3/pool/card/list', ['pool_object_id' => $poolObjectId]);
    }

    public function cardAdd(int $poolObjectId, string $iccidArr): array
    {
        return $this->client->post('/api/v3/pool/card/add', [
            'pool_object_id' => $poolObjectId,
            'iccid_arr' => $iccidArr,
        ]);
    }

    public function cardRemove(int $poolObjectId, string $iccidArr): array
    {
        return $this->client->post('/api/v3/pool/card/remove', [
            'pool_object_id' => $poolObjectId,
            'iccid_arr' => $iccidArr,
        ]);
    }

    public function cardCanAddList(int $poolObjectId, string $iccidArr): array
    {
        return $this->client->post('/api/v3/pool/card/can_add_list', [
            'pool_object_id' => $poolObjectId,
            'iccid_arr' => $iccidArr,
        ]);
    }

    private function filterNull(array $params): array
    {
        return array_filter($params, static fn ($v) => $v !== null);
    }
}
