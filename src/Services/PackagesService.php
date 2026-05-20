<?php

declare(strict_types=1);

namespace EIOTClub\Sdk\Services;

use EIOTClub\Sdk\Client;

final class PackagesService
{
    public function __construct(private readonly Client $client)
    {
    }

    public function request(string $path, array $params = []): array
    {
        return $this->client->post($path, $params);
    }

    public function orderPackage(string $iccid, string $packageCode, string $outTradeNo, ?int $renew = null): array
    {
        $params = [
            'iccid' => $iccid,
            'packageCode' => $packageCode,
            'outTradeNo' => $outTradeNo,
            'renew' => $renew,
        ];

        return $this->client->post('/api/v3/package/orderPackage', $this->filterNull($params));
    }

    public function refundPackage(string $iccid, string $orderId, string $remark): array
    {
        return $this->client->post('/api/v3/package/refundPackage', [
            'iccid' => $iccid,
            'orderId' => $orderId,
            'remark' => $remark,
        ]);
    }

    public function getOrderPackageDetails(
        int $state,
        int $page,
        int $pageSize,
        ?string $orderNumberArrStr = null,
        ?string $outTradeNoArrStr = null
    ): array
    {
        $params = [
            'state' => $state,
            'page' => $page,
            'pageSize' => $pageSize,
            'orderNumberArrStr' => $orderNumberArrStr,
            'outTradeNoArrStr' => $outTradeNoArrStr,
        ];

        return $this->client->post('/api/v3/package/getOrderPackageDetails', $this->filterNull($params));
    }

    public function listOrderPackageByIccid(string $iccid): array
    {
        return $this->client->post('/api/v3/package/listOrderPackageByIccid', ['iccid' => $iccid]);
    }

    public function listOrderPackageByProduct(string $productCode): array
    {
        return $this->client->post('/api/v3/package/listOrderPackageByProduct', ['productCode' => $productCode]);
    }

    public function packageOrderRecord(
        string $iccid,
        ?int $state = null,
        ?string $createDateStart = null,
        ?string $createDateEnd = null,
        ?string $orderIdArrStr = null,
        ?int $page = null,
        ?int $pageSize = null,
        ?string $updateDateStart = null,
        ?string $updateDateEnd = null,
        ?string $orderBy = null,
        ?string $stateArrStr = null
    ): array
    {
        $params = [
            'iccid' => $iccid,
            'state' => $state,
            'createDateStart' => $createDateStart,
            'createDateEnd' => $createDateEnd,
            'orderIdArrStr' => $orderIdArrStr,
            'page' => $page,
            'pageSize' => $pageSize,
            'updateDateStart' => $updateDateStart,
            'updateDateEnd' => $updateDateEnd,
            'orderBy' => $orderBy,
            'stateArrStr' => $stateArrStr,
        ];

        return $this->client->post('/api/v3/package/packageOrderRecord', $this->filterNull($params));
    }

    public function queryOrderPackageByPackageCode(string $packageCode): array
    {
        return $this->client->post('/api/v3/package/queryOrderPackageByPackageCode', ['packageCode' => $packageCode]);
    }

    public function queryPackage(string $iccid, string $orderId): array
    {
        return $this->client->post('/api/v3/package/queryPackage', [
            'iccid' => $iccid,
            'orderId' => $orderId,
        ]);
    }

    public function queryRefundAmount(string $iccid, string $orderId): array
    {
        return $this->client->post('/api/v3/package/queryRefundAmount', [
            'iccid' => $iccid,
            'orderId' => $orderId,
        ]);
    }

    private function filterNull(array $params): array
    {
        return array_filter($params, static fn ($v) => $v !== null);
    }
}
