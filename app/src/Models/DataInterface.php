<?php
/**
 * DataInterface for the SQLite database
 * @author Freddy Giordano <freddy@megacorp.com>
 * @version 1.0.0
 * @description This is the data interface for the SQLite database.
 */
namespace App\Models;

use DateTime;

interface DataInterface
{
    public function __construct();
    public function getEmployee(string $userName, string $userEmail);
    public function getRandomLunchRecipient(int $requesterId);
    public function hasLunchForDate(int $requesterId, DateTime $date);
    public function addLunch(int $requesterId, int $recipientId, DateTime $date);
}