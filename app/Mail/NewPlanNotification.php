<?php

namespace App\Mail;

use App\Models\HostingPlan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewPlanNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $plan;
    public $email;

    /**
     * Create a new message instance.
     */
    public function __construct(HostingPlan $plan, $email = null)
    {
        $this->plan = $plan;
        $this->email = $email;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Plan: ' . $this->plan->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.subscription.new_plan',
            with: [
                'planName' => $this->plan->name,
                'planPrice' => number_format($this->plan->price, 0, ',', '.'),
                'planDescription' => $this->plan->description,
                'email' => $this->email,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
