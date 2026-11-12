<?php

namespace App\Shared\Models;

use App\Modules\Intelligence\Models\SmeProfile;
use Illuminate\Database\Eloquent\Model;

class BusinessData extends Model
{
    protected $table = 'business_data';

    protected $fillable = [
        'id_profile',
        'period_date',
        'omzet',
        'pendapatan',
        'pengeluaran',
        'laba',
        'jumlah_transaksi',
        'jumlah_pelanggan',
        'produk_terjual',
        'produk_tidak_terjual',
        'stok',
        'biaya_operasional',
        'biaya_pemasaran',
        'data_pendukung',
    ];

    protected function casts(): array
    {
        return [
            'period_date' => 'date',
            'omzet' => 'decimal:2',
            'pendapatan' => 'decimal:2',
            'pengeluaran' => 'decimal:2',
            'laba' => 'decimal:2',
            'jumlah_transaksi' => 'integer',
            'jumlah_pelanggan' => 'integer',
            'produk_terjual' => 'integer',
            'produk_tidak_terjual' => 'integer',
            'stok' => 'integer',
            'biaya_operasional' => 'decimal:2',
            'biaya_pemasaran' => 'decimal:2',
        ];
    }

    public function profile()
    {
        return $this->belongsTo(SmeProfile::class, 'id_profile', 'id_profile');
    }
}
