<?php

namespace App\Mail;

use App\Models\Presensi;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class IzinSakitApproval extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Presensi $presensi,
        public string   $approvalStatus
    ) {}

    public function envelope(): Envelope
    {
        $statusLabel  = $this->presensi->status === 'izin' ? 'Izin' : 'Sakit';
        $resultLabel  = $this->approvalStatus === 'disetujui' ? 'Disetujui' : 'Ditolak';
        $namaGuru     = $this->presensi->guru->nama ?? 'Guru';

        return new Envelope(
            from   : new Address(config('mail.from.address'), config('mail.from.name')),
            subject: "[MA Attaqwa] Pengajuan {$statusLabel} Anda {$resultLabel} — {$namaGuru}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.izin_sakit_approval',
        );
    }
}
