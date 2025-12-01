<?php

use App\Services\AttachmentService;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Route;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::delete('media/{id}', function ($id) {
    $media = Media::find($id);

    if (! $media) {
        Notification::make()
            ->title('File Not Found')
            ->danger()
            ->send();

        return redirect()->back();
    }

    AttachmentService::delete($media);

    Notification::make()
        ->title('File Deleted Successfully')
        ->success()
        ->send();

    return redirect()->back();
})->name('media.delete');
