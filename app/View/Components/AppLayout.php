<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    /**
     * A page passes its heading as plain attributes, and the layout draws the
     * teal header and the browser tab title from them:
     * <x-app-layout title="Questions" subtitle="Create, edit, retire">
     */
    public function __construct(
        public string $title = '',
        public string $subtitle = '',
    ) {
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.app');
    }
}
