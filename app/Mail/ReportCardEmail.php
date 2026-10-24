<?php

namespace App\Mail;

use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReportCardEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Student $student,
        public string $pdfPath,
        public ?string $customMessage = null,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Report Card - {$this->student->full_name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.report-card',
            with: [
                'student' => $this->student,
                'customMessage' => $this->customMessage,
            ],
        );
    }

    public function attachments(): array
    {
        if (!is_file($this->pdfPath)) {
            return [];
        }

        $filename = 'report-card-'
            . str_replace(' ', '-', strtolower($this->student->full_name))
            . '.pdf';

        return [
            Attachment::fromPath($this->pdfPath)->as($filename)->withMime('application/pdf'),
        ];
    }
}