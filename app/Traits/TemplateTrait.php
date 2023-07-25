<?php

namespace App\Traits;

use App\Models\Template;
use Illuminate\Http\Request;

trait TemplateTrait
{
    /**
     * Store the Template.
     *
     * @param Request $request
     * @return Template
     */
    protected function templateStore(Request $request)
    {
        $template = new Template;

        $template->user_id = ($request->is('admin/*') ? 0 : $request->user()->id);
        $template->name = $request->input('name');
        $template->icon = $request->input('icon');
        $template->description = $request->input('description');
        $template->prompt = $request->input('prompt');
        $template->category_id = 'custom';
        $template->save();

        return $template;
    }

    /**
     * Update the Template.
     *
     * @param Request $request
     * @param Template $template
     * @return Template
     */
    protected function templateUpdate(Request $request, Template $template)
    {
        if ($request->has('name')) {
            $template->name = $request->input('name');
        }

        if ($request->has('icon')) {
            $template->icon = $request->input('icon');
        }

        if ($request->has('description')) {
            $template->description = $request->input('description');
        }

        if ($request->has('prompt')) {
            $template->prompt = $request->input('prompt');
        }

        $template->save();

        return $template;
    }
}
