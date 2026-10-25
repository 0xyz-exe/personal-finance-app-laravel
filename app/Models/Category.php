<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'color',
        'icon',
        'budget_limit',
    ];

    protected function casts(): array
    {
        return [
            'budget_limit' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function getMonthlySpentAttribute(): float
    {
        return (float) $this->transactions()
            ->where('type', 'expense')
            ->whereYear('transaction_date', now()->year)
            ->whereMonth('transaction_date', now()->month)
            ->sum('amount');
    }

    public function getBudgetUsagePercentAttribute(): ?float
    {
        if (!$this->budget_limit || $this->budget_limit <= 0) {
            return null;
        }

        return min(100, round(($this->monthly_spent / $this->budget_limit) * 100, 1));
    }
}
