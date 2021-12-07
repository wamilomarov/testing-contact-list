<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $email
 * @property string $first_name
 * @property string $last_name
 * @property string $lead_source
 * @property string $phone
 * @property string $salesforce_id
 * @property bool $is_synced
 */
class Contact extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'lead_source',
        'salesforce_id',
        'is_synced'
    ];

    protected $casts = [
        'is_synced' => 'bool'
    ];

    /**
     * @return void
     */
    public function isNotSyncAnymore(): void
    {
        $this->update([
            'is_synced' => false
        ]);
    }

    public function isSynced()
    {
        $this->update([
            'is_synced' => true
        ]);
    }
}
