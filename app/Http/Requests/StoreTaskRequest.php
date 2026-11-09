<?php

namespace App\Http\Requests;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('createTask', $this->project());
    }

    protected function project(): Project
    {
        return $this->route('project');
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:5000'],
            // O responsável precisa ser membro deste projeto (consulta ao pivô project_user).
            'assignee_id' => ['nullable', 'integer', Rule::exists('project_user', 'user_id')->where('project_id', $this->project()->id)],
            'priority' => ['required', Rule::enum(TaskPriority::class)],
            'status' => ['sometimes', Rule::enum(TaskStatus::class)],
            'due_date' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Informe um título para a tarefa.',
            'assignee_id.exists' => 'O responsável precisa ser membro do projeto.',
        ];
    }
}
