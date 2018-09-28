<?php
namespace Module\Folio\Interfaces\Model;


interface iEntityFollow
{
    const STAT_PENDING  = 'pending';
    const STAT_REJECTED = 'rejected';
    const STAT_ACCEPTED = 'accepted';


    /**
     * Get Unique Identifier
     *
     * @ignore
     */
    function getUid();

    /**
     * Get Requester(Outgoing) Unique Identifier
     *
     * @return mixed
     */
    function getOutgoing();

    /**
     * Get Receiver(Outgoing) Unique Identifier
     *
     * @return mixed
     */
    function getIncoming();

    /**
     * Get Request Follow Stat
     *
     * @return string
     */
    function getStat();

    /**
     * Get Date Time Created
     *
     * @return \DateTime
     */
    function getDateTimeCreated();

    /**
     * Get Date Time Updated
     *
     * @return \DateTime|null
     */
    function getDateTimeUpdated();
}
