<?php

namespace Presentation\Http\View\Components;

use Illuminate\View\Component;

class GuestLayout extends Component
{
    public function render()
    {
        return view('layouts.guest');
    }
}
