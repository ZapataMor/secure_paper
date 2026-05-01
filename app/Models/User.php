<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Throwable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function userSubscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function activePaidSubscription(): HasOne
    {
        return $this->hasOne(UserSubscription::class)
            ->activePaid()
            ->latestOfMany('end_date');
    }

    public function hasActiveMembership(): bool
    {
        if ($this->relationLoaded('activePaidSubscription')) {
            return $this->activePaidSubscription !== null;
        }

        return $this->activePaidSubscription()->exists();
    }

    public function activeMembershipPlanName(): ?string
    {
        $subscription = $this->relationLoaded('activePaidSubscription')
            ? $this->activePaidSubscription
            : $this->activePaidSubscription()->with('paymentPlan:id,name')->first();

        if (! $subscription) {
            return null;
        }

        $subscription->loadMissing('paymentPlan:id,name');

        return $subscription->paymentPlan?->name;
    }

    public function additionalInformationEntries(): Collection
    {
        $rawValue = trim((string) $this->document_additional_information);

        if ($rawValue === '') {
            return collect();
        }

        $decodedValue = json_decode($rawValue, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decodedValue)) {
            $decodedEntries = array_is_list($decodedValue) ? $decodedValue : [$decodedValue];

            $entries = collect($decodedEntries)
                ->map(function (mixed $entry): ?array {
                    if (! is_array($entry)) {
                        return null;
                    }

                    $content = trim((string) ($entry['content'] ?? ''));

                    if ($content === '') {
                        return null;
                    }

                    return [
                        'content' => $content,
                        'created_at' => $this->parseAdditionalInformationDate($entry['created_at'] ?? null),
                    ];
                })
                ->filter()
                ->values();

            if ($entries->isNotEmpty()) {
                return $entries;
            }
        }

        return collect([
            [
                'content' => $rawValue,
                'created_at' => null,
            ],
        ]);
    }

    public function appendAdditionalInformation(string $content, ?Carbon $createdAt = null): void
    {
        $content = trim($content);

        if ($content === '') {
            return;
        }

        $entries = $this->additionalInformationEntries();

        $entries->push([
            'content' => $content,
            'created_at' => $createdAt ?? now(),
        ]);

        $this->document_additional_information = $this->serializeAdditionalInformationEntries($entries);
    }

    public function latestAdditionalInformationContent(): ?string
    {
        $lastEntry = $this->additionalInformationEntries()->last();

        if (! is_array($lastEntry)) {
            return null;
        }

        return $lastEntry['content'] ?? null;
    }

    public function isAdmin(): bool
    {
        return $this->role?->name === 'admin';
    }

    public function fullName(): string
    {
        return trim(implode(' ', array_filter([$this->name, $this->last_name])));
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    private function serializeAdditionalInformationEntries(Collection $entries): ?string
    {
        $payload = $entries
            ->map(function (mixed $entry): ?array {
                if (! is_array($entry)) {
                    return null;
                }

                $content = trim((string) ($entry['content'] ?? ''));

                if ($content === '') {
                    return null;
                }

                $createdAt = $entry['created_at'] ?? null;
                $createdAtValue = null;

                if ($createdAt instanceof DateTimeInterface) {
                    $createdAtValue = Carbon::instance($createdAt)->toIso8601String();
                } elseif (is_string($createdAt)) {
                    $createdAtValue = $this->parseAdditionalInformationDate($createdAt)?->toIso8601String();
                }

                return [
                    'content' => $content,
                    'created_at' => $createdAtValue,
                ];
            })
            ->filter()
            ->values()
            ->all();

        if ($payload === []) {
            return null;
        }

        $jsonPayload = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return $jsonPayload !== false ? $jsonPayload : null;
    }

    private function parseAdditionalInformationDate(mixed $value): ?Carbon
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (Throwable) {
            return null;
        }
    }
}
