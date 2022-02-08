<?php
$menus = [
    'admin'=>[
        'url'   => 'admin',
        'title' => __("Dashboard"),
        'icon'  => 'icon ion-ios-desktop',
        "position"=>0
    ],



    'beyond'=>[
        "position"=>55,
        'url'      => 'admin/',
        'title'    => __("Productos"),
        'icon'     => 'icon ion-ios-hammer',
		'permission' => '',
        'children' => [
            'products'=>[
                'url'        => 'admin/',
                'title'      => __('Products'),
                'icon'       => 'icon ion-ios-globe',
                
            ],
            'products2'=>[
                'url'        => 'admin/',
                'title'      => __('Medicin'),
                'icon'       => 'icon ion-ios-globe',
                
            ],
        ]
    ],


	'general1'=>[
        "position"=>60,
        'url'        => '',
        'title'      => 'Gastos',
        'icon'  => 'icon ion-ios-desktop',
        'permission' => '',
        'children'   => [],
    ],
	'general3'=>[
        "position"=>299,
        'url'        => '',
        'title'      => '',
        'permission' => '',
        'children'   => [],
    ],
	'general4'=>[
        "position"=>299,
        'url'        => '',
        'title'      => '',
        'permission' => '',
        'children'   => [],
    ],
	'general5'=>[
        "position"=>299,
        'url'        => '',
        'title'      => '',
        'permission' => '',
        'children'   => [],
    ],
	'general6'=>[
        "position"=>299,
        'url'        => '',
        'title'      => '',
        'permission' => '',
        'children'   => [],
    ],
	'general7'=>[
        "position"=>299,
        'url'        => '',
        'title'      => '',
        'permission' => '',
        'children'   => [],
    ],
	'general8'=>[
        "position"=>299,
        'url'        => '',
        'title'      => '',
        'permission' => '',
        'children'   => [],
    ],
	'general9'=>[
        "position"=>299,
        'url'        => '',
        'title'      => '',
        'permission' => '',
        'children'   => [],
    ],

	'general10'=>[
        "position"=>299,
        'url'        => '',
        'title'      => '',
        'permission' => '',
        'children'   => [],
    ],
    
    'general'=>[
        "position"=>300,
        'url'        => 'admin/module/core/settings/index/general',
        'title'      => '',
        'permission' => 'setting_update',
        'children'   => \Modules\Core\Models\Settings::getSettingPages(true)
    ],
    'tools'=>[
        "position"=>200,
        'url'      => 'admin/module/core/tools',
        'title'    => __("Tools"),
        'icon'     => 'icon ion-ios-hammer',
        'children' => [
			'location_view'=>[
				'url'        => route('location.admin.index'),
				'title'      => __('All Location'),
				'icon'       => 'icon ion-md-compass',
				'permission' => 'location_view',
			],
			'location_create'=>[
				'url'        => route('location.admin.category.index'),
				'title'      => __("All Category"),
				'icon'       => 'icon ion-md-compass',
				'permission' => 'location_view',
			],
            'language'=>[
                'url'        => 'admin/module/language',
                'title'      => __('Languages'),
                'icon'       => 'icon ion-ios-globe',
                'permission' => 'language_manage',
            ],
            'translations'=>[
                'url'        => 'admin/module/language/translations',
                'title'      => __("Translation Manager"),
                'icon'       => 'icon ion-ios-globe',
                'permission' => 'language_translation',
            ],
            'logs'=>[
                'url'        => 'admin/logs',
                'title'      => __("System Logs"),
                'icon'       => 'icon ion-ios-nuclear',
                'permission' => 'system_log_view',
            ],
			'menu'=>[
				'url'        => 'admin/module/core/menu',
				'title'      => __("Menu"),
				'icon'       => 'icon ion-ios-apps',
				'permission' => 'menu_view',
			],
			'template'=>[
				'url'        => 'admin/module/template',
				'title'      => __('Templates'),
				'icon'       => 'icon ion-logo-html5',
				'permission' => 'template_create',
			],
        ]
    ],
];

// Modules
$custom_modules = \Modules\ServiceProvider::getModules();
if(!empty($custom_modules)){
    foreach($custom_modules as $module){
        $moduleClass = "\\Modules\\".ucfirst($module)."\\ModuleProvider";
        if(class_exists($moduleClass))
        {
            $menuConfig = call_user_func([$moduleClass,'getAdminMenu']);

            if(!empty($menuConfig)){
                $menus = array_merge($menus,$menuConfig);
            }

            $menuSubMenu = call_user_func([$moduleClass,'getAdminSubMenu']);

            if(!empty($menuSubMenu)){
                foreach($menuSubMenu as $k=>$submenu){
                    $submenu['id'] = $submenu['id'] ?? '_'.$k;

                    if(!empty($submenu['parent']) and isset($menus[$submenu['parent']])){
                        $menus[$submenu['parent']]['children'][$submenu['id']] = $submenu;
                        $menus[$submenu['parent']]['children'] = array_values(\Illuminate\Support\Arr::sort($menus[$submenu['parent']]['children'], function ($value) {
                            return $value['position'] ?? 100;
                        }));
                    }
                }

            }
        }

    }
}


// Custom Menu
$custom_modules = \Custom\ServiceProvider::getModules();
if(!empty($custom_modules)){
    foreach($custom_modules as $module){
        $moduleClass = "\\Custom\\".ucfirst($module)."\\ModuleProvider";
        if(class_exists($moduleClass))
        {
            $menuConfig = call_user_func([$moduleClass,'getAdminMenu']);

            if(!empty($menuConfig)){
                $menus = array_merge($menus,$menuConfig);
            }

            $menuSubMenu = call_user_func([$moduleClass,'getAdminSubMenu']);

            if(!empty($menuSubMenu)){
                foreach($menuSubMenu as $k=>$submenu){
                    $submenu['id'] = $submenu['id'] ?? '_'.$k;
                    if(!empty($submenu['parent']) and isset($menus[$submenu['parent']])){
                        $menus[$submenu['parent']]['children'][$submenu['id']] = $submenu;
                        $menus[$submenu['parent']]['children'] = array_values(\Illuminate\Support\Arr::sort($menus[$submenu['parent']]['children'], function ($value) {
                            return $value['position'] ?? 100;
                        }));
                    }
                }

            }
        }

    }
}

$currentUrl = url(\Modules\Core\Walkers\MenuWalker::getActiveMenu());
$user = \Illuminate\Support\Facades\Auth::user();
if (!empty($menus)){
    foreach ($menus as $k => $menuItem) {

        if (!empty($menuItem['permission']) and !$user->hasPermissionTo($menuItem['permission'])) {
            unset($menus[$k]);
            continue;
        }
        $menus[$k]['class'] = $currentUrl == url($menuItem['url']) ? 'active' : '';
        if (!empty($menuItem['children'])) {
            $menus[$k]['class'] .= ' has-children';
            foreach ($menuItem['children'] as $k2 => $menuItem2) {
                if (!empty($menuItem2['permission']) and !$user->hasPermissionTo($menuItem2['permission'])) {
                    unset($menus[$k]['children'][$k2]);
                    continue;
                }
                $menus[$k]['children'][$k2]['class'] = $currentUrl == url($menuItem2['url']) ? 'active' : '';
            }
        }
    }

    //@todo Sort Menu by Position
    $menus = array_values(\Illuminate\Support\Arr::sort($menus, function ($value) {
        return $value['position'] ?? 100;
    }));
}

?>
<ul class="main-menu">
    @foreach($menus as $menuItem)
        @php $menuItem['class'] .= " ".str_ireplace("/","_",$menuItem['url']) @endphp
        <li class="{{$menuItem['class']}}"><a href="{{ url($menuItem['url']) }}">
                @if(!empty($menuItem['icon']))
                    <span class="icon text-center"><i class="{{$menuItem['icon']}}"></i></span>
                @endif
                {!! clean($menuItem['title'],[
                    'Attr.AllowedClasses'=>null
                ]) !!}
            </a>
            @if(!empty($menuItem['children']))
                <span class="btn-toggle"><i class="fa fa-angle-left pull-right"></i></span>
                <ul class="children">
                    @foreach($menuItem['children'] as $menuItem2)
                        <li class="{{$menuItem['class']}}"><a href="{{ url($menuItem2['url']) }}">
                                @if(!empty($menuItem2['icon']))
                                    <i class="{{$menuItem2['icon']}}"></i>
                                @endif
                                {!! clean($menuItem2['title'],[
                                    'Attr.AllowedClasses'=>null
                                ]) !!}</a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </li>
    @endforeach
</ul>
