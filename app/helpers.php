<?php


use App\Enums\Permission;

function is_admin(): bool
{
    return auth()->user()->isAdmin();
}

function is_user(): bool
{
    return auth()->user()->isUser();
}

if (!function_exists('get_real_ip')) {
    function get_real_ip(): mixed
    {
        $server = request()->server;

        if (!empty($server->get('HTTP_CF_CONNECTING_IP'))) {
            $ip = $server->get('HTTP_CF_CONNECTING_IP');
        } elseif (!empty($server->get('HTTP_CLIENT_IP'))) {
            $ip = $server->get('HTTP_CLIENT_IP');
        } elseif (!empty($server->get('HTTP_X_FORWARDED_FOR'))) {
            $ip = $server->get('HTTP_X_FORWARDED_FOR');

            if (str_contains($ip, ',')) {
                $ipArray = explode(',', $ip);
                $ip = reset($ipArray);
            }
        } else {
            $ip = $server->get('REMOTE_ADDR');
        }

        return $ip;
    }
}

if (!function_exists('in_array_recursive')) {
    function in_array_recursive($needle, $haystack, $strict = false): bool
    {
        foreach ($haystack as $item) {
            if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_recursive($needle, $item, $strict))) {
                return true;
            }
        }

        return false;
    }
}

function check_permission(string|Permission $permission, string $guard = 'web'): bool
{
    if ($permission instanceof Permission) {
        $permission = $permission->value;
    }

    $user = auth($guard)->user();

    if (!$user) {
        return false;
    }

    if ($user->role->isAdmin()) {
        return true;
    }

    $permissions = $user->permissions;

    if (empty($permissions)) {
        return false;
    }

    return in_array($permission, $permissions);
}
