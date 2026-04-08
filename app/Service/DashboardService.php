<?php
declare(strict_types=1);

namespace Service;

use Repository\DashboardRepository;

class DashboardService
{
    public function __construct(private DashboardRepository $dashboardRepository)
    {
    }

    public function getAllVtuberData()
    {
        return $this->dashboardRepository->fetchVtuberRecords();
    }

    /**
     * ユーザーがリマインダ―登録したchannelIdを取得する
     */
    public function getRegisteredChannelIds(string $userId): array
    {
        return $this->dashboardRepository->fetchRegisteredChannelIds($userId);
    }

    /**
     * ユーザーが選択したchannelIdとユーザーがリマインダ―登録しているchannelIdを比較する<br>
     * 未登録のChannelIdが含まれていれば、該当するChannelIdを返す
     * @return array 未登録のChannelId
     */
    public function fetchUnregisteredChannelIds(array $selectedChannelIds, string $userId): array
    {
        $registeredChannelIds = $this->dashboardRepository->fetchRegisteredChannelIds($userId);

        return array_values(array_diff($selectedChannelIds, $registeredChannelIds));
    }

    /**
     * ユーザーIDと未登録ChannelIDを紐付ける
     * @param array $unregisteredChannelIds 未登録のchannelId
     * @param string $userId ユーザーID
     */
    public function assignUserIdToUnregisteredChannelIds(array $unregisteredChannelIds, string $userId): void
    {
        $this->dashboardRepository->insertNotificationList($unregisteredChannelIds, $userId);
    }
}