<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'locale' => ['required', 'in:en,es'],
        ]);

        $request->session()->put('locale', $validated['locale']);

        if ($request->user()) {
            $request->user()->forceFill(['locale' => $validated['locale']])->save();
        }

        return back();
    }
}
