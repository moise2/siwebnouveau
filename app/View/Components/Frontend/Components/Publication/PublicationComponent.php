<?php

namespace App\View\Components\Frontend\Components\Publication;

use App\Models\Document; // Assurez-vous d'importer le modèle Document
use Illuminate\View\Component;

class PublicationComponent extends Component
{
    public $publication;

    public function __construct(Document $publication)
    {
        $this->publication = $publication;
    }

    public function render()
    {
        return view('frontend.components.publication.publication');
    }
}
