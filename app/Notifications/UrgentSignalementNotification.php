<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Signalement;

class UrgentSignalementNotification extends Notification
{
    use Queueable;

    protected $signalement;

    // Constructeur
    public function __construct($signalement)
    {
        $this->signalement = $signalement;
    }

    // Canal(s) de notification (ici on utilise 'database')
    public function via($notifiable)
    {
        return ['database'];
    }

    // Les données envoyées dans la table `notifications`
    public function toDatabase($notifiable)
    {
        return [
            'signalement_id' => $this->signalement->id,
            'titre' => $this->signalement->titre,
            'description' => $this->signalement->description,
            'urgent' => $this->signalement->urgent,
            'soumis_par' => $this->signalement->user->nom, // Si tu as une relation 'user' sur ton signalement
        ];
    }
}