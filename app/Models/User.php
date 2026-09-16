<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'username', 'name', 'email', 'password',
        'peran', 'unit_pengolah', 'aktif',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'aktif'    => 'boolean',
        ];
    }

    public function unit()
    {
        return $this->belongsTo(UnitPengolah::class, 'unit_pengolah', 'kode');
    }

    public function superadmin(): bool
    {
        return $this->peran === 'superadmin';
    }

    public function kearsipan(): bool
    {
        return $this->peran === 'kearsipan';
    }

    public function operator(): bool
    {
        return $this->peran === 'operator';
    }

    // Kearsipan dan superadmin melihat seluruh unit
    public function lihatSemuaUnit(): bool
    {
        return in_array($this->peran, ['kearsipan', 'superadmin'], true);
    }

    // Surat masuk diagendakan terpusat di Sekretariat
    public function bolehSuratMasuk(): bool
    {
        return $this->lihatSemuaUnit() || $this->unit_pengolah === '121.1';
    }
}