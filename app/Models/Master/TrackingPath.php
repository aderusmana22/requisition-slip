<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class TrackingPath extends Model
{
    use LogsActivity; // Aktifkan fitur Log Aktivitas

    protected $table = 'tracking_paths';

    protected $fillable = [
        'category',
        'sub_category',
        'sequence_approvers',
        'print_batch',
    ];

    protected $casts = [
        'sequence_approvers' => 'array', // Wajib array agar JSON tersimpan benar
        'print_batch' => 'boolean',      // Cast ke boolean (true/false)
    ];

    // Konfigurasi Log Otomatis (Spatie Activity Log)
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['category', 'sub_category', 'sequence_approvers', 'print_batch']) // Catat perubahan di kolom ini
            ->logOnlyDirty() // Hanya catat jika ada data yang berubah
            ->useLogName('tracking-path') // Nama log di database
            ->setDescriptionForEvent(fn(string $eventName) => "Tracking Path has been {$eventName}");
    }
}