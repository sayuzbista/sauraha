<?php

namespace App\Mail;


use App\Models\ServiceProvider; 
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VendorApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $vendor;

    public function __construct(ServiceProvider $vendor)
    {
        $this->vendor = $vendor;
    }

    public function build()
    {
        return $this->subject('Vendor Registration Approved')
                    ->view('emails.vendor-approved');
    }
}
