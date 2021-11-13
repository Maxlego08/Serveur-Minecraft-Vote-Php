<?php

namespace ServeurMinecraftVote;

/**
 * @property int $id
 * @property string $event
 */
class Event
{

    /**
     * Event ID
     * @var int
     */
    public $id;

    /**
     * Event name
     * @var string
     */
    public $event;

    /**
     * @param int $id
     * @param string $event
     */
    public function __construct(int $id, string $event)
    {
        $this->id = $id;
        $this->event = $event;
    }

    /**
     * Get events array
     *
     * @param $events
     * @return Event[]
     */
    public static function fromJson($events): array
    {
        $realEvents = [];

        foreach ($events as $event) {
            array_push($realEvents, new Event($event['id'], $event['event']));
        }

        return $realEvents;
    }

}
