# Coding Exercise: API Server for Lunch Buddy™

## Overview

This application has been created as a test exercise for Urban Dynamics hiring processes.  I built the application in a running LAMP stack on a VPS server I use for hosing other web applications.  The code is written in PHP and the API is written in a REST style that allows for data to be sent in using `Content-type: application/json`.  Sqlite3 is required on the server that runs this test application; the test database is in the repo, however a clean database can be started by using `$ sqlite3 urbdyn.db < schema.sql`

## Data Storage

Data storage has been accomplished on the working prototype using Sqlite3 as a placeholder.  It is written in DataConnector.sqlite.php in the Models section and has an accompanying DataInterface.php to implement when upgrading to a different data storage platorm like MySQL, PostGreSQL as some local examples, or RedShift, StarRocks vai API calls for remote examples.

## Data Authentication

Sqlite3 by default requires no authentication, but works well for testing purposes.  There is no authentication in the DataConnector class, but would be added into a different PDO connection string locally.

## Testing Endpoint
For testing purposes, this endpoint may be used with the following headers:
|Item|Value|
|---------|---------|
|Endpoint|https://gof.red/urbdyn/|
|Content-type|application/json|
|USER_NAME|Freddy Giordano [must match schema.sql]|
|USER_EMAIL|freddy@megacorp.com [must match schema.sql]|


## Requirements
1. A user can request lunch with a random other user for a particular day.
`curl --silent -X POST -H 'Content-type: application/json' -H 'USER_NAME: [username]' -H 'USER_EMAIL: [email]' -d '{"date":"[requested date]"}' [endpoint] | jq`

![Request Lunch](https://gof.red/screenshots/urbdyn-lunch-create.png)

This will go to the database and find another user at random that does not already have a lunch date planned in the future if the current user is available that day.

2. A user can view their "lunch buddy" booking details (the date of the appointment and who it's with).
`curl --silent -H 'Content-type: application/json' -H 'USER_NAME: [username]' -H 'USER_EMAIL: [email]' [endpoint] | jq`

![View Lunches](https://gof.red/screenshots/urbdyn-lunch-list.png)

This will return all current lunch dates in the database ordered by lunch date.

3. A user can cancel a given booking.
`curl --silent -X DELETE -H 'Content-type: application/json' -H 'USER_NAME: [username]' -H 'USER_EMAIL: [email]' -d '{"lunchId":[lunchId from details string]}' [endpoint] | jq`

![Cancel Lunch](https://gof.red/screenshots/urbdyn-lunch-delete.png)

## Errors
Errors are returned as HTTP 400 Bad Request and a message is included with the error.
|Error|Condition|
|-------|-------|
|User not found in headers|Either `USER_NAME` or `USER_EMAIL` was not submitted in the call|
|User not found in database|Either `USER_NAME` or `USER_EMAIL` was not found in the database|
|Date is required|No date submitted in request|
|Invalid date: [Date submitted]|Date was not parsed (e.g. 2026-17-04)|
|You have already created a lunch for this date|User already has a lunch planned that day|
|You have already created lunches with everyone|User has a future lunch date with all other employees|
|Lunch ID is required|Delete request sent without `lunchId`|


## Additional thoughts
There were a few different ideas I had that would take this from a simple exercise to a full application, but I decided to keep it simple and effective.  This also makes it much easier for the reviewer.  
* No logging was implemented in this test application, but should be in a production environment
* No framework was used as it's a small application and would not necessitate the bloat
* `curl` was used for testing the application
* Unit tests were not included today, however could be generated quickly and reviewed by AI to assure quality
* DateTime formats still saved in the database to scale up the ability to add times
* I used soft deletes so that the record would remain and there are no destructive actions