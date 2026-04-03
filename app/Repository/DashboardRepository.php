<?php
declare(strict_types=1);

namespace Repository;

class DashboardRepository
{
    public function __construct(private \PDO $pdo)
    {
    }

    public function fetchVtuberRecords(): array
    {
        $statement = $this->pdo->prepare('SELECT * FROM vtuber_list');
        $statement->execute();

        return $statement->fetchAll(\PDO::FETCH_UNIQUE | \PDO::FETCH_ASSOC);
    }

    /**
     * ユーザーがリマインダ―登録しているVtuberのChannelIDを取得する
     */
    public function fetchRegisteredChannelIds(string $userId): array
    {
        $statement = $this->pdo->prepare('SELECT channel_id FROM users_notification_list WHERE id = ?');
        $statement->execute([$userId]);

        return $statement->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * ユーザーIDとChannelIDを紐付けたレコードをNotificationListに追加する
     * @param array $channelIds 登録するChannelIdの配列
     * @param array $newReminderRecords ユーザーIDとChannelIDを紐付けたレコードの配列
     */
    public function insertNotificationList(array $channelIds, array $newReminderRecords): void
    {
        $placeholders = implode(',', array_fill(0, \count($channelIds), '(?, ?)'));
        $statement = $this->pdo->prepare("INSERT INTO users_notification_list (id, channel_id) VALUES {$placeholders}");

        $statement->execute(array_merge(...$newReminderRecords));
    }
}