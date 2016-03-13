<?php namespace prozorro\Claims;

use System\Classes\PluginBase;
use Backend;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
    }

    public function registerSettings()
    {
        return [
            'location' => [
                'label'       => 'API Settings',
                'description' => '',
                'category'    => 'Claims',
                'icon'        => 'icon-briefcase',
                'class'       => 'prozorro\Claims\Models\Settings',
                'order'       => 500,
                'keywords'    => 'prozorro'
            ]
        ];
    }
}
