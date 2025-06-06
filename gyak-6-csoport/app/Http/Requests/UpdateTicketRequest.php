<?php

namespace App\Http\Requests;

use App\Models\Ticket;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $ticket = Ticket::findOrFail($this->route('id'));
        return $this->user()->tokenCan('ticket:admin') || $ticket->users->contains($this->user()->id);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:20|min:5',
            'priority' => 'required|integer|min:0|max:3',
            'text' => 'required|string|max:1000',
        ];
    }
}
