<?php

namespace App\Traits;

use App\Models\JejakAudit;

trait DicatatJejak
{
    public static function bootDicatatJejak(): void
    {
        static::created(function ($model) {
            $model->catatJejak('tambah', null, $model->getAttributes());
        });

        static::updated(function ($model) {
            $lama = [];
            $baru = [];

            // Hanya kolom yang benar-benar berubah yang dicatat
            foreach ($model->getChanges() as $kolom => $nilaiBaru) {
                if (in_array($kolom, JejakAudit::ABAIKAN, true)) continue;

                $lama[$kolom] = $model->getOriginal($kolom);
                $baru[$kolom] = $nilaiBaru;
            }

            if (empty($baru)) return;

            $model->catatJejak('ubah', $lama, $baru);
        });

        static::deleted(function ($model) {
            $model->catatJejak('hapus', $model->getOriginal(), null);
        });
    }

    public function catatJejak(string $aksi, ?array $sebelum, ?array $sesudah): void
    {
        $user = auth()->user();

        $bersih = function (?array $data) {
            if (! $data) return null;

            return collect($data)
                ->except(JejakAudit::ABAIKAN)
                ->all();
        };

        JejakAudit::create([
            'model'         => class_basename($this),
            'model_id'      => $this->getKey(),
            'aksi'          => $aksi,
            'sebelum'       => $bersih($sebelum),
            'sesudah'       => $bersih($sesudah),
            'user_id'       => $user?->id,
            'nama_user'     => $user?->name,
            'unit_pengolah' => $user?->unit_pengolah,
            'ip'            => request()->ip(),
            'created_at'    => now(),
        ]);
    }

    public function jejak()
    {
        return JejakAudit::where('model', class_basename($this))
            ->where('model_id', $this->getKey())
            ->orderByDesc('created_at');
    }
}