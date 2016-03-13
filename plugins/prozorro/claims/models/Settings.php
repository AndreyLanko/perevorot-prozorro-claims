<?php namespace prozorro\Claims\Models;

use Model;

/**
 * Model
 */
class Settings extends Model
{
    
    public $implement = ['System.Behaviors.SettingsModel'];

    // A unique code
    public $settingsCode = 'prozorro_settings';

    // Reference to field configuration
    public $settingsFields = 'fields.yaml';

}