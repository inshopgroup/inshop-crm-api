<?php

namespace App\Service;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

/**
 * https://developers.google.com/calendar/quickstart/php
 *
 * Class GoogleClient
 * @package App\Service
 */
class GoogleClient
{
    /**
     * @var \Google_Client
     */
    protected $googleClient;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * GoogleClient constructor.
     * @param \Google_Client $googleClient
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(\Google_Client $googleClient, EntityManagerInterface $entityManager)
    {
        $this->googleClient = $googleClient;
        $this->entityManager = $entityManager;
    }

    /**
     * @param User $user
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function init(User $user): void
    {
        $this->googleClient->setApplicationName('Google calendar sync');
        $this->googleClient->setScopes([\Google_Service_Calendar::CALENDAR]);
        $this->googleClient->setPrompt('select_account consent');
        $this->googleClient->setRedirectUri('http://localhost:8080');
        $this->googleClient->setAccessType('offline');

        $this->refresh($user);
    }

    /**
     * @param User $user
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function refresh(User $user): void
    {
        $accessToken = $user->getGoogleAccessToken();

        if ($accessToken) {
            $this->googleClient->setAccessToken($accessToken);

            if ($this->googleClient->isAccessTokenExpired()) {
                if ($this->googleClient->getRefreshToken()) {
                    $this->googleClient->fetchAccessTokenWithRefreshToken($this->googleClient->getAccessToken());

                    // Save the token
                    $user->setGoogleAccessToken(json_encode($this->googleClient->getAccessToken()));
                    $this->entityManager->flush();
                }
            }
        }
    }

    /**
     * @param User $user
     * @throws \Exception
     */
    public function auth(User $user): void
    {
        $authUrl = $this->googleClient->createAuthUrl();

        printf("Open the following link in your browser:\n%s\n", $authUrl);
        print 'Enter verification code: ';

        $authCode = trim(fgets(STDIN));
        $accessToken = $this->googleClient->fetchAccessTokenWithAuthCode($authCode);

        // Check to see if there was an error.
        if (array_key_exists('error', $accessToken)) {
            throw new \Exception(implode(', ', $accessToken));
        }

        $this->googleClient->setAccessToken($accessToken);

        $user->setIsGoogleSyncEnabled(true);
        $user->setGoogleCalendars(json_encode($this->getCalendars()));
        $user->setGoogleAccessToken(json_encode($this->googleClient->getAccessToken()));
        $this->entityManager->flush();
    }

    /**
     * @return \Google_Service_Calendar_CalendarList
     */
    public function getCalendars(): \Google_Service_Calendar_CalendarList
    {
        $service = new \Google_Service_Calendar($this->googleClient);

        return $service->calendarList->listCalendarList();
    }

    /**
     * @param Task $task
     * @param User $user
     * @return \Google_Service_Calendar_Event|null
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function insertEvent(Task $task, User $user): ?\Google_Service_Calendar_Event
    {
        if (!$user->getIsGoogleSyncEnabled() || !$user->getGoogleCalendarId()) {
            return null;
        }

        $this->init($user);

        $service = new \Google_Service_Calendar($this->googleClient);

        $googleEvent = $service->events->insert(
            $user->getGoogleCalendarId(),
            $this->createGoogleEvent($task)
        );

        $task->setGoogleEventId($googleEvent->getId());
        $this->entityManager->flush();

        return $googleEvent;
    }

    /**
     * @param Task $task
     * @param User $user
     * @return \Google_Service_Calendar_Event|null
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateEvent(Task $task, User $user): ?\Google_Service_Calendar_Event
    {
        if (!$user->getIsGoogleSyncEnabled() || !$user->getGoogleCalendarId()) {
            return null;
        }

        $this->init($user);

        $service = new \Google_Service_Calendar($this->googleClient);

        if ($task->getGoogleEventId()) {
            $googleEvent = $service->events->update(
                $user->getGoogleCalendarId(),
                $task->getGoogleEventId(),
                $this->createGoogleEvent($task)
            );

            return $googleEvent;
        }

        return null;
    }

    /**
     * @param Task $task
     * @param User $user
     * @return \expectedClass|\Google_Http_Request|null
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteEvent(Task $task, User $user)
    {
        if (!$user->getIsGoogleSyncEnabled() || !$user->getGoogleCalendarId()) {
            return null;
        }

        $this->init($user);

        $service = new \Google_Service_Calendar($this->googleClient);

        if ($task->getGoogleEventId()) {
            $response = $service->events->delete(
                $user->getGoogleCalendarId(),
                $task->getGoogleEventId()
            );

            return $response;
        }

        return null;
    }

    /**
     * @param Task $task
     * @return \Google_Service_Calendar_Event
     */
    protected function createGoogleEvent(Task $task): \Google_Service_Calendar_Event
    {
        $start = \DateTime::createFromFormat('Y-m-d H:i:s', $task->getDeadline()->format('Y-m-d') . ' 09:00:00');
        $end = (clone $start)->modify('+1 hour');

        $googleEvent = new \Google_Service_Calendar_Event(array(
            'summary' => sprintf('%s - %s', $task->getName(), $task->getClient()->getName()),
            'description' => $task->getDescription(),
            'start' => array(
                'dateTime' => $start->format('Y-m-d\TH:i:s+01:00'),
                'timeZone' => 'Europe/Warsaw',
            ),
            'end' => array(
                'dateTime' => $end->format('Y-m-d\TH:i:s+01:00'),
                'timeZone' => 'Europe/Warsaw',
            ),
            'reminders' => array(
                'useDefault' => false,
                'overrides' => array(
                    array('method' => 'email', 'minutes' => 24 * 60),
                    array('method' => 'popup', 'minutes' => 10),
                ),
            ),
        ));

        return $googleEvent;
    }
}
