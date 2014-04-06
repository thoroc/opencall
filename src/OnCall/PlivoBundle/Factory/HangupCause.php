<?php

namespace OnCall\PlivoBundle\Factory;

class HangupCause
{
    static $causes = array(
        'NORMAL_CLEARING'                   => 'NORMAL_CLEARING',
        'USER_BUSY'                         => 'USER_BUSY',
        'NO_ANSWER'                         => 'NO_ANSWER',
        'ORIGINATOR_CANCEL'                 => 'ORIGINATOR_CANCEL',
        'UNSPECIFIED'                       => 'UNSPECIFIED',
        'UNALLOCATED_NUMBER'                => 'UNALLOCATED_NUMBER',
        'NO_ROUTE_TRANSIT_NET'              => 'NO_ROUTE_TRANSIT_NET',
        'NO_ROUTE_DESTINATION'              => 'NO_ROUTE_DESTINATION',
        'CHANNEL_UNACCEPTABLE'              => 'CHANNEL_UNACCEPTABLE',
        'CALL_AWARDED_DELIVERED'            => 'CALL_AWARDED_DELIVERED',
        'NO_USER_RESPONSE'                  => 'NO_USER_RESPONSE',
        'SUBSCRIBER_ABSENT'                 => 'SUBSCRIBER_ABSENT',
        'CALL_REJECTED'                     => 'CALL_REJECTED',
        'NUMBER_CHANGED'                    => 'NUMBER_CHANGED',
        'REDIRECTION_TO_NEW_DESTINATION'    => 'REDIRECTION_TO_NEW_DESTINATION',
        'EXCHANGE_ROUTING_ERROR'            => 'EXCHANGE_ROUTING_ERROR',
        'DESTINATION_OUT_OF_ORDER'          => 'DESTINATION_OUT_OF_ORDER',
        'INVALID_NUMBER_FORMAT'             => 'INVALID_NUMBER_FORMAT',
        'FACILITY_REJECTED'                 => 'FACILITY_REJECTED',
        'RESPONSE_TO_STATUS_ENQUIRY'        => 'RESPONSE_TO_STATUS_ENQUIRY',
        'NORMAL_UNSPECIFIED'                => 'NORMAL_UNSPECIFIED',
        'NORMAL_CIRCUIT_CONGESTION'         => 'NORMAL_CIRCUIT_CONGESTION',
        'NETWORK_OUT_OF_ORDER'              => 'NETWORK_OUT_OF_ORDER',
        'NORMAL_TEMPORARY_FAILURE'          => 'NORMAL_TEMPORARY_FAILURE',
        'SWITCH_CONGESTION'                 => 'SWITCH_CONGESTION',
        'ACCESS_INFO_DISCARDED'             => 'ACCESS_INFO_DISCARDED',
        'REQUESTED_CHAN_UNAVAIL'            => 'REQUESTED_CHAN_UNAVAIL',
        'PRE_EMPTED'                        => 'PRE_EMPTED',
        'FACILITY_NOT_SUBSCRIBED'           => 'FACILITY_NOT_SUBSCRIBED',
        'OUTGOING_CALL_BARRED'              => 'OUTGOING_CALL_BARRED',
        'INCOMING_CALL_BARRED'              => 'INCOMING_CALL_BARRED',
        'BEARERCAPABILITY_NOTAUTH'          => 'BEARERCAPABILITY_NOTAUTH',
        'BEARERCAPABILITY_NOTAVAIL'         => 'BEARERCAPABILITY_NOTAVAIL',
        'SERVICE_UNAVAILABLE'               => 'SERVICE_UNAVAILABLE',
        'BEARERCAPABILITY_NOTIMPL'          => 'BEARERCAPABILITY_NOTIMPL',
        'CHAN_NOT_IMPLEMENTED'              => 'CHAN_NOT_IMPLEMENTED',
        'FACILITY_NOT_IMPLEMENTED'          => 'FACILITY_NOT_IMPLEMENTED',
        'SERVICE_NOT_IMPLEMENTED'           => 'SERVICE_NOT_IMPLEMENTED',
        'INVALID_CALL_REFERENCE'            => 'INVALID_CALL_REFERENCE',
        'INCOMPATIBLE_DESTINATION'          => 'INCOMPATIBLE_DESTINATION',
        'INVALID_MSG_UNSPECIFIED'           => 'INVALID_MSG_UNSPECIFIED',
        'MANDATORY_IE_MISSING'              => 'MANDATORY_IE_MISSING',
        'MESSAGE_TYPE_NONEXIST'             => 'MESSAGE_TYPE_NONEXIST',
        'WRONG_MESSAGE'                     => 'WRONG_MESSAGE',
        'IE_NONEXIST'                       => 'IE_NONEXIST',
        'INVALID_IE_CONTENTS'               => 'INVALID_IE_CONTENTS',
        'WRONG_CALL_STATE'                  => 'WRONG_CALL_STATE',
        'RECOVERY_ON_TIMER_EXPIRE'          => 'RECOVERY_ON_TIMER_EXPIRE',
        'MANDATORY_IE_LENGTH_ERROR'         => 'MANDATORY_IE_LENGTH_ERROR',
        'PROTOCOL_ERROR'                    => 'PROTOCOL_ERROR',
        'INTERWORKING'                      => 'INTERWORKING',
        'CRASH'                             => 'CRASH',
        'SYSTEM_SHUTDOWN'                   => 'SYSTEM_SHUTDOWN',
        'LOSE_RACE'                         => 'LOSE_RACE',
        'MANAGER_REQUEST'                   => 'MANAGER_REQUEST',
        'BLIND_TRANSFER'                    => 'BLIND_TRANSFER',
        'ATTENDED_TRANSFER'                 => 'ATTENDED_TRANSFER',
        'ALLOTTED_TIMEOUT'                  => 'ALLOTTED_TIMEOUT',
        'USER_CHALLENGE'                    => 'USER_CHALLENGE',
        'MEDIA_TIMEOUT'                     => 'MEDIA_TIMEOUT',
        'PICKED_OFF'                        => 'PICKED_OFF',
        'USER_NOT_REGISTERED'               => 'USER_NOT_REGISTERED',
        'PROGRESS_TIMEOUT'                  => 'PROGRESS_TIMEOUT',
        'GATEWAY_DOWN'                      => 'GATEWAY_DOWN'
    );

    public static function getAll()
    {
        return static::$causes;
    }

    public static function getFailed()
    {
        $failed = static::$causes;
        unset($failed['NORMAL_CLEARING']);
        unset($failed['NORMAL_UNSPECIFIED']);

        return $failed;
    }
}
