<?php
/**
 * MegaCorp LunchBuddies API
 * @author Freddy Giordano <freddy@megacorp.com>
 * @version 1.0.0
 * @description This is the index controller for the MegaCorp LunchBuddies API.  The purpose 
 * of this application is to randomize lunch meetings between employees to discuss long term plans 
 * for world domination.
 */
namespace App\Controllers;

use App\Models\DataConnector;
use DateTime;
use Exception;

class IndexController
{
    private $dataConnector;
    private $user;

    public function __construct()
    {
        $this->dataConnector = new DataConnector();
        if ($this->getUserAndVerify()) {
            $this->route();
        }
    }

    private function returnError($message) 
    {
        header('HTTP/1.1 400 Bad Request');
        header('Content-Type: application/json');
        echo json_encode(['error' => $message]) . PHP_EOL;
        exit;
    }

    private function getUserAndVerify()
    {
        // User information is USER_NAME and USER_EMAIL coming from headers
        // beyond company SSO
        $headers = getallheaders();
        $userName = $headers['USER_NAME'] ?? null;
        $userEmail = $headers['USER_EMAIL'] ?? null;
        if (!$userName || !$userEmail) {
            $this->returnError('User not found in headers');
        }
        $this->user = $this->dataConnector->getEmployee($userName, $userEmail)[0];
        if (!$this->user) {
            $this->returnError('User not found in database');
        }
        return true;
    }

    private function route()
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if ($method == 'POST') {
            $this->requestLunch();
        } else if ($method == 'DELETE') {
            $this->deleteRequestLunch();
        } else {
            $this->getLunches();
        }
    }

    private function getLunches()
    {
        $lunches = $this->dataConnector->getLunches($this->user['id']);
        $this->returnSuccess('Lunches retrieved successfully', ['lunches' => $lunches]);
    }

    private function requestLunch()
    {
        // validate incoming date
        $data = json_decode(file_get_contents('php://input'), true);
        $date = $data['date'] ?? null;
        if (!$date) {
            $this->returnError('Date is required');
        }
        try {
            $lunchDate = new DateTime($date);
        } catch (Exception $e) {
            $this->returnError('Invalid date: ' . $date);
        }

        if ($this->dataConnector->hasLunchForDate($this->user['id'], $lunchDate)) {
            $this->returnError('You have already created a lunch for this date');
        }
        // get random lunch recipient
        $recipient = $this->dataConnector->getRandomLunchRecipient($this->user['id']);
        if (!$recipient) {
            $this->returnError('You have already created lunches with everyone');
        }
        $recipient_id = $recipient['id'];
        
        // add lunch to database
        $this->dataConnector->addLunch($this->user['id'], $recipient_id, $lunchDate);

        $this->returnSuccess('Lunch created successfully', ['date' => ($lunchDate)->format('Y-m-d'), 'recipient' => $recipient]);
    }

    private function deleteRequestLunch()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $lunchId = $data['lunchId'] ?? null;
        if (!$lunchId) {
            $this->returnError('Lunch ID is required');
        }
        $this->dataConnector->deleteLunch($lunchId);
        $this->returnSuccess('Lunch deleted successfully');
    }

    private function returnSuccess($message, $data = [])
    {
        header('HTTP/1.1 200 OK');
        header('Content-Type: application/json');
        echo json_encode(['success' => $message, 'data' => $data]) . PHP_EOL;
        exit;
    }
}
new IndexController();