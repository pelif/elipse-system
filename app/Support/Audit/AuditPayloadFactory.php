<?php

namespace App\Support\Audit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuditPayloadFactory
{
    private const DEFAULT_SENSITIVE_KEYS = [
        'password',
        'password_confirmation',
        'remember_token',
        'token',
        'access_token',
        'refresh_token',
        'api_token',
        'secret',
        'client_secret',
    ];

    public static function fromEloquentModelEvent(string $action, Model $model): ?array
    {
        if (self::shouldIgnoreModel($model)) {
            return null;
        }

        $action = strtolower($action);

        $request = app()->bound('request') ? request() : null;
        $maxUa = max(0, (int) (config('audit.max_request_user_agent_length', 500) ?? 500));

        $actor = null;
        try {
            $user = Auth::user();
            if ($user) {
                $actor = [
                    'id' => $user->getAuthIdentifier(),
                    'email' => $user->email ?? null,
                    'name' => $user->name ?? null,
                ];
            }
        } catch (\Throwable) {
            $actor = null;
        }

        $changes = match ($action) {
            'updated' => self::buildUpdateChanges($model),
            'created', 'deleted' => [
                'attributes' => self::sanitizeAttributes($model->getAttributes()),
            ],
            default => [],
        };

        $userAgent = $request?->userAgent();
        if (is_string($userAgent) && $maxUa > 0 && Str::length($userAgent) > $maxUa) {
            $userAgent = Str::limit($userAgent, $maxUa, '...[TRUNCATED]');
        }

        return [
            'occurred_at' => Carbon::now()->toIso8601String(),
            'action' => $action,
            'actor' => $actor,
            'request' => [
                'method' => $request?->method(),
                'path' => $request ? ltrim($request->path(), '/') : null,
                'ip' => $request?->ip(),
                'user_agent' => $userAgent,
            ],
            'resource' => [
                'model' => $model::class,
                'table' => $model->getTable(),
                'key' => $model->getKey(),
            ],
            'changes' => $changes,
        ];
    }

    /**
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    public static function fromAuthEvent(string $action, mixed $user, array $context = []): array
    {
        $action = strtolower($action);

        $request = app()->bound('request') ? request() : null;
        $maxUa = max(0, (int) (config('audit.max_request_user_agent_length', 500) ?? 500));

        $actor = null;
        if (is_object($user) && method_exists($user, 'getAuthIdentifier')) {
            $actor = [
                'id' => $user->getAuthIdentifier(),
                'email' => $user->email ?? null,
                'name' => $user->name ?? null,
            ];
        }

        $resource = match (true) {
            $user instanceof Model => [
                'model' => $user::class,
                'table' => $user->getTable(),
                'key' => $user->getKey(),
            ],
            is_object($user) && method_exists($user, 'getAuthIdentifier') => [
                'model' => $user::class,
                'table' => null,
                'key' => $user->getAuthIdentifier(),
            ],
            default => null
        };

        $userAgent = $request?->userAgent();
        if (is_string($userAgent) && $maxUa > 0 && Str::length($userAgent) > $maxUa) {
            $userAgent = Str::limit($userAgent, $maxUa, '...[TRUNCATED]');
        }

        $guard = null;
        if (array_key_exists('guard', $context) && is_string($context['guard'])) {
            $guard = $context['guard'];
        }

        $remember = null;
        if (array_key_exists('remember', $context)) {
            $remember = (bool) $context['remember'];
        }

        $changes = [
            'auth' => array_filter([
                'guard' => $guard,
                'remember' => $remember,
            ], static fn ($v) => $v !== null),
        ];

        return [
            'occurred_at' => Carbon::now()->toIso8601String(),
            'action' => $action,
            'actor' => $actor,
            'request' => [
                'method' => $request?->method(),
                'path' => $request ? ltrim($request->path(), '/') : null,
                'ip' => $request?->ip(),
                'user_agent' => $userAgent,
            ],
            'resource' => $resource,
            'changes' => $changes,
        ];
    }

    private static function shouldIgnoreModel(Model $model): bool
    {
        $ignoredModels = array_filter((array) (config('audit.ignored_models', []) ?? []));
        if ($ignoredModels !== [] && in_array($model::class, $ignoredModels, true)) {
            return true;
        }

        $ignoredTables = array_filter((array) (config('audit.ignored_tables', []) ?? []));
        if ($ignoredTables !== [] && in_array($model->getTable(), $ignoredTables, true)) {
            return true;
        }

        return false;
    }

    private static function buildUpdateChanges(Model $model): array
    {
        $changed = $model->getChanges();
        $original = [];

        foreach (array_keys($changed) as $key) {
            $original[$key] = $model->getOriginal($key);
        }

        return [
            'changed' => self::sanitizeAttributes($changed),
            'original' => self::sanitizeAttributes($original),
        ];
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    private static function sanitizeAttributes(array $attributes): array
    {
        $whitelist = array_values(array_filter((array) (config('audit.attribute_whitelist', []) ?? [])));
        if ($whitelist !== []) {
            $attributes = array_intersect_key($attributes, array_flip($whitelist));
        }

        $maxKeys = max(1, (int) (config('audit.max_attribute_keys', 150) ?? 150));
        $omitted = 0;

        if (count($attributes) > $maxKeys) {
            ksort($attributes);
            $omitted = count($attributes) - $maxKeys;
            $attributes = array_slice($attributes, 0, $maxKeys, true);
        }

        $sanitized = [];
        foreach ($attributes as $key => $value) {
            if (!is_string($key)) {
                continue;
            }
            if (self::isSensitiveKey($key)) {
                $sanitized[$key] = '[REDACTED]';
                continue;
            }
            $sanitized[$key] = self::sanitizeValue($value, 0);
        }

        if ($omitted > 0) {
            $sanitized['_audit_omitted_key_count'] = $omitted;
        }

        return $sanitized;
    }

    private static function isSensitiveKey(string $key): bool
    {
        foreach (self::mergedSensitiveKeys() as $blocked) {
            if (strcasecmp($key, $blocked) === 0) {
                return true;
            }
        }

        foreach (self::sensitiveKeyPatterns() as $pattern) {
            if (@fnmatch($pattern, $key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return list<string>
     */
    private static function mergedSensitiveKeys(): array
    {
        $extra = (array) (config('audit.sensitive_keys', []) ?? []);

        return array_values(array_unique(array_merge(self::DEFAULT_SENSITIVE_KEYS, $extra)));
    }

    /**
     * @return list<string>
     */
    private static function sensitiveKeyPatterns(): array
    {
        return array_values(array_filter((array) (config('audit.sensitive_key_patterns', []) ?? [])));
    }

    private static function sanitizeValue(mixed $value, int $depth): mixed
    {
        $maxStringLength = max(0, (int) (config('audit.max_string_length', 2000) ?? 2000));
        $maxDepth = max(0, (int) (config('audit.max_array_depth', 5) ?? 5));
        $maxKeysPerLevel = max(1, (int) (config('audit.max_array_keys_per_level', 50) ?? 50));

        if ($depth > $maxDepth) {
            return '[DEPTH_LIMIT]';
        }

        if (is_string($value) && $maxStringLength > 0 && Str::length($value) > $maxStringLength) {
            return Str::limit($value, $maxStringLength, '...[TRUNCATED]');
        }

        if (is_array($value)) {
            if (count($value) > $maxKeysPerLevel) {
                ksort($value);
            }

            $out = [];
            $i = 0;
            $omitted = 0;

            foreach ($value as $k => $v) {
                if ($i >= $maxKeysPerLevel) {
                    $omitted = count($value) - $maxKeysPerLevel;
                    break;
                }

                if (is_string($k) && self::isSensitiveKey($k)) {
                    $out[$k] = '[REDACTED]';
                } else {
                    $out[$k] = self::sanitizeValue($v, $depth + 1);
                }
                $i++;
            }

            if ($omitted > 0) {
                $out['_audit_omitted_key_count'] = $omitted;
            }

            return $out;
        }

        if (is_object($value)) {
            return '[OBJECT:' . $value::class . ']';
        }

        if (is_resource($value)) {
            return '[RESOURCE]';
        }

        return $value;
    }
}
