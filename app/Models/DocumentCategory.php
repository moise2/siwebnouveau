<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DocumentCategory extends Model
{
    use HasFactory;
    protected $table = 'category_document';

    protected $fillable = [
        'document_category_id',
        'document_id',
        'created_at',
        'updated_at',
    ];
    /**
     * The documents that belong to the category.
     */

    // Vérifie si une relation existe entre un document et une catégorie
    public static function exists($documentId, $categoryId)
    {
        return self::where('document_id', $categoryId)
            ->where('document_category_id', $documentId)
        
            ->exists();
    }

    public function document()
    {
        return $this->belongsTo(Document::class, 'document_category_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'document_id');
    }

    // Associe un document à une catégorie si la relation n'existe pas encore
        public static function attachIfNotExists($documentId, $categoryId)
        {
            if (!self::exists($documentId, $categoryId)) {
                return self::create([
                    'document_category_id' => $documentId, // L'ID du document
                    'document_id' => $categoryId, // L'ID de la catégorie
                ]);
            }

            return false; // Relation déjà existante
        }

        // Dissocie un document d'une catégorie
        public static function detach($documentId, $categoryId)
        {
            return self::where('document_category_id', $documentId) // L'ID du document
                ->where('document_id', $categoryId) // L'ID de la catégorie
                ->delete();
        }


}
