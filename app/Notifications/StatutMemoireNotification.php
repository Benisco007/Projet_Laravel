<?php

namespace App\Notifications;

use App\Models\Memoire;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StatutMemoireNotification extends Notification
{
    public function __construct(public Memoire $memoire)
    {
    }

    /**
     * Canaux : base de données (cloche + liste). Ajouter 'mail' si le SMTP est configuré.
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => $this->construireMessage(),
            'memoire_id' => $this->memoire->id,
            'titre' => $this->memoire->titre,
            'statut' => $this->memoire->statut,
            'motif_rejet' => $this->memoire->statut === 'rejete' ? $this->memoire->motif_rejet : null,
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Mise à jour de votre mémoire')
            ->line($this->construireMessage());

        if ($this->memoire->statut === 'rejete' && $this->memoire->motif_rejet) {
            $mail->line('Motif du rejet : ' . $this->memoire->motif_rejet);
        }

        return $mail;
    }

    private function construireMessage(): string
    {
        $libelle = match ($this->memoire->statut) {
            'soumis' => 'soumis',
            'en_verification' => 'en cours de vérification',
            'valide' => 'validé',
            'rejete' => 'rejeté',
            default => $this->memoire->statut,
        };

        return 'Votre mémoire "' . $this->memoire->titre . '" est maintenant : ' . $libelle . '.';
    }
}
