<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewGameRequest;
use Illuminate\Http\Request;

class GameController extends Controller
{
    /**
     * * @ApiDescription(create a game)
     * @param NewGameRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createGame(NewGameRequest $request)
    {

        $game = $request->createNewGame();

        return response()->json([
            'success' => "Successfully created game!",

            'id' => $game->id,
        ], 200);

    }
}
