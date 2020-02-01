<?php

namespace App\Http\Requests;

use App\Game;
use Illuminate\Foundation\Http\FormRequest;

class NewGameRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'columns' => ['required', 'numeric', 'min:3'],
            'rows' => ['required', 'numeric', 'min:3'],
            'mines' => ['required', 'numeric', 'min:1'],
        ];
    }

    public function createNewGame()
    {
        $game = new Game();

        $game->forceFill([
            'num_columns' => $this->columns,
            'num_rows' => $this->rows,
            'num_mines' => $this->mines
        ]);

        $game->save();

        return $game;
    }
}
