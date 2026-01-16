<?php

namespace App\Jobs;

use App\Models\Profils;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Intervention\Image\Laravel\Facades\Image; 
use Illuminate\Support\Facades\File;

class ProcessProfileImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $profilId;
    protected $imagePath;

    public function __construct(int $profilId, string $imagePath)
    {
        $this->profilId = $profilId;
        $this->imagePath = $imagePath;
    }

    public function handle(): void
    {
        $profil = Profils::find($this->profilId);
        if (!$profil) return;

        $fullPath = storage_path('app/public/' . $this->imagePath);
        
        // Verificar si el archivo original existe
        if (!File::exists($fullPath)) return;

        // Crear el directorio de miniaturas
        $thumbnailDirectory = storage_path('app/public/profiles/thumbnails');
        if (!File::isDirectory($thumbnailDirectory)) {
            File::makeDirectory($thumbnailDirectory, 0755, true);
        }

        // Procesar la imagen
        $image = Image::read($fullPath);
        $image->scale(width: 150);

        $thumbnailName = basename($this->imagePath);
        $thumbnailPath = 'profiles/thumbnails/' . $thumbnailName;
        
        // Guardar
        $image->save(storage_path('app/public/' . $thumbnailPath));

        // Actualizar base de datos
        $profil->update(['avatar_thumbnail' => $thumbnailPath]);
    }
}