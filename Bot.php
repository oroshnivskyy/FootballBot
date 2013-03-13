<?php
class Bot{
    private $last_id;
    private $proxy;
    private $votes;
    private $firstVotedTime;

    public function getVotes(){
        return $this->votes;
    }
    
    public function clearVoting($chat){
        unset($this->votes[$chat]);
    }

    public function __construct( $proxy ){
        $this->proxy = $proxy;
    }

    public function invoke( $message_id ){
        list( $chat, $message, $message_id, $author ) = $this->getDetails( $message_id );

        $this->reply( $chat, $message, $message_id, $author );
    }

    public function getDetails( $message_id ){

        $ch = $this->proxy->Invoke( 'GET CHATMESSAGE ' . $message_id . ' CHATNAME' ); //Get chat id
        $mess = $this->proxy->Invoke( 'GET CHATMESSAGE ' . $message_id . ' BODY' ); //Get message text
        $aut = $this->proxy->Invoke( 'GET CHATMESSAGE ' . $message_id . ' FROM_DISPNAME' ); //Get message author

        $author = explode( 'FROM_DISPNAME ', $aut );
        $chat = explode( 'CHATNAME ', $ch );
        $message = explode( 'BODY ', $mess );

        return [ $chat[ 1 ], $message[ 1 ], $message_id, $author[ 1 ] ];
    }

    public function reply( $chat, $message, $id, $author ){
        $botReply = "[BOT]: ";
        $reply = '';

        if($id==$this->last_id){
            return;
        }else{
           $this->last_id = $id;
        }
        switch ( $message ){
            case '!clear':
                $reply = "Votes were erased!";
                $this->clearVoting($chat);
                break;
            case 'football':
            case 'go':
            case '+1':
            case '+':
                if ( !isset( $this->votes[$chat] ) ){
                    $reply = $author . ' had started voting';
                    $this->votes[$chat] = array( 'time'=>time(), "votes" => array( $author => $message) );
                    $this->firstVotedTime = time();
                } else if ( isset( $this->votes[$chat]["votes"][ $author ] ) ){
                    $reply = "You already voted!";
                    break;
                } else{
                    $this->votes[$chat]["votes"][ $author ] = $message;
                    $reply = "Vote was added";
                }
                if ( count( $this->votes[$chat]["votes"] ) >= 4 ){
                    $reply = "Got a quorum, " . implode( " ", array_keys( $this->votes[$chat]["votes"] ) ) . " lets play a football!";
                } else{
                    $reply .= ".\n Need " . ( 4 - count( $this->votes[$chat]["votes"] ) ) . " votes to make a quorum!";
                }
                break;
            case '!test':
                $reply = 'It\'s work!';
                break;

            case '!help':
                $reply = 'Use football, go, + or +1';
                break;
        }
        if ( isset($reply) && $reply != '' ){
            $replyMessage = $botReply . $reply;
            $this->proxy->Invoke( 'CHATMESSAGE ' . $chat . ' ' . $replyMessage );
        } //Send message
    }
    
    
}
