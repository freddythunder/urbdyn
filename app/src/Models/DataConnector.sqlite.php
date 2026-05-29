<?php
/**
 * DataConnector for SQLite
 * @author Freddy Giordano <freddy@megacorp.com>
 * @version 1.0.0
 * @description This is the data connector for the SQLite database for testing purposes.
 */
namespace App\Models;

use PDO;
use PDOException;
use DateTime;

class DataConnector implements DataInterface
{
    private $db;

    public function __construct()
    {
        $this->db = new PDO('sqlite:' . DOCROOT . '/app/data/urbdyn.db');
    }

    public function getEmployee(string $userName, string $userEmail)
    {
        $stmt = $this->db->prepare('SELECT * FROM employees WHERE name = ? AND email = ?');
        $params = [$userName, $userEmail];
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLunches(int $requesterId)
    {
        $stmt = $this->db->prepare('SELECT l.id AS lunchId, l.date, e.name, e.email, e.phone 
                                    FROM lunches l 
                                    LEFT JOIN employees e ON e.id = l.recipient_id
                                    WHERE l.requester_id = ? 
                                    AND l.deleted IS NULL
                                    ORDER BY l.date');
        $params = [$requesterId];
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];
    }

    public function getRandomLunchRecipient(int $requesterId)
    {
        // select id from employees where id not in (select recipient_id from lunches where requester_id=1) and id != 1 order by random() limit 1;
        // added date > CURRENT_DATE to maintain history of past lunches but not future lunches
        $stmt = $this->db->prepare('SELECT id, name, email, phone FROM employees 
                                    WHERE id NOT IN (SELECT recipient_id 
                                    FROM lunches WHERE requester_id = ?
                                    AND date > CURRENT_DATE
                                    AND deleted IS NULL) 
                                    AND id != ? 
                                    ORDER BY RANDOM() 
                                    LIMIT 1');
        $params = [$requesterId, $requesterId];
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
    }

    public function hasLunchForDate(int $requesterId, DateTime $date)
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) AS count 
                                    FROM lunches 
                                    WHERE requester_id = ? 
                                    AND date = ? 
                                    AND deleted IS NULL');
        $params = [$requesterId, ($date)->format('Y-m-d')];
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['count'] > 0;
    }

    public function addLunch(int $requesterId, int $recipientId, DateTime $date)
    {
        try {
            $stmt = $this->db->prepare('INSERT INTO lunches (requester_id, recipient_id, date) 
                                        VALUES (?, ?, ?)');
            $params = [$requesterId, $recipientId, ($date)->format('Y-m-d')];
            $stmt->execute($params);
        } catch (PDOException $e) {
            die($e->getMessage());
        }
        return true;
    }

    public function deleteLunch(int $lunchId)
    {
        try {
            $stmt = $this->db->prepare('UPDATE lunches SET deleted = CURRENT_TIMESTAMP WHERE id = ?');
            $params = [$lunchId];
            $stmt->execute($params);
        } catch (PDOException $e) {
            die($e->getMessage());
        }
        return true;
    }
}