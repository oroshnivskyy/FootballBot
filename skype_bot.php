#!/usr/local/bin/php
<?php
include "Skype.php";
include "Bot.php";
$dbus = new Dbus( Dbus::BUS_SESSION, true );

$proxy = $dbus->createProxy( 'com.Skype.API', '/com/Skype', 'com.Skype.API' ); //Connect to skype
$proxy->Invoke( "NAME PHP" );
$proxy->Invoke( 'PROTOCOL 8' );

Skype::$bot = new Bot( $proxy );


$dbus->registerObject( '/com/Skype/Client', 'com.Skype.API.Client', 'Skype' ); // Register message listener

while ( 1 ){
    
    $votes = Skype::$bot->getVotes();
    if(isset($votes)){
        foreach( $votes as $chat=>$vot ){
            if( $vot['time'] < ( time() - ( 10*60*60 ) ) ){
                Skype::$bot->clearVoting($chat);
            }
        }
    }
    $s = $dbus->waitLoop( 1 );
}
