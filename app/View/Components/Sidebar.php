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
                    [
                        'label' => 'Product Stock',
                        'route' => 'master-data.stocks',
                        'is_active' => request()->routeIs('master-data.stocks.*'),
                    ],
                ],
            ],
            [
                'label' => 'Transactions',
                'route' => '#',
                'is_active' => request()->routeIs('transactions.*'),
                'icon' => 'fas fa-exchange-alt',
                'is_dropdown' => true,
                'sub_links' => [
                    [
                        'label' => 'Input Transaction',
                        'route' => 'transactions.create',
                        'is_active' => request()->routeIs('transactions.create'),
                    ],
                    [
                        'label' => 'Transaction History',
                        'route' => 'transactions.history',
                        'is_active' => request()->routeIs('transactions.history'),
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
