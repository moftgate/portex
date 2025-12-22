<?php

namespace App\Enums;

use App\Concerns\EnumToArray;

enum Permission: string
{
    use EnumToArray;

    case SHOW_DASHBOARD = 'show_dashboard';

    case SHOW_MAIN_REPORT = 'show_main_report';
    case CREATE_MAIN_REPORT = 'create_main_report';
    case UPDATE_MAIN_REPORT = 'update_main_report';
    case DELETE_MAIN_REPORT = 'delete_main_report';

    case SHOW_APP_ACTIVITY_MAP = 'show_app_activity_map';

    case SHOW_PAYWALL = 'show_paywall';
    case CREATE_PAYWALL = 'create_paywall';
    case UPDATE_PAYWALL = 'update_paywall';
    case DELETE_PAYWALL = 'delete_paywall';

    case SHOW_PAYWALL_REPORT = 'show_paywall_report';

    case SHOW_PREMIUM_PACKAGE = 'show_premium_package';
    case CREATE_PREMIUM_PACKAGE = 'create_premium_package';
    case UPDATE_PREMIUM_PACKAGE = 'update_premium_package';
    case DELETE_PREMIUM_PACKAGE = 'delete_premium_package';

    case SHOW_APP = 'show_app';
    case CREATE_APP = 'create_app';
    case UPDATE_APP = 'update_app';
    case DELETE_APP = 'delete_app';

    case SHOW_INIT_METRIC = 'show_init_metric';
    case CREATE_INIT_METRIC = 'create_init_metric';
    case UPDATE_INIT_METRIC = 'update_init_metric';
    case DELETE_INIT_METRIC = 'delete_init_metric';

    case SHOW_CAMPAIGN_ANALYTIC = 'show_campaign_analytic';

    case SHOW_APP_TYPE = 'show_app_type';
    case CREATE_APP_TYPE = 'create_app_type';
    case UPDATE_APP_TYPE = 'update_app_type';
    case DELETE_APP_TYPE = 'delete_app_type';

    case SHOW_USER = 'show_user';
    case CREATE_USER = 'create_user';
    case UPDATE_USER = 'update_user';
    case DELETE_USER = 'delete_user';

    // Permission Management
    case SHOW_PERMISSION = 'show_permission';
    case CREATE_PERMISSION = 'create_permission';
    case UPDATE_PERMISSION = 'update_permission';
    case DELETE_PERMISSION = 'delete_permission';

    case SHOW_DEFAULT_TRIGGER = 'show_default_trigger';
    case CREATE_DEFAULT_TRIGGER = 'create_default_trigger';
    case UPDATE_DEFAULT_TRIGGER = 'update_default_trigger';
    case DELETE_DEFAULT_TRIGGER = 'delete_default_trigger';

    case SHOW_DEFAULT_ACTION = 'show_default_action';
    case CREATE_DEFAULT_ACTION = 'create_default_action';
    case UPDATE_DEFAULT_ACTION = 'update_default_action';
    case DELETE_DEFAULT_ACTION = 'delete_default_action';

    case SHOW_WIZARD = 'show_wizard';
    case UPDATE_WIZARD = 'update_wizard';

    public function text(): string
    {
        return match ($this) {
            // Dashboard
            self::SHOW_DASHBOARD => 'View Dashboard',

            // User Management
            self::SHOW_USER => 'View Users',
            self::CREATE_USER => 'Create User',
            self::UPDATE_USER => 'Edit Users',
            self::DELETE_USER => 'Delete Users',

            // Permission Management
            self::SHOW_PERMISSION => 'View Permissions',
            self::CREATE_PERMISSION => 'Create Permission',
            self::UPDATE_PERMISSION => 'Edit Permissions',
            self::DELETE_PERMISSION => 'Delete Permissions',

            // App Management
            self::SHOW_APP => 'View Apps',
            self::CREATE_APP => 'Create App',
            self::UPDATE_APP => 'Edit Apps',
            self::DELETE_APP => 'Delete Apps',

            // Analytics
            self::SHOW_MAIN_REPORT => 'View Main Reports',

            // Monetization
            self::SHOW_PAYWALL => 'View Paywalls',
            self::CREATE_PAYWALL => 'Create Paywall',
            self::UPDATE_PAYWALL => 'Edit Paywalls',
            self::DELETE_PAYWALL => 'Delete Paywalls',

            self::SHOW_PAYWALL_REPORT => 'View Paywall Report',

            // Features
            self::SHOW_INIT_METRIC => 'View Init Metrics',

            // Analytics
            self::SHOW_CAMPAIGN_ANALYTIC => 'View Analytics',

            // App Types
            self::SHOW_APP_TYPE => 'View App Types',
            self::CREATE_APP_TYPE => 'Create App Types',

            // Premium Packages
            self::SHOW_PREMIUM_PACKAGE => 'View Premium Packages',
            self::CREATE_PREMIUM_PACKAGE => 'Create Premium Package',
            self::UPDATE_PREMIUM_PACKAGE => 'Edit Premium Packages',
            self::DELETE_PREMIUM_PACKAGE => 'Delete Premium Packages',

            // App Wizard
            self::SHOW_APP_ACTIVITY_MAP => 'View App Activity Map',

            self::SHOW_WIZARD => 'View Wizard',
            self::UPDATE_WIZARD => 'Update Wizard',

            // Default Triggers
            self::SHOW_DEFAULT_TRIGGER => 'View Default Triggers',
            self::CREATE_DEFAULT_TRIGGER => 'Create Default Trigger',
            self::UPDATE_DEFAULT_TRIGGER => 'Edit Default Triggers',
            self::DELETE_DEFAULT_TRIGGER => 'Delete Default Triggers',

            // Default Actions
            self::SHOW_DEFAULT_ACTION => 'View Default Actions',
            self::CREATE_DEFAULT_ACTION => 'Create Default Action',
            self::UPDATE_DEFAULT_ACTION => 'Edit Default Actions',
            self::DELETE_DEFAULT_ACTION => 'Delete Default Actions',

            default => '',
        };
    }
}
