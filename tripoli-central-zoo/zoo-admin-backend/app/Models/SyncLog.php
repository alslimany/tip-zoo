<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SyncLog extends Model
{
    protected $fillable = [
        'table_name',
        'last_sync',
        'record_count',
        'sync_status',
        'error_message',
        'metadata',
    ];

    protected $casts = [
        'last_sync' => 'datetime',
        'record_count' => 'integer',
        'metadata' => 'array',
    ];

    protected $attributes = [
        'sync_status' => 'success',
        'record_count' => 0,
    ];

    /**
     * Scope a query to filter by table name.
     */
    public function scopeForTable(Builder $query, string $tableName): Builder
    {
        return $query->where('table_name', $tableName);
    }

    /**
     * Scope a query to only include successful syncs.
     */
    public function scopeSuccessful(Builder $query): Builder
    {
        return $query->where('sync_status', 'success');
    }

    /**
     * Scope a query to only include failed syncs.
     */
    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('sync_status', 'failed');
    }

    /**
     * Scope a query to get the latest sync for each table.
     */
    public function scopeLatestPerTable(Builder $query): Builder
    {
        return $query->whereIn('id', function ($subQuery) {
            $subQuery->selectRaw('MAX(id)')
                ->from('sync_logs')
                ->groupBy('table_name');
        });
    }
}
