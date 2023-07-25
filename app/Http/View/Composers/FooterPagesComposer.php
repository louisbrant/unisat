<?php

namespace App\Http\View\Composers;

use App\Models\Page;
use Illuminate\Contracts\View\View;

class FooterPagesComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        try {
            $footerPages = Page::where('visibility', '<>', 0)->get();
        } catch (\Exception $e) {
            $footerPages = [];
        }

        $view->with('footerPages', $footerPages);
    }
}