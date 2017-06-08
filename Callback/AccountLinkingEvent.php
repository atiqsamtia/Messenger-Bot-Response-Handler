<?php

namespace Response\Callback;

use Response\Model\Callback\AccountLinking;

class AccountLinkingEvent extends CallbackEvent
{
    const NAME = 'account_linking_event';

    /**
     * @var int
     */
    private $timestamp;

    /**
     * @var AccountLinking
     */
    private $accountLinking;

    /**
     * @param string $senderId
     * @param string $recipientId
     * @param int $timestamp
     * @param AccountLinking $accountLinking
     */
    public function __construct($senderId, $recipientId, $timestamp, AccountLinking $accountLinking)
    {
        parent::__construct($senderId, $recipientId);
        $this->timestamp = $timestamp;
        $this->accountLinking = $accountLinking;
    }

    /**
     * @return int
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @return AccountLinking
     */
    public function getAccountLinking()
    {
        return $this->accountLinking;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }
}
