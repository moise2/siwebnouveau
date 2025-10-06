<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
use App\Models\Subscriber;
use App\Notifications\NewPublicationNotification;
use Carbon\Carbon; // Assurez-vous d'importer Carbon pour le formatage de date
use Illuminate\Support\Str; // Assurez-vous d'importer Str pour le slug

class Document extends Model
{
    use HasFactory;

    // protected static function booted()
    // {
    //     static::created(function ($document) {
    //         $subscribers = Subscriber::where('is_active', true)->get();
    //         foreach ($subscribers as $subscriber) {
    //             // Ici, nous passons 'document' comme type et $document comme publication
    //             $subscriber->notify(new NewPublicationNotification('document', $document));
    //         }
    //     });
    // }
    protected static function boot()
    {
        parent::boot();

        // Utiliser 'creating' pour s'exécuter UNIQUEMENT lors de la création
        static::creating(function ($document) {
            // On ne génère un slug que s'il n'a pas déjà été fourni
            if (empty($document->slug)) {
                $slug = Str::slug($document->title);
                $originalSlug = $slug;
                $count = 1;

                // Boucle pour garantir l'unicité en ajoutant un suffixe si nécessaire
                while (static::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $count++;
                }

                $document->slug = $slug;
            }
        });
    }
    protected $fillable = [
        'wp_post_id',
        'title',
        'slug', // Assurez-vous que 'slug' est bien une colonne dans votre table documents
        'description',
        'file_path',
        'file_size',
        'file_type',
        'year',
        'status',
        'access_type',
        'expiration_date',
        'download_count',
        'view_count',
        'date_publication'
    ];

    /**
     * The attributes that should be cast.
     * Pour s'assurer que date_publication est traité comme un objet Carbon.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_publication' => 'datetime', // 'datetime' est suffisant pour que Carbon le gère
    ];

    /**
     * The accessors to append to the model's array form.
     * Ces attributs seront inclus automatiquement lorsque le modèle est converti en tableau ou JSON.
     *
     * @var array
     */
    // protected $appends = ['category', 'file_url', 'download_link', 'slug'];
    protected $appends = ['category', 'file_url', 'download_link'];

    /**
     * Relation Many-to-Many avec Category.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        // CORRECTION ICI : Utilisation de vos noms de clés étrangères spécifiques dans la table pivot
        // 'category_document' est le nom de la table pivot
        // 'document_category_id' est la clé étrangère du document (de ce modèle) dans la table pivot
        // 'document_id' est la clé étrangère de la catégorie (du modèle Category) dans la table pivot
        // return $this->belongsToMany(Category::class, 'category_document', 'document_category_id', 'document_id');
        return $this->belongsToMany(Category::class, 'category_document', 'document_id', 'document_category_id');
    }

    /**
     * Accesseur pour 'category'.
     * Retourne une chaîne de noms de catégories séparés par des virgules.
     * Cela sera accessible via $document->category.
     *
     * @return string
     */
    public function getCategoryAttribute(): string
    {
        // On s'assure que la relation est chargée pour éviter une nouvelle requête N+1
        // si elle n'a pas été eager-loaded (avec 'with('categories')').
        // Il est recommandé d'assurer l'eager loading dans le service si vous utilisez cet accesseur.
        return $this->categories->pluck('name')->implode(', ');
    }

    /**
     * Accesseur pour 'file_url'.
     * Retourne l'URL publique du fichier stocké dans le système de fichiers de Laravel,
     * en gérant les chemins simples et les chemins JSON de Voyager.
     *
     * @return string|null
     */
    public function getFileUrlAttribute(): ?string
    {
        // Accès direct à l'attribut brut 'file_path'
        $filePathInDb = $this->attributes['file_path'] ?? null;

        if (is_string($filePathInDb)) {
            // Tente de décoder le JSON
            $decodedPath = json_decode($filePathInDb, true);

            // Si c'est un tableau (après décodage JSON) et qu'il a le format Voyager
            if (is_array($decodedPath) && !empty($decodedPath) && isset($decodedPath[0]['download_link'])) {
                // C'est le nouveau format Voyager (JSON)
                // Le chemin est dans 'download_link'. Remplace les backslashes par des forward slashes.
                $realFilePath = str_replace('\\', '/', $decodedPath[0]['download_link']);
                return Storage::url($realFilePath);
            } else {
                // C'est l'ancien format (simple chaîne) ou un autre format non-JSON connu
                return Storage::url($filePathInDb);
            }
        }

        // Retourne null si le chemin n'est pas une chaîne ou est vide/null
        return null;
    }

    /**
     * Accesseur pour 'download_link'.
     * Retourne l'URL de la route Laravel qui gère le téléchargement (et incrémente le compteur).
     * Cela sera accessible via $document->download_link.
     *
     * @return string|null
     */
    public function getDownloadLinkAttribute(): ?string
    {
        // La route 'documents.general.download' est maintenue comme demandé.
        // Assurez-vous que cette route existe et pointe vers votre logique de téléchargement.
        return $this->id ? route('documents.general.download', $this->id) : null;
    }

    /**
     * Accesseur pour 'slug'.
     * Retourne le slug du document.
     * Si la colonne 'slug' existe en DB, elle est prioritaire.
     * Sinon, génère un slug à la volée à partir du titre.
     * @return string
     */
    // public function getSlugAttribute(): string
    // {
    //     // Si vous avez une colonne 'slug' dans votre base de données, utilisez-la.
    //     // Assurez-vous qu'elle est remplie lors de la création/mise à jour du document.
    //     if (isset($this->attributes['slug']) && !empty($this->attributes['slug'])) {
    //         return $this->attributes['slug'];
    //     }
    //     // Sinon, générez un slug à la volée à partir du titre.
    //     // Il est fortement recommandé d'avoir une colonne 'slug' persistante pour les URLs.
    //     return Str::slug($this->title ?? ''); // Utilisez Str::slug pour générer un slug propre
    // }


    public function getIconClassAttribute(): string
    {
        // On récupère le chemin du fichier (gère le format JSON de Voyager)
        $filePathInDb = $this->attributes['file_path'] ?? null;
        $realFilePath = $filePathInDb;

        if (is_string($filePathInDb)) {
            $decodedPath = json_decode($filePathInDb, true);
            if (is_array($decodedPath) && !empty($decodedPath) && isset($decodedPath[0]['download_link'])) {
                $realFilePath = $decodedPath[0]['download_link'];
            }
        }

        if (!$realFilePath) {
            return 'fa-file'; // Icône par défaut si aucun chemin
        }

        // On extrait l'extension du fichier en minuscules
        $extension = strtolower(pathinfo($realFilePath, PATHINFO_EXTENSION));

        // On retourne la classe correspondante
        switch ($extension) {
            case 'pdf':
                return 'fa-file-pdf';
            case 'doc':
            case 'docx':
                return 'fa-file-word';
            case 'xls':
            case 'xlsx':
                return 'fa-file-excel';
            case 'ppt':
            case 'pptx':
                return 'fa-file-powerpoint';
            case 'zip':
            case 'rar':
            case '7z':
                return 'fa-file-archive';
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
            case 'webp':
                return 'fa-file-image';
            case 'txt':
                return 'fa-file-alt';
            default:
                return 'fa-file'; // Icône générique pour tout le reste
        }
    }

    public function getFormattedSizeAttribute(): string
    {
        // On récupère la taille en octets depuis la base de données
        $bytes = $this->attributes['file_size'] ?? 0;

        if ($bytes <= 0) {
            return '0 Ko';
        }

        // Tableau des unités de mesure
        $units = ['Octets', 'Ko', 'Mo', 'Go', 'To', 'Po'];

        // Calcule le logarithme pour déterminer l'unité appropriée
        $power = floor(log($bytes, 1024));

        // Formate le nombre avec 2 décimales et ajoute l'unité correspondante
        // Par exemple : 1536 octets -> 1.50 Ko
        return round($bytes / (1024 ** $power), 2) . ' ' . $units[$power];
    }
}