<?php

namespace App\Http\ViewComposer;

use App\Models\Menu;
use Illuminate\Contracts\View\View;

class MenuComposer
{
    /**
     * Bind the configured navigation menu to the application sidebar.
     */
    public function compose(View $view): void
    {
        $view->with('menu', Menu::query()
            ->with('children')
            ->whereNull('menu_id')
            ->orderBy('priority')
            ->get());
    }
}
