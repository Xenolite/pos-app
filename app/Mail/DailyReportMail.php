<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Transaction;
use Carbon\Carbon;
class DailyReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $transactions;
public $totalSales;
public $totalProfit;
    public function __construct()
    {
    $this->transactions = Transaction::whereDate('created_at', Carbon::today())->get();
    $this->totalSales = $this->transactions->sum('total');
    $this->totalProfit = $this->transactions->sum('profit');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Daily Report Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'view.name',
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
