<?php

namespace App\Http\Requests;

class UpdateProjectRequest extends StoreProjectRequest
{
    /** A Policy roda antes da validação: quem não é proprietário recebe 403. */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('project'));
    }
}
