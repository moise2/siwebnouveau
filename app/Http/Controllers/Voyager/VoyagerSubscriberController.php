<?php

namespace App\Http\Controllers\Voyager;

use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use Illuminate\Http\Request;
use TCG\Voyager\Facades\Voyager;
use App\Models\Subscriber;
use Illuminate\Database\Eloquent\SoftDeletes;

class VoyagerSubscriberController extends VoyagerBaseController
{
    /**
     * Affiche une liste des abonnés.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $slug = $this->getSlug($request);
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();
        $dataTypeContent = Subscriber::all();

        // Vérifie si le modèle utilise SoftDeletes
        $usesSoftDeletes = in_array(SoftDeletes::class, class_uses(Subscriber::class));
        $actions = [];
        foreach (Voyager::actions() as $actionClass) {
            $action = new $actionClass($dataType, $dataTypeContent->first());
            if ($action->shouldActionDisplayOnDataType()) {
                $actions[] = $action;
            }
        }
        $isServerSide = false;
        $showCheckboxColumn = true; 
        $orderColumn = 'id';
        $isModelTranslatable = in_array('TCG\Voyager\Traits\Translatable', class_uses(Subscriber::class));
        // Passe la variable à la vue
        return Voyager::view('voyager::bread.browse', compact('dataType', 'dataTypeContent', 'usesSoftDeletes','actions','isServerSide','showCheckboxColumn', 'orderColumn','isModelTranslatable'));
    }

    /**
     * Affiche le formulaire de création d'un nouvel abonné.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $slug = $this->getSlug($request);
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();
    
        // Créer un nouvel objet Subscriber
        $dataTypeContent = new Subscriber(); // ou tu peux initialiser un tableau pour d'autres types de données
        $isModelTranslatable = in_array('TCG\Voyager\Traits\Translatable', class_uses(Subscriber::class));
        return Voyager::view('voyager::bread.edit-add', compact('dataType', 'dataTypeContent','isModelTranslatable'));
    }
    

    // Vous pouvez définir d'autres méthodes pour store, edit, update et destroy
    // pour contrôler les actions CRUD si nécessaire.
}
