<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class FixedDepositsMaturingMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  Collection<int, \App\Models\Saving>  $savings
     */
    public function __construct(
        public User $user,
        public Collection $savings,
    ) {}

    public function envelope(): Envelope
    {
        $count = $this->savings->count();

        return new Envelope(
            subject: $count === 1
                ? 'Your FD matures this month'
                : "{$count} fixed deposits mature this month",
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'emails.savings.fixed-deposits-maturing',
            with: [
                'user' => $this->user,
                'savings' => $this->savings,
                'monthLabel' => now()->format('F Y'),
            ],
        );
    }
}
