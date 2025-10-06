<?php

namespace App\Http\Controllers\Voyager;

use App\Models\Institution;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use Illuminate\Http\Request;
use TCG\Voyager\Facades\Voyager;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\DB;
use TCG\Voyager\Models\Role;
use Illuminate\Support\Facades\Hash;
use TCG\Voyager\Models\DataType;

class VoyagerUtilisateurController extends VoyagerBaseController
{
    /**
     * Affiche la liste des utilisateurs.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $slug = $this->getSlug($request);
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->firstOrFail();
        
        // Utilisation de Eloquent pour récupérer le contenu
        $dataTypeContent = app($dataType->model_name)::with('role')->get();
        
        $usesSoftDeletes = in_array(SoftDeletes::class, class_uses_recursive(app($dataType->model_name)));
        $isModelTranslatable = is_bread_translatable(app($dataType->model_name));
        $showSoftDeleted = $request->get('showSoftDeleted', false);
    
        // Récupération et initialisation des actions
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
        $search = '';
    
        return Voyager::view('voyager::bread.browse', compact(
            'dataType', 
            'dataTypeContent', 
            'usesSoftDeletes', 
            'isModelTranslatable', 
            'showSoftDeleted', 
            'actions', 
            'isServerSide', 
            'showCheckboxColumn', 
            'orderColumn', 
            'search'
        ));
    }
    
    

    /**
     * Affiche le formulaire de création d'un utilisateur.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */

 
    public function create(Request $request)
    {
       


        $slug = $this->getSlug($request);
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        //$dataType = Voyager::model('DataType')->where('slug', '=', $slug)->firstOrFail();
        //$dataTypeContent = new Utilisateur();
        $dataTypeContent = new Utilisateur();

        $roles = Role::all();
        $isModelTranslatable = in_array('TCG\Voyager\Traits\Translatable', class_uses(Utilisateur::class));


        return Voyager::view('voyager::bread.edit-add', compact('dataType', 'dataTypeContent', 'roles', 'isModelTranslatable'));
    }

    /**
     * Enregistre un nouvel utilisateur.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {

        $dataType = Voyager::model('DataType')->where('slug', '=', $this->getSlug($request))->firstOrFail();

        // Validation des données
        $validatedData = $request->validate([
            'role_id' => 'required|exists:roles,id',
            'nom' => 'required|string|max:255',
            'prenoms' => 'required|string|max:255',
            'email' => 'required|email|unique:utilisateurs',
            'password' => 'required|min:8|confirmed',
        ]);

        // Hash du mot de passe
        $validatedData['password'] = Hash::make($request->password);
        
        // Création de l'utilisateur
        Utilisateur::create($validatedData);

        // Redirection avec message de succès
        return redirect()->route("voyager.{$dataType->slug}.index")
            ->with(['message' => "Utilisateur créé avec succès", 'alert-type' => 'success']);
    }

    /**
     * Affiche le formulaire de mise à jour d'un utilisateur.
     *
     * @param Request $request, int $id
     * @return \Illuminate\Http\Response
     */

    
    /**
     * Met à jour un utilisateur existant.
     *
     * @param Request $request, int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $dataType = Voyager::model('DataType')->where('slug', '=', $this->getSlug($request))->firstOrFail();
        $utilisateur = Utilisateur::findOrFail($id);

        // Validation des données
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:utilisateurs,email,' . $id,
            'password' => 'nullable|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'boolean',
            'institution_id' => 'required|exists:institutions,id'
        ]);

        // Si le mot de passe est rempli, le hacher
        if ($request->filled('password')) {
            $validatedData['password'] = Hash::make($request->password);
        } else {
            unset($validatedData['password']);
        }

        // Gestion du champ is_active (conversion en booléen si nécessaire)
        $validatedData['is_active'] = $request->has('is_active') ? 1 : 0;

        // Mise à jour de l'utilisateur
        $utilisateur->update($validatedData);

        // Redirection avec message de succès
        return redirect()->route("voyager.{$dataType->slug}.index")
            ->with(['message' => "Utilisateur mis à jour avec succès", 'alert-type' => 'success']);
    }

    /**
     * Supprime un utilisateur.
     *
     * @param Request $request, int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, $id)
    {
        $slug = $this->getSlug($request);
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->firstOrFail();
        $utilisateur = Utilisateur::findOrFail($id);

        // Suppression de l'utilisateur
        $utilisateur->delete();

        // Redirection avec message de succès
        return redirect()->route("voyager.{$dataType->slug}.index")
            ->with(['message' => "Utilisateur supprimé avec succès", 'alert-type' => 'success']);
    }


    
}
