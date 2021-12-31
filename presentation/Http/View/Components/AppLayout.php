<?php

namespace Presentation\Http\View\Components;

use Illuminate\View\Component;

class AppLayout extends Component
{
    public function render()
    {
        return view('layouts.app');
    }
}
