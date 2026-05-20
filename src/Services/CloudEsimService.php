<?php

declare(strict_types=1);

namespace EIOTClub\Sdk\Services;

use EIOTClub\Sdk\Client;

final class CloudEsimService
{
    public function __construct(private readonly Client $client)
    {
    }

    public function request(string $path, array $params = []): array
    {
        return $this->client->post($path, $params);
    }

    public function list(int $pageNum = 1, int $pageSize = 500, ?string $cardStatus = null, ?string $eids = null): array
    {
        $params = [
            'pageNum' => (string) $pageNum,
            'pageSize' => $pageSize,
            'cardStatus' => $cardStatus,
            'eids' => $eids,
        ];

        return $this->client->post('/api/v3/cloudesim/list', $this->filterNull($params));
    }

    public function get(string $eid): array
    {
        return $this->client->post('/api/v3/cloudesim/get', ['eid' => $eid]);
    }

    public function pkgList(string $eid): array
    {
        return $this->client->post('/api/v3/cloudesim/pkg/list', ['eid' => $eid]);
    }

    public function pkgRecordList(
        string $eid,
        ?string $createDateStart = null,
        ?string $createDateEnd = null,
        ?string $updateDateStart = null,
        ?string $updateDateEnd = null,
        ?string $orderIdArrStr = null,
        ?int $page = null,
        ?int $pageSize = null
    ): array
    {
        $params = [
            'eid' => $eid,
            'createDateStart' => $createDateStart,
            'createDateEnd' => $createDateEnd,
            'updateDateStart' => $updateDateStart,
            'updateDateEnd' => $updateDateEnd,
            'orderIdArrStr' => $orderIdArrStr,
            'page' => $page,
            'pageSize' => $pageSize,
        ];

        return $this->client->post('/api/v3/cloudesim/pkg/record/list', $this->filterNull($params));
    }

    public function purchase(string $eid, string $outTradeNo, string $packageCode, ?int $renew = null): array
    {
        $params = [
            'eid' => $eid,
            'outTradeNo' => $outTradeNo,
            'packageCode' => $packageCode,
            'renew' => $renew,
        ];

        return $this->client->post('/api/v3/cloudesim/purchase', $this->filterNull($params));
    }

    public function refund(string $remark, ?string $orderId = null, ?int $eshopOrderId = null): array
    {
        $params = [
            'remark' => $remark,
            'orderId' => $orderId,
            'eshopOrderId' => $eshopOrderId,
        ];

        return $this->client->post('/api/v3/cloudesim/refund', $this->filterNull($params));
    }

    public function orderStatus(?string $orderId = null, ?int $eshopOrderId = null): array
    {
        $params = [
            'orderId' => $orderId,
            'eshopOrderId' => $eshopOrderId,
        ];

        return $this->client->post('/api/v3/cloudesim/orderStatus', $this->filterNull($params));
    }

    public function queryRefundAmount(?string $orderId = null, ?int $eshopOrderId = null): array
    {
        $params = [
            'orderId' => $orderId,
            'eshopOrderId' => $eshopOrderId,
        ];

        return $this->client->post('/api/v3/cloudesim/queryRefundAmount', $this->filterNull($params));
    }

    public function refreshUsage(string $eid): array
    {
        return $this->client->post('/api/v3/cloudesim/refreshUsage', ['eid' => $eid]);
    }

    public function cancelSession(string $eid): array
    {
        return $this->client->post('/api/v3/cloudesim/cancelSession', ['eid' => $eid]);
    }

    public function transferPkg(string $oldEid, string $newEid, int $oldEshopOrderId, ?string $memo = null): array
    {
        $params = [
            'oldEid' => $oldEid,
            'newEid' => $newEid,
            'oldEshopOrderId' => $oldEshopOrderId,
            'memo' => $memo,
        ];

        return $this->client->post('/api/v3/cloudesim/transferPkg', $this->filterNull($params));
    }

    public function unlock(string $eid): array
    {
        return $this->client->post('/api/v3/cloudesim/unlock', ['eid' => $eid]);
    }

    private function filterNull(array $params): array
    {
        return array_filter($params, static fn ($v) => $v !== null);
    }
}
