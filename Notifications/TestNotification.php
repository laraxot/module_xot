<<<<<<< HEAD
<?php

declare(strict_types=1);

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

/**
 * Class TestNotification.
 */
class TestNotification extends Notification implements ShouldQueue {
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct() {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable) {
        return ['slack'];
    }

    /**
     * @param mixed $notifiable
     *
     * @return SlackMessage
     */
    public function toSlack($notifiable) {
        return (new SlackMessage())
            ->content('This is my test message!');
    }
}
=======
<?php

declare(strict_types=1);

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

/**
 * Class TestNotification.
 */
class TestNotification extends Notification implements ShouldQueue {
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct() {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable) {
        return ['slack'];
    }

    /**
     * @param mixed $notifiable
     *
     * @return SlackMessage
     */
    public function toSlack($notifiable) {
        return (new SlackMessage())
            ->content('This is my test message!');
    }
}
>>>>>>> 3c97c308c85924a62f31c89c71edfe23450749f0
