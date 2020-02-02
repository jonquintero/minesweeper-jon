<?php

namespace App\Http\Controllers;

use App\Game;
use App\Http\Requests\NewGameRequest;
use App\RowColumnRevealed;
use App\SetRandomMine;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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

    /**
     * @Description(description="Gets a game by its id")
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGame($id)
    {
        $data = Game::find($id);

        if ($data == null) { return response()->json(['http' => Response::HTTP_NOT_FOUND]); }

        return response()->json($data);
    }

    /**
     * @ApiDescription(save total mines and positions of the current game in case the game is saved)
     * @param Request $request
     * @param $id
     */
    public function setMine(Request $request, $id)
    {
        $totalMine = Game::select('num_mines')->where('id','=', $id)->first();



        $totalSetMines = SetRandomMine::where('game_id','=', $id)->count();

        if($totalSetMines < $totalMine->num_mines){
            $mine = new SetRandomMine();

            $mine->forceFill([
                'game_id' => $id,
                'row' => $request->x,
                'column' => $request->y,
                'mine' => $request->m,
            ]);

            $mine->save();
        }

    }


    /**
     * @ApiDescription(return the totals mines from a game)

     * @param $id
     */
    public function getMine($id)
    {
        $data = SetRandomMine::where('game_id', '=', $id)->get();

        return response()->json($data);
    }

    /**
     * * @ApiDescription(save total cell revealed and positions of the current game in case the game is saved)
     * @param Request $request
     * @param $id
     */
    public function revealedCell(Request $request, $id)
    {
        $cell = new RowColumnRevealed();

        $cell->forceFill([
            'game_id' => $id,
            'row' => $request->x,
            'column' => $request->y,
            'revealed' => true,
        ]);

        $cell->save();

    }

    /**
     * @ApiDescription(delete in database all cells revealed in current game in case the page been refresh o reload)
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetRevealed($id)
    {

        RowColumnRevealed::where('game_id', '=', $id)->delete();

        return response()->json('records destroy');

    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveGame($id)
    {

        $game = Game::find($id);
        $game->is_saved = true;
        $game->save();

        return response()->json('game saved');

    }

}
