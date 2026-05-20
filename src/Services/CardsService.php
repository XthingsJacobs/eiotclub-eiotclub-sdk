<?php

declare(strict_types=1);

namespace EIOTClub\Sdk\Services;

use EIOTClub\Sdk\CardStatus;
use EIOTClub\Sdk\Client;

final class CardsService
{
    public function __construct(private readonly Client $client)
    {
    }

    public function request(string $path, array $params = []): array
    {
        return $this->client->post($path, $params);
    }

    public function getAllCardInfo(
        int $pageNum = 1,
        int $pageSize = 500,
        ?string $cardStatus = null,
        string|array|null $iccids = null
    ): array
    {
        if ($cardStatus !== null && !in_array($cardStatus, CardStatus::values(), true)) {
            throw new \InvalidArgumentException('Invalid cardStatus.');
        }

        if (is_array($iccids)) {
            $iccids = implode(',', array_values(array_filter($iccids, static fn ($v) => $v !== null && $v !== '')));
            if ($iccids === '') {
                $iccids = null;
            }
        }

        $params = [
            'pageNum' => (string) $pageNum,
            'pageSize' => $pageSize,
            'cardStatus' => $cardStatus,
            'iccids' => $iccids,
        ];

        return $this->client->post('/api/v3/card/getAllCardInfo', $this->filterNull($params));
    }

    public function getCardsInfo(string $iccid): array
    {
        return $this->client->post('/api/v3/card/getCardsInfo', ['iccid' => $iccid]);
    }

    public function refreshUsage(string $iccid): array
    {
        return $this->client->post('/api/v3/card/refreshUsage', ['iccid' => $iccid]);
    }

    public function cancelLocation(string $iccid): array
    {
        return $this->client->post('/api/v3/card/cancelLocation', ['iccid' => $iccid]);
    }

    public function switchOptions(string $iccid): array
    {
        return $this->client->post('/api/v3/card/switchOptions', ['iccid' => $iccid]);
    }

    public function switchOperator(string $network, ?string $imsi = null, ?string $iccid = null): array
    {
        $params = [
            'network' => $network,
            'imsi' => $imsi,
            'iccid' => $iccid,
        ];

        return $this->client->post('/api/v3/card/switchOperator', $this->filterNull($params));
    }

    public function imeiUnlock(string $iccid): array
    {
        return $this->client->post('/api/v3/card/imeiUnlock', ['iccid' => $iccid]);
    }

    public function plockAddImei(string $imei, int $plockGroupId): array
    {
        return $this->client->post('/api/v3/card/plockAddImei', [
            'imei' => $imei,
            'plockGroupId' => $plockGroupId,
        ]);
    }

    public function plockAddIccids(int $plockGroupId, string $iccids, string $memo): array
    {
        return $this->client->post('/api/v3/card/plockAddIccids', [
            'plockGroupId' => $plockGroupId,
            'iccids' => $iccids,
            'memo' => $memo,
        ]);
    }

    public function bindDevice(string $iccid, string $imei): array
    {
        return $this->client->post('/api/v3/card/bindDevice', [
            'iccid' => $iccid,
            'imei' => $imei,
        ]);
    }

    public function unbindDevice(string $iccid): array
    {
        return $this->client->post('/api/v3/card/unbindDevice', ['iccid' => $iccid]);
    }

    public function transferPkg(string $oldIccid, string $newIccid, int $orderId, string $orderNumber, ?string $memo = null): array
    {
        $params = [
            'oldIccid' => $oldIccid,
            'newIccid' => $newIccid,
            'orderId' => $orderId,
            'orderNumber' => $orderNumber,
            'memo' => $memo,
        ];

        return $this->client->post('/api/v3/card/transferPkg', $this->filterNull($params));
    }

    public function reset(string $iccids): array
    {
        return $this->client->post('/api/v3/card/reset', ['iccids' => $iccids]);
    }

    public function product(string $iccid): array
    {
        return $this->client->post('/api/v3/card/product', ['iccid' => $iccid]);
    }

    public function getAgentBalance(): array
    {
        return $this->client->post('/api/v3/agent/balance');
    }

    public function queryCardNumber(): array
    {
        return $this->client->post('/api/v3/outputApi/queryCardNumber');
    }

    public function queryAccountWarningCard(int $pageNum, ?int $type = null, ?string $iccid = null): array
    {
        $params = [
            'pageNum' => $pageNum,
            'type' => $type,
            'iccid' => $iccid,
        ];

        return $this->client->post('/api/v3/outputApi/queryAccountWarningCard', $this->filterNull($params));
    }

    public function listProfitInfo(
        int $pageNum,
        int $pageSize,
        ?string $param = null,
        ?string $productCode = null,
        ?string $startDate = null,
        ?string $endDate = null,
        ?int $ifWithdrawDeposit = null,
        ?int $withdrawDepositType = null,
        ?string $orderNumber = null
    ): array
    {
        $params = [
            'pageNum' => $pageNum,
            'pageSize' => $pageSize,
            'param' => $param,
            'productCode' => $productCode,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'ifWithdrawDeposit' => $ifWithdrawDeposit,
            'withdrawDepositType' => $withdrawDepositType,
            'orderNumber' => $orderNumber,
        ];

        return $this->client->post('/api/v3/outputApi/listProfitInfo', $this->filterNull($params));
    }

    public function sendSms(string $message, string $iccid): array
    {
        return $this->client->post('/api/v3/sms/sendSms', [
            'message' => $message,
            'iccid' => $iccid,
        ]);
    }

    public function queryTask(string $ids): array
    {
        return $this->client->post('/api/v3/task/query', ['ids' => $ids]);
    }

    public function esimGoods(): array
    {
        return $this->client->post('/api/v3/esim/goods');
    }

    public function esimPurchase(string $eSIMGoodsCode, string $outTradeNo): array
    {
        return $this->client->post('/api/v3/esim/purchase', [
            'eSIMGoodsCode' => $eSIMGoodsCode,
            'outTradeNo' => $outTradeNo,
        ]);
    }

    public function esimInstall(
        string $eid,
        int $productCode,
        ?int $enable = null,
        ?string $simPackageCode = null,
        ?string $cloudSimPackageCode = null,
        ?int $renew = null
    ): array
    {
        $params = [
            'eid' => $eid,
            'productCode' => $productCode,
            'enable' => $enable,
            'simPackageCode' => $simPackageCode,
            'cloudSimPackageCode' => $cloudSimPackageCode,
            'renew' => $renew,
        ];

        return $this->client->post('/api/v3/esim/install', $this->filterNull($params));
    }

    public function esimOrderStatus(string $orderId): array
    {
        return $this->client->post('/api/v3/esim/orderStatus', ['orderId' => $orderId]);
    }

    public function esimQueryRefundAmount(string $orderId): array
    {
        return $this->client->post('/api/v3/esim/queryRefundAmount', ['orderId' => $orderId]);
    }

    public function esimRefund(string $orderId, string $remark): array
    {
        return $this->client->post('/api/v3/esim/refund', [
            'orderId' => $orderId,
            'remark' => $remark,
        ]);
    }

    private function filterNull(array $params): array
    {
        return array_filter($params, static fn ($v) => $v !== null);
    }
}
