<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Actions;

class ActionReceived extends Mailable
{
    use Queueable, SerializesModels;

    public $action;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Actions $action)
    {
        $this->action = $action;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $subject = "[FNM] Action: ".$this->action->ip ." ". $this->action->action."ned at " . $this->action->attack_total_incoming_pps . " pps (UUID: ".$this->action->uuid.")";
        // Attach a packet dump if we have one...
        if(isset($this->action->packet_dump) && !is_null($this->action->packet_dump)) {
            $packets = "";
            $dump = json_decode($this->action->packet_dump, true);
            foreach($dump as $d) {
                $packets = $packets."\r\n".$d;
            }
            return $this->subject($subject)
                        ->view('emails.action-received')
                        ->attachData($packets, $this->action->uuid.'-'.time().'.pcap', [
                            'mime' => 'text/plain',
                        ]);
        }

        // Otherwise, just send a plain ol' email
        return $this->subject($subject)->view('emails.action-received');
    }
}
