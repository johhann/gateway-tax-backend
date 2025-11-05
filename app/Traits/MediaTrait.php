<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait MediaTrait
{
    /**
     * Return first media attachment as array
     */
    public function getAttachmentAttribute(): ?array
    {
        $firstMedia = $this->media->first();

        if (! $firstMedia) {
            return [];
        }

        $this->makeHidden(['media']);

        return [
            'id' => $firstMedia->uuid,
            'collection' => $firstMedia->collection_name,
            'file_name' => $firstMedia->file_name,
            'link' => $this->getAbsoluteUrl($firstMedia->original_url),
            'created_at' => $firstMedia->created_at->timestamp,
        ];
    }

    /**
     * Return all media attachments as collection
     */
    public function getAttachmentsAttribute(): Collection
    {
        $attachments = $this->media->map(function ($item) {
            return [
                'id' => $item->uuid,
                'collection' => $item->collection_name,
                'file_name' => $item->file_name,
                'order' => $item->order_column,
                'link' => $this->getAbsoluteUrl($item->original_url),
                'created_at' => $item->created_at->timestamp,
            ];
        });

        $this->makeHidden(['media']);

        return $attachments->sortBy('order')->values();
    }

    /**
     * Filter attachments by collection names
     */
    public function filterAttachments(Collection $attachments, array $filters): Collection
    {
        if (empty($filters)) {
            return $attachments;
        }

        return $attachments->filter(fn ($attachment) => in_array($attachment['collection'], $filters))->values();
    }

    /**
     * Check if a model has attachments in a collection
     */
    public function validateCollectionName(Model $model, string $collectionName): bool
    {
        return $model->attachments?->where('collection', $collectionName)->isNotEmpty();
    }

    /**
     * Generate absolute URL for media
     */
    private function getAbsoluteUrl(string $url, string $disk = 'public'): string
    {
        $baseUrl = rtrim(config('app.url'), '/');

        // Only rewrite URLs if disk is s3
        if ($disk === 's3') {
            // If already absolute (MinIO or AWS), replace host with app.url
            if (preg_match('/^https?:\/\//i', $url)) {
                $parsed = parse_url($url);
                $path = $parsed['path'] ?? '';
                $query = ! empty($parsed['query']) ? '?'.$parsed['query'] : '';

                return $baseUrl.$path.$query;
            }

            // If relative path
            return $baseUrl.'/'.ltrim($url, '/');
        }

        $baseUrl = rtrim(config('app.url'), '/');
        $url = ltrim($url, '/');

        if (str_starts_with($url, $baseUrl)) {
            return $url;
        }

        return $baseUrl.'/'.$url;
    }
}
