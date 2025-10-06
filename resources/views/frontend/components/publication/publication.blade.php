@if($publications->isEmpty())
    <p>Aucune publication trouvée.</p>
@endif

@foreach($publications as $publication)
    <div class="publication-card">
        <div class="publication-overlay">
            <div class="publication-category">
                @if($publication->categories->isNotEmpty())
                    @foreach($publication->categories as $category)
                        {{ $category->name }}
                    @endforeach
                @else
                    <span>Aucune catégorie</span>
                @endif
            </div>
        </div>
        <div class="publication-info">
            <span class="publication-date">Publié le {{ $publication->date_publication }}</span>
            <span class="publication-title">{{ $publication->title }}</span>

            <a href="{{ route('documents.download', $publication->id) }}" class="publication-button" target="_blank" rel="noopener">
                @if($publication->file_type == 'pdf')
                    <i class="fas fa-file-pdf"></i>
                @elseif(in_array($publication->file_type, ['doc', 'docx']))
                    <i class="fas fa-file-word"></i>
                @elseif(in_array($publication->file_type, ['xls', 'xlsx']))
                    <i class="fas fa-file-excel"></i>
                @elseif(in_array($publication->file_type, ['ppt', 'pptx']))
                    <i class="fas fa-file-powerpoint"></i>
                @else
                    <i class="fas fa-file"></i>
                @endif
                <span>{{ round($publication->file_size / 1024, 2) }} Mo</span>
                <i class="fas fa-download"></i>
            </a>

            <span class="download-count">{{ $publication->download_count }} téléchargements</span>
        </div>
    </div>
@endforeach
