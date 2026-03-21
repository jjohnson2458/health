<?php

namespace Core;

use PDO;

abstract class Model
{
    protected static string $table = '';
    protected static string $primaryKey = 'id';

    public static function find(int $id): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM `' . static::$table . '` WHERE `' . static::$primaryKey . '` = ?');
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public static function where(string $column, mixed $value): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM `' . static::$table . '` WHERE `' . $column . '` = ?');
        $stmt->execute([$value]);
        return $stmt->fetchAll();
    }

    public static function whereFirst(string $column, mixed $value): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM `' . static::$table . '` WHERE `' . $column . '` = ? LIMIT 1');
        $stmt->execute([$value]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public static function create(array $data): int
    {
        $db = Database::getInstance();
        $columns = implode('`, `', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $stmt = $db->prepare('INSERT INTO `' . static::$table . '` (`' . $columns . '`) VALUES (' . $placeholders . ')');
        $stmt->execute(array_values($data));
        return (int) $db->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $db = Database::getInstance();
        $set = implode(', ', array_map(fn($col) => '`' . $col . '` = ?', array_keys($data)));
        $stmt = $db->prepare('UPDATE `' . static::$table . '` SET ' . $set . ' WHERE `' . static::$primaryKey . '` = ?');
        $values = array_values($data);
        $values[] = $id;
        return $stmt->execute($values);
    }

    public static function delete(int $id): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('DELETE FROM `' . static::$table . '` WHERE `' . static::$primaryKey . '` = ?');
        return $stmt->execute([$id]);
    }

    public static function count(string $column = '*', ?string $where = null, array $params = []): int
    {
        $db = Database::getInstance();
        $sql = 'SELECT COUNT(' . $column . ') as cnt FROM `' . static::$table . '`';
        if ($where) {
            $sql .= ' WHERE ' . $where;
        }
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetch()['cnt'];
    }
}
