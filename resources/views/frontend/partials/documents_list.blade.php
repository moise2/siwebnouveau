{{-- frontend/partials/documents_list.blade.php --}}

@php
    // Helper function to determine Font Awesome icon class based on file details
    // This PHP function mirrors the JavaScript logic for initial server-side rendering
    function getFileIconClassPhp($filePath, $fileType, $fileExtension) {
        // Priority 1: Use file extension from the database if provided
        if ($fileExtension) {
            switch (strtolower($fileExtension)) {
                case 'pdf': return 'fa-file-pdf';
                case 'doc':
                case 'docx': return 'fa-file-word';
                case 'xls':
                case 'xlsx': return 'fa-file-excel';
                case 'ppt':
                case 'pptx': return 'fa-file-powerpoint';
                case 'zip':
                case 'rar':
                case '7z': return 'fa-file-archive';
                case 'jpg':
                case 'jpeg':
                case 'png':
                case 'gif':
                case 'webp': return 'fa-file-image';
                case 'txt': return 'fa-file-alt';
                case 'csv': return 'fa-file-csv';
                case 'json': return 'fa-file-code';
                default: return 'fa-file'; // Generic icon
            }
        }

        // Priority 2: Use MIME type (fileType from DB)
        if ($fileType) {
            $lowerFileType = strtolower($fileType);
            if (str_contains($lowerFileType, 'pdf')) return 'fa-file-pdf';
            if (str_contains($lowerFileType, 'word') || str_contains($lowerFileType, 'document')) return 'fa-file-word';
            if (str_contains($lowerFileType, 'excel') || str_contains($lowerFileType, 'sheet')) return 'fa-file-excel';
            if (str_contains($lowerFileType, 'powerpoint') || str_contains($lowerFileType, 'presentation')) return 'fa-file-powerpoint';
            if (str_contains($lowerFileType, 'zip') || str_contains($lowerFileType, 'archive')) return 'fa-file-archive';
            if (str_contains($lowerFileType, 'image')) return 'fa-file-image';
            if (str_contains($lowerFileType, 'text') || str_contains($lowerFileType, 'plain')) return 'fa-file-alt';
            if (str_contains($lowerFileType, 'csv')) return 'fa-file-csv';
            if (str_contains($lowerFileType, 'json')) return 'fa-file-code';
        }

        // Fallback: Try to extract extension from filePath/URL
        if ($filePath && is_string($filePath)) {
            $parts = explode('.', $filePath);
            if (count($parts) > 1) {
                $extension = strtolower(end($parts));
                switch ($extension) {
                    case 'pdf': return 'fa-file-pdf';
                    case 'doc':
                    case 'docx': return 'fa-file-word';
                    case 'xls':
                    case 'xlsx': return 'fa-file-excel';
                    case 'ppt':
                    case 'pptx': return 'fa-file-powerpoint';
                    case 'zip':
                    case 'rar':
                    case '7z': return 'fa-file-archive';
                    case 'jpg':
                    case 'jpeg':
                    case 'png':
                    case 'gif':
                    case 'webp': return 'fa-file-image';
                    case 'txt': return 'fa-file-alt';
                    case 'csv': return 'fa-file-csv';
                    case 'json': return 'fa-file-code';
                    default: return 'fa-file';
                }
            }
        }

        return 'fa-file'; // Default icon if all attempts fail
    }

    // Function to format date (mirroring JS function)
    function formatDatePhp($dateStr) {
        if (empty($dateStr)) return "Date non disponible";
        $formatter = new IntlDateFormatter(
            'fr_FR',
            IntlDateFormatter::LONG,
            IntlDateFormatter::NONE,
            'Europe/Paris', // Or your desired timezone
            IntlDateFormatter::GREGORIAN
        );
        return $formatter->format(new DateTime($dateStr));
    }
@endphp

@forelse($documents as $doc)
    @php
        $iconClass = getFileIconClassPhp(
            $doc->file_url ?? null,
            $doc->file_type ?? null,
            $doc->file_extension ?? null // Assuming you have file_extension in your Document model/data
        );
        $showUrl = $routes['show'] ? str_replace(':id', $doc->id, $routes['show']) : '#';
        $downloadUrl = $doc->download_link ?? ($routes['download'] ? str_replace(':id', $doc->id, $routes['download']) : '#');
    @endphp
    <div class="col-12 col-md-4 col-lg-3 mb-4 document-card">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body d-flex flex-column">
                <!-- <a href="{{ $showUrl }}" target="_blank"
                   class="text-decoration-none text-dark d-flex align-items-center gap-2 mb-3">
                    <i class="fas {{ $iconClass }} fa-2x text-danger" style="font-size: 1rem"></i>
                    <h3 class="card-title mb-0" style="font-size: 0.9rem">{{ ucfirst($doc->title ?? "Titre non disponible") }}</h3>
                </a> -->

                <a href="{{ route($routes['show'], ['slug' => $doc->slug]) }}"
                   target="_blank"
                   class="text-decoration-none text-dark d-flex align-items-center gap-2 mb-3">
                   <i class="fas {{ $iconClass }} fa-2x text-danger" style="font-size: 1rem"></i>
                   {{-- CORRECTION : Utilisation de la syntaxe objet -> --}}
                   <h3 class="card-title mb-0" style="font-size: 0.9rem">{{ ucfirst(strtolower($doc->title ?? 'Titre non disponible')) }}</h3>
                </a>

                <p class="download-count text-muted mb-2">
                    <i class="fas fa-file-download" style="font-size: 0.8rem"></i> {{ $doc->download_count ?? 0 }} téléchargements
                </p>

                <p class="card-category text-secondary mb-1">Catégories :
                    <span class="text-danger">{{ $doc->category ?? "Aucune catégorie" }}</span>
                </p>

                <p class="card-date text-muted mb-4">
                    <strong>Publié le {{ formatDatePhp($doc->date_publication ?? null) }}</strong>
                </p>

                <a href="{{ $downloadUrl }}" target="_blank"
                   class="btn btn-danger mt-auto d-flex align-items-center justify-content-center gap-1"
                   style="padding: 4px 8px; font-size: 0.875rem;">
                    <i class="fas fa-download"></i> Télécharger
                </a>
            </div>
        </div>
    </div>
@empty
    <div class="col-12 text-center text-muted">
        Aucun document trouvé pour le moment.
    </div>
@endforelse