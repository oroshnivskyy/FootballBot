<?php
class Skype{
    /**
     * @var Bot $bot
     */
    public static $bot;
    public static function notify( $notify ){
        if ( preg_match( '#RECEIVED|SENT#Uis', $notify ) ){
            $message_id = explode( ' ', $notify );
             self::$bot->invoke( $message_id[ 1 ] );
        } else{
//            echo $notify, "\n";
            return;
        }
    }
}
