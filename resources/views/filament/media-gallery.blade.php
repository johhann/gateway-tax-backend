@php
    $mediaItems = $record->getMedia('tax_requests');
@endphp

@if ($mediaItems->isNotEmpty())

    <style>
        .file-list { 
            margin: 0; 
            padding: 0; 
            list-style: none; 
        }
        .file-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 4px 0;
        }
        .file-item a.file-link {
            color: #e5e7eb;
            text-decoration: none;
            font-size: 14px;
        }
        .file-item a.file-link:hover {
            color: #ffffff;
        }
        .file-icon {
            width: 18px;
            height: 18px;
            display: inline-block;
        }
        .file-icon img {
            width: 18px;
            height: 18px;
            object-fit: cover;
            border-radius: 2px;
        }
        .file-icon-ext {
            width: 18px;
            height: 18px;
            background: #d9534f;
            border-radius: 3px;
            color: white;
            font-size: 10px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            text-transform: uppercase;
        }

        /* DELETE BUTTON */
        .delete-btn {
            margin-left: 10px;
            font-size: 12px;
            background: transparent;
            border: 1px solid #f87171;
            color: #f87171;
            padding: 2px 6px;
            border-radius: 4px;
            cursor: pointer;
        }
        .delete-btn:hover {
            background: #f87171;
            color: white;
        }
    </style>

    <ul class="file-list">
        @foreach ($mediaItems as $media)
            <li class="file-item">

                {{-- Icon --}}
                <div class="file-icon">
                    @if (str_starts_with($media->mime_type, 'image/'))
                        <img src="{{ $media->getUrl() }}" alt="">
                    @else
                        <div class="file-icon-ext">
                            {{ $media->extension }}
                        </div>
                    @endif
                </div>

                {{-- Filename --}}
                <a href="{{ $media->getUrl() }}" target="_blank" class="file-link">
                    {{ $media->file_name }}
                </a>

                {{-- Delete button --}}
                <form 
                    action="{{ route('media.delete', $media->id) }}" 
                    method="POST"
                    onsubmit="return confirm('Delete this file?')"
                >
                    @csrf
                    @method('DELETE')
                    <button class="delete-btn">Delete</button>
                </form>

            </li>
        @endforeach
    </ul>

@else
    <p style="color:#aaa; font-size:14px;">No attached files.</p>
@endif
