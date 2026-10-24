<?php

namespace App\Mail;

use App\Models\Classroom;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TimetableEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Classroom $classroom,
        public string $termName,
        public string $academicYear,
        public string $schoolName,
        public string $pdfPath,
        public string $subjectLine,
        public string $bodyText,
        public ?string $guardianName = null,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectLine,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.timetable',
            with: [
                'classroom' => $this->classroom,
                'termName' => $this->termName,
                'academicYear' => $this->academicYear,
                'schoolName' => $this->schoolName,
                'bodyText' => $this->bodyText,
                'guardianName' => $this->guardianName,
            ],
        );
    }

    public function attachments(): array
    {
        if (!is_file($this->pdfPath)) {
            return [];
        }

        $filename = 'timetable-'
            . str_replace(' ', '-', strtolower($this->classroom->name))
            . '.pdf';

        return [
            Attachment::fromPath($this->pdfPath)->as($filename)->withMime('application/pdf'),
        ];
    }
}