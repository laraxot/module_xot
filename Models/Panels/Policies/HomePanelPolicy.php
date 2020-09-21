<?php

namespace Modules\Xot\Models\Panels\Policies;

class HomePanelPolicy extends XotBasePanelPolicy
{
    public function index($user, $post)
    {
        return true; //da aggiungere pezzi
    }

    public function show($user, $post)
    {
        return true; //da aggiungere pezzi
    }
}
