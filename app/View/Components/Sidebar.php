<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Sidebar extends Component
{
    /**
     * Create a new component instance.
     */
    public $links;

    public function __construct()
    {
        $this->links = [
            [
                'label' => 'Dashboard',
                'route' => 'home',
                'is_active' => request()->routeIs('home'),
                'icon' => 'fas fa-chart-line',
                'is_dropdown' => false,
            ],
            [
                'label' => 'Master Data',
                'route' => '#',
                'is_active' => request()->routeIs('master-data.*'),
                'icon' => 'fas fa-database',
                'is_dropdown' => true,
                'sub_links' => [
                    [
                        'label' => 'Category',
                        'route' => 'master-data.categories',
                        'is_active' => request()->routeIs('master-data.categories.*'),
                    ],
                    [
                        'label' => 'Product',
                        'route' => 'master-data.products',
                        'is_active' => request()->routeIs('master-data.products.*'),
                    ],
                ],
            ],    
        ];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.sidebar');
    }
}
