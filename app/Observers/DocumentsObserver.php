<?php

namespace App\Observers;

use App\Models\Document;
use App\Models\Subscriber;
use App\Notifications\NewPublicationNotification;

class DocumentsObserver
{
    /**
     * Handle the Document "created" event.
     *
     * @param  \App\Models\Document  $document
     * @return void
     */
    public function created(Document $document)
    {
        $subscribers = Subscriber::where('is_active', true)
            ->where('verified', '=', 1)
            ->get();

        // Envoyer la notification à chaque abonné
        foreach ($subscribers as $subscriber) {
            // $subscriber->notify(new NewPublicationNotification($document));
            $subscriber->notify(new NewPublicationNotification('document', $document));
        }
    }

    /**
     * Handle the Document "updated" event.
     *
     * @param  \App\Models\Document  $document
     * @return void
     */
    public function updated(Document $document)
    {
        //
    }

    /**
     * Handle the Document "deleted" event.
     *
     * @param  \App\Models\Document  $document
     * @return void
     */
    public function deleted(Document $document)
    {
        //
    }

    /**
     * Handle the Document "restored" event.
     *
     * @param  \App\Models\Document  $document
     * @return void
     */
    public function restored(Document $document)
    {
        //
    }

    /**
     * Handle the Document "force deleted" event.
     *
     * @param  \App\Models\Document  $document
     * @return void
     */
    public function forceDeleted(Document $document)
    {
        //
    }
}
