<?php
namespace Module\Folio\Events;

use Poirot\Events\Event;
use Poirot\Events\EventHeap;

use Module\Folio\Interfaces\Model\iEntityAvatar;


class EventsHeapOfFolio
    extends EventHeap
{
    const BEFORE_CREATE_FOLIO = 'folio.before.created';
    const BEFORE_UPDATE_FOLIO = 'folio.before.updated';

    const Event_SendFollowRequest = 'SendFollowRequest';

    const AVATAR_CHANGED     = 'folio.avatar.after.created';


    /**
     * Initialize
     *
     */
    function __init()
    {
        // Before Create Folio:
        //
        $this->bind( new Event(self::BEFORE_CREATE_FOLIO, new Event\BuildEvent([
            'collector' => new DTofCreateFolio ])) );


        // Before Update Folio:
        //
        $this->bind( new Event(self::BEFORE_UPDATE_FOLIO, new Event\BuildEvent([
            'collector' => new DTofCreateFolio ])) );

        // After Avatar Uploaded:
        //
        $this->bind( new Event(self::AVATAR_CHANGED, new Event\BuildEvent([
            'collector' => new DTofUploadAvatar ])) );


        // Send Follow Request:
        //
        $this->bind( new Event(self::Event_SendFollowRequest, new Event\BuildEvent([
            'collector' => new DTofFollow ])) );
    }
}


// Events Data Transfer Objects:

class DTofCreateFolio
    extends Event\DataCollector
{
    protected $entityFolio;


    function getEntityFolio()
    {
        return $this->entityFolio;
    }

    function setEntityFolio($entityFolio)
    {
        $this->entityFolio = $entityFolio;
    }
}

class DTofUploadAvatar
    extends Event\DataCollector
{
    /** @var iEntityAvatar */
    protected $avatarEntity;


    /**
     * @return iEntityAvatar
     */
    function getAvatarEntity()
    {
        return $this->avatarEntity;
    }

    /**
     * @param mixed $avatarEntity
     */
    function setAvatarEntity($avatarEntity)
    {
        $this->avatarEntity = $avatarEntity;
    }
}

class DTofFollow
    extends Event\DataCollector
{
    protected $entityFollow;
    protected $userId;
    protected $visitorId;


    function getEntityFollow()
    {
        return $this->entityFollow;
    }

    function setEntityFollow($entityFollow)
    {
        $this->entityFollow = $entityFollow;
    }

    function getUserId()
    {
        return $this->userId;
    }

    function setUserId($userId)
    {
        $this->userId = $userId;
    }

    function getVisitorId()
    {
        return $this->visitorId;
    }

    function setVisitorId($visitorId)
    {
        $this->visitorId = $visitorId;
    }
}
