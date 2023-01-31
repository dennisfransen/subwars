<?php

namespace App\Http\Controllers;

use App\Http\Resources\RuleTemplateResource;
use App\Models\RuleTemplate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class RuleTemplateController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     *
     * TODO Feature test
     */
    public function index(): AnonymousResourceCollection
    {
        return RuleTemplateResource::collection(RuleTemplate::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RuleTemplateResource
     * TODO Feature test
     * TODO Policy
     */
    public function store(Request $request): RuleTemplateResource
    {
        $ruleTemplate = new RuleTemplate();
        $ruleTemplate->title = $request->title;
        $ruleTemplate->rules = $request->rules;
        $ruleTemplate->save();

        return new RuleTemplateResource($ruleTemplate);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param RuleTemplate $ruleTemplate
     * @return RuleTemplateResource
     * TODO Feature test
     * TODO Policy
     */
    public function update(Request $request, RuleTemplate $ruleTemplate): RuleTemplateResource
    {
        $ruleTemplate->title = $request->title ?? $ruleTemplate->title;
        $ruleTemplate->rules = $request->rules ?? $ruleTemplate->rules;

        if ($ruleTemplate->isDirty())
            $ruleTemplate->save();

        return new RuleTemplateResource($ruleTemplate);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param RuleTemplate $ruleTemplate
     * @return Response
     * TODO Feature test
     * TODO Policy
     */
    public function destroy(RuleTemplate $ruleTemplate): Response
    {
        $ruleTemplate->delete();

        return new Response();
    }
}
