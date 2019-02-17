<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendContactUs extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(Request $request)
    {
        $subject = "School 89";
        $name = $request->name;
        $phone = $request->phone;
        $body = $request->body;

        return $this->view('mail.contactus',[
            'body'=>$body,
            'name'=>$name,
            'phone'=>$phone
        ])->subject('School 89')
        ->to('info@howdoyousee.me')
        ->from('sbjbcmail@gmail.com',$name);
    }
}
