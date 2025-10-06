<?php

namespace App\Models;


use TCG\Voyager\Models\Post as VoyagerPost;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Traits\Resizable;
use TCG\Voyager\Traits\Translatable;
use App\Models\Subscriber;
use App\Notifications\NewPublicationNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class Post extends VoyagerPost  // 👈 Hérite de VoyagerPost
{
    use Translatable, Resizable;

    // Colonnes traduisibles
    protected $translatable = [
        'title', 'seo_title', 'excerpt', 'body', 
        'slug', 'meta_description', 'meta_keywords'
    ];

    // Statut pour les publications publiées
    // public const PUBLISHED = 'PUBLISHED';

    // // Protection des champs
    // protected $guarded = [];

    /**
     * Définir les actions au démarrage du modèle
     */
    // protected static function booted()
    // {
    //     parent::boot();

    //     // Déclenchement d'une action lors de la création
    //     static::created(function ($post) {
    //         Log::info('Événement created déclenché pour le post ID : ' . $post->id);

    //         // Envoi des notifications aux abonnés actifs et vérifiés
    //         $subscribers = Subscriber::where('is_active', 1)
    //             ->where('verified', '>', 0)
    //             ->get();
    //             dd('$subscribers');
    //         foreach ($subscribers as $subscriber) {
    //             $subscriber->notify(new NewPublicationNotification($post));
    //             Log::info('Notification envoyée à l’abonné : ' . $subscriber->email);
    //         }
    //     });
    // }

    // /**
    //  * Sauvegarder un post en assignant l'auteur si nécessaire
    //  */
    // public function save(array $options = [])
    // {
    //     if (!$this->author_id && Auth::user()) {
    //         $this->author_id = Auth::user()->getKey();
    //     }

    //     return parent::save($options);
    // }

    /**
     * Relation avec l'auteur (User)
     */
    // public function author()
    // {
    //     return $this->belongsTo(Voyager::modelClass('User'), 'author_id', 'id');
    // }

    /**
     * Scope : Retourner uniquement les posts publiés
     */
    // public function scopePublished(Builder $query)
    // {
    //     return $query->where('status', '=', static::PUBLISHED);
    // }

    /**
     * Relation avec les catégories
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_post', 'category_id', 'post_id');
    }

public function getImageUrlAttribute()
    {
        if (empty($this->image)) {
            return asset('path/to/default-image.jpg'); // Image par défaut si le champ est vide
        }

        // 1. Nettoyage des barres obliques : remplace les \ par /
        $cleanPath = str_replace('\\', '/', $this->image);

        // 2. Détection du type de chemin et génération de l'URL
        // Tentative pour les chemins Laravel Storage (nouveaux liens)
        // Ceux-ci commencent généralement sans préfixe comme 'images/' s'ils sont dans 'posts/'
        // et sont stockés dans storage/app/public
        if (Storage::disk('public')->exists($cleanPath)) {
            return Storage::url($cleanPath);
        }

        // Tentative pour les anciens chemins (dans public/images, public/uploads, etc.)
        // Si le chemin commence par 'images/' ou 'uploads/', utilisez asset()
        // Ajoutez d'autres préfixes si vous en avez (ex: 'data/images/')
        if (str_starts_with($cleanPath, 'images/') || str_starts_with($cleanPath, 'uploads/')) {
            // Assurez-vous que le fichier existe physiquement dans public/images ou public/uploads
            if (file_exists(public_path($cleanPath))) {
                 return asset($cleanPath);
            }
        }

        // Si l'image n'est trouvée nulle part, retournez une image par défaut
        return asset('path/to/default-image.jpg'); // Assurez-vous que ce chemin est valide
    }
}
