@php
    $files = $getState() ?? [];
@endphp

<style>
    .file-table-wrapper {
        width: 100%;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        background: #ffffff;
    }

    .file-table {
        width: 100%;
        border-collapse: collapse;
    }

    .file-table thead {
        background-color: #f9fafb;
    }

    .file-table th {
        padding: 12px 16px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6b7280;
        text-align: left;
        border-bottom: 1px solid #e5e7eb;
    }

    .file-table th.center {
        text-align: center;
    }

    .file-table th.right {
        text-align: right;
    }

    .file-table td {
        padding: 16px;
        font-size: 14px;
        color: #111827;
        border-bottom: 1px solid #e5e7eb;
        vertical-align: middle;
    }

    .file-table tbody tr:hover {
        background-color: #f9fafb;
    }

    .file-table td.center {
        text-align: center;
        color: #4b5563;
    }

    .file-table td.type {
        text-transform: uppercase;
        font-size: 13px;
        color: #4b5563;
    }

    .file-actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
    }

    .btn-view {
        padding: 6px 12px;
        font-size: 13px;
        font-weight: 500;
        background: #ffffff;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        cursor: pointer;
        color: #374151;
    }

    .btn-view:hover {
        background-color: #f3f4f6;
    }

    .btn-download {
        padding: 6px 12px;
        font-size: 13px;
        font-weight: 600;
        background-color: #80db82;
        color: #ffffff;
        border: none;
        border-radius: 6px;
        cursor: pointer;
    }

    .btn-download:hover {
        background-color: #1d4ed8;
    }

    .no-files {
        text-align: center;
        padding: 40px 16px;
        font-size: 14px;
        color: #6b7280;
    }
</style>

<div class="file-table-wrapper">

    <table class="file-table">
        <thead>
            <tr>
                <th>File Name</th>
                <th class="center">Size</th>
                <th class="center">Type</th>
                <th class="right">Actions</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($files as $file)
                @if (!$file['media'])
                    @continue
                @endif
                <tr>
                    <td>
                        {{ $file['name'] }}
                    </td>

                    <td class="center">
                        {{ number_format($file['media']['size'] / 1000, 2) . ' KB' ?? '—' }}
                    </td>

                    <td class="center type">
                        {{ $file['media']['mime_type'] ?? '—' }}
                    </td>

                    <td>
                        <div class="file-actions">
                            <button type="button" class="btn-view" onclick="viewFile('{{ $file['url'] ?? '' }}')">
                                View
                            </button>

                            <button type="button" class="btn-download"
                                onclick="downloadFile(
                                '{{ $file['url'] ?? '' }}', 
                                '{{ $file['media']['id'] . '_' . $file['media']['collection_name'] }}', 
                                '{{ $file['media']['mime_type'] }}', 
                                )">
                                Download
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="no-files">
                        No files uploaded.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>

<script>
    function viewFile(url) {
        if (!url) return;
        window.open(url, '_blank', 'noopener,noreferrer');
    }

    function downloadFile(url, name, mimeType) {
        if (!url) return;

        const extension = mimeType.split('/').pop(); // gets the part after '/'
        const filename = `${name}.${extension}`;

        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', filename);
        link.setAttribute('target', '_blank');

        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>
