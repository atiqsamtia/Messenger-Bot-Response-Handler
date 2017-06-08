<?php

namespace Response;

use Response\Callback\AccountLinkingEvent;
use Response\Callback\AuthenticationEvent;
use Response\Callback\CallbackEvent;
use Response\Callback\MessageDeliveryEvent;
use Response\Callback\MessageEchoEvent;
use Response\Callback\MessageEvent;
use Response\Callback\MessageReadEvent;
use Response\Callback\PostbackEvent;
use Response\Callback\ReferralEvent;
use Response\Callback\RawEvent;
use Response\Model\Callback\AccountLinking;
use Response\Model\Callback\Delivery;
use Response\Model\Callback\Message;
use Response\Model\Callback\MessageEcho;
use Response\Model\Callback\Optin;
use Response\Model\Callback\Postback;
use Response\Model\Callback\Read;
use Response\Model\Callback\Referral;

class CallbackEventFactory
{

    public static function getData(){
       $in = file_get_contents("php://input");;
      //  file_put_contents("hello.txt",$in,FILE_APPEND);
       return  json_decode($in,true)['entry'][0]['messaging'][0];
    }

    public static function create_new(){
        return self::create(self::getData());
    }



    /**
     * @param array $payload
     *
     * @return CallbackEvent
     */
    public static function create(array $payload)
    {
        // PostbackEvent
        if (isset($payload['postback']) OR isset($payload['message']['quick_reply'])) {
            return self::createPostbackEvent($payload);
        }

        // MessageEvent & MessageEchoEvent
        if (isset($payload['message'])) {
            if (isset($payload['message']['is_echo'])) {
                return self::createMessageEchoEvent($payload);
            }

            return self::createMessageEvent($payload);
        }


        // AuthenticationEvent
        if (isset($payload['optin'])) {
            return self::createAuthenticationEvent($payload);
        }

        // AccountLinkingEvent
        if (isset($payload['account_linking'])) {
            return self::createAccountLinkingEvent($payload);
        }

        // MessageDeliveryEvent
        if (isset($payload['delivery'])) {
            return self::createMessageDeliveryEvent($payload);
        }

        // MessageReadEvent
        if (isset($payload['read'])) {
            return self::createMessageReadEvent($payload);
        }

        // ReferralEvent
        if(isset($payload['referral'])) {
            return self::createReferralEvent($payload);
        }

        return new RawEvent($payload['sender']['id'], $payload['recipient']['id'], $payload);
    }

    /**
     * @param array $payload
     *
     * @return MessageEvent
     */
    public static function createMessageEvent(array $payload)
    {
        $message = Message::create($payload['message']);
        $senderId = $payload['sender']['id'];
        $recipientId = $payload['recipient']['id'];
        $timestamp = $payload['timestamp'];

        return new MessageEvent($senderId, $recipientId, $timestamp, $message);
    }

    /**
     * @param array $payload
     *
     * @return MessageEchoEvent
     */
    public static function createMessageEchoEvent(array $payload)
    {
        $message = MessageEcho::create($payload['message']);
        $senderId = $payload['sender']['id'];
        $recipientId = $payload['recipient']['id'];
        $timestamp = $payload['timestamp'];

        return new MessageEchoEvent($senderId, $recipientId, $timestamp, $message);
    }

    /**
     * @param array $payload
     *
     * @return PostbackEvent
     */
    public static function createPostbackEvent(array $payload)
    {
        $postback = Postback::create(isset($payload['postback']) ? $payload['postback'] : $payload['message']['quick_reply']);
        $senderId = $payload['sender']['id'];
        $recipientId = $payload['recipient']['id'];
        $timestamp = $payload['timestamp'];

        return new PostbackEvent($senderId, $recipientId, $timestamp, $postback);
    }

    /**
     * @param array $payload
     *
     * @return AuthenticationEvent
     */
    public static function createAuthenticationEvent(array $payload)
    {
        $optin = Optin::create($payload['optin']);
        $senderId = $payload['sender']['id'];
        $recipientId = $payload['recipient']['id'];
        $timestamp = $payload['timestamp'];

        return new AuthenticationEvent($senderId, $recipientId, $timestamp, $optin);
    }

    /**
     * @param array $payload
     *
     * @return AccountLinkingEvent
     */
    public static function createAccountLinkingEvent(array $payload)
    {
        $accountLinking = AccountLinking::create($payload['account_linking']);
        $senderId = $payload['sender']['id'];
        $recipientId = $payload['recipient']['id'];
        $timestamp = $payload['timestamp'];

        return new AccountLinkingEvent($senderId, $recipientId, $timestamp, $accountLinking);
    }

    /**
     * @param array $payload
     *
     * @return MessageDeliveryEvent
     */
    public static function createMessageDeliveryEvent(array $payload)
    {
        $delivery = Delivery::create($payload['delivery']);
        $senderId = $payload['sender']['id'];
        $recipientId = $payload['recipient']['id'];

        return new MessageDeliveryEvent($senderId, $recipientId, $delivery);
    }

    /**
     * @param array $payload
     * 
     * @return MessageReadEvent
     */
    public static function createMessageReadEvent(array $payload)
    {
        $read = Read::create($payload['read']);
        $senderId = $payload['sender']['id'];
        $recipientId = $payload['recipient']['id'];
        $timestamp = $payload['timestamp'];

        return new MessageReadEvent($senderId, $recipientId, $timestamp, $read);
    }

    /**
     * @param array $payload
     *
     * @return ReferralEvent
     */
    public static function createReferralEvent(array $payload)
    {
        $referral = Referral::create($payload['referral']);
        $senderId = $payload['sender']['id'];
        $recipientId = $payload['recipient']['id'];
        $timestamp = $payload['timestamp'];

        return new ReferralEvent($senderId, $recipientId, $timestamp, $referral);
    }
}
