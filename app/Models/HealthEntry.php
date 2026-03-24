<?php

namespace App\Models;

use Core\Database;
use Core\Encryption;
use Core\Model;

class HealthEntry extends Model
{
    protected static string $table = 'health_entries';

    private static array $encryptedFields = ['weight', 'notes'];

    public static function createEntry(int $userId, array $data): int
    {
        $data['user_id'] = $userId;
        $data = self::encryptFields($data);
        return static::create($data);
    }

    public static function updateEntry(int $id, array $data): bool
    {
        $data = self::encryptFields($data);
        return static::update($id, $data);
    }

    public static function decryptEntry(array $entry): array
    {
        foreach (self::$encryptedFields as $field) {
            if (isset($entry[$field]) && $entry[$field] !== null) {
                $entry[$field] = Encryption::decrypt($entry[$field]);
            }
        }
        return $entry;
    }

    public static function getByUserAndDate(int $userId, string $date): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM health_entries WHERE user_id = ? AND entry_date = ?');
        $stmt->execute([$userId, $date]);
        $result = $stmt->fetch();
        return $result ? self::decryptEntry($result) : null;
    }

    public static function getEntriesForUser(int $userId, ?string $startDate = null, ?string $endDate = null): array
    {
        $db = Database::getInstance();
        $sql = 'SELECT * FROM health_entries WHERE user_id = ?';
        $params = [$userId];

        if ($startDate) {
            $sql .= ' AND entry_date >= ?';
            $params[] = $startDate;
        }
        if ($endDate) {
            $sql .= ' AND entry_date <= ?';
            $params[] = $endDate;
        }

        $sql .= ' ORDER BY entry_date ASC';
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $entries = $stmt->fetchAll();

        return array_map([self::class, 'decryptEntry'], $entries);
    }

    public static function getRecentEntries(int $userId, int $days = 7): array
    {
        $startDate = date('Y-m-d', strtotime("-{$days} days"));
        return self::getEntriesForUser($userId, $startDate);
    }

    public static function getLatestWeight(int $userId): ?string
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'SELECT weight FROM health_entries WHERE user_id = ? AND weight IS NOT NULL ORDER BY entry_date DESC LIMIT 1'
        );
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        return $result ? Encryption::decrypt($result['weight']) : null;
    }

    public static function getStreak(int $userId): int
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'SELECT entry_date FROM health_entries WHERE user_id = ? ORDER BY entry_date DESC'
        );
        $stmt->execute([$userId]);
        $dates = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        if (empty($dates)) {
            return 0;
        }

        $streak = 0;
        $checkDate = date('Y-m-d');

        // If no entry today, start from yesterday
        if ($dates[0] !== $checkDate) {
            $checkDate = date('Y-m-d', strtotime('-1 day'));
            if ($dates[0] !== $checkDate) {
                return 0;
            }
        }

        foreach ($dates as $date) {
            if ($date === $checkDate) {
                $streak++;
                $checkDate = date('Y-m-d', strtotime($checkDate . ' -1 day'));
            } else {
                break;
            }
        }

        return $streak;
    }

    public static function getDayOfWeekAverages(int $userId, ?string $startDate = null, ?string $endDate = null): array
    {
        $entries = self::getEntriesForUser($userId, $startDate, $endDate);
        $days = ['Mon' => [], 'Tue' => [], 'Wed' => [], 'Thu' => [], 'Fri' => [], 'Sat' => [], 'Sun' => []];

        foreach ($entries as $entry) {
            $dayName = date('D', strtotime($entry['entry_date']));
            if (isset($days[$dayName])) {
                $days[$dayName][] = $entry;
            }
        }

        $averages = [];
        foreach ($days as $day => $dayEntries) {
            $count = count($dayEntries);
            if ($count === 0) {
                $averages[$day] = ['calories' => 0, 'weight' => 0, 'exercise_minutes' => 0];
                continue;
            }
            $averages[$day] = [
                'calories' => round(array_sum(array_column($dayEntries, 'calories')) / $count),
                'weight' => round(array_sum(array_filter(array_column($dayEntries, 'weight'))) / max(1, count(array_filter(array_column($dayEntries, 'weight')))), 1),
                'exercise_minutes' => round(array_sum(array_column($dayEntries, 'exercise_minutes')) / $count),
            ];
        }

        return $averages;
    }

    public static function paginateForUser(int $userId, int $page = 1, int $perPage = 15): array
    {
        $result = static::paginate($page, $perPage, 'user_id = ?', [$userId], 'entry_date DESC');
        $result['data'] = array_map([self::class, 'decryptEntry'], $result['data']);
        return $result;
    }

    private static function encryptFields(array $data): array
    {
        foreach (self::$encryptedFields as $field) {
            if (isset($data[$field]) && $data[$field] !== null && $data[$field] !== '') {
                $data[$field] = Encryption::encrypt((string) $data[$field]);
            }
        }
        return $data;
    }
}
