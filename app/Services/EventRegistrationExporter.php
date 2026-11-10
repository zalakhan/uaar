<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\RegistrationField;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EventRegistrationExporter
{
    /**
     * Export registrations with columns based on the event's configured form fields.
     */
    public function download(Event $event): StreamedResponse
    {
        $event->load(['formFields' => fn ($q) => $q->orderBy('sort_order')]);

        $headers = [
            'Registration No.',
            'Category',
            'Submitted At',
        ];
        if ($event->fees_enabled) {
            $headers[] = 'Fee (PKR)';
        }

        foreach ($event->formFields as $field) {
            $headers[] = $field->label;
        }

        $filename = 'registrations-'.$event->event_number.'.xlsx';

        return response()->streamDownload(function () use ($event, $headers): void {
            $writer = new Writer;
            $writer->openToFile('php://output');

            $writer->addRow(Row::fromValues($headers));

            EventRegistration::query()
                ->where('event_id', $event->id)
                ->with('answers')
                ->orderBy('submitted_at')
                ->chunk(200, function ($registrations) use ($writer, $event): void {
                    foreach ($registrations as $registration) {
                        $answers = $registration->answerMap();

                        $row = [
                            $registration->registration_number,
                            $registration->category_label,
                            $registration->submitted_at?->format('Y-m-d H:i'),
                        ];

                        if ($event->fees_enabled) {
                            $row[] = $registration->fee_amount !== null
                                ? number_format((float) $registration->fee_amount, 2, '.', '')
                                : '';
                        }

                        foreach ($event->formFields as $field) {
                            $value = $answers[$field->field_key] ?? '';
                            if ($field->field_type === RegistrationField::TYPE_FILE && $value !== '') {
                                $value = 'Uploaded: '.$value;
                            }
                            $row[] = $value;
                        }

                        $writer->addRow(Row::fromValues($row));
                    }
                });

            $writer->close();
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
