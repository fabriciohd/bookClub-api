<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\Models\Reservation;
use App\Models\Title;

class ReservationController extends Controller
{
    public function addReservation(Request $request) {
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'title_id' => 'required',
            'start_date' => 'required|date|date_format:Y-m-d|after:now',
            'end_date' => 'required|date|date_format:Y-m-d|after:start_date|before_or_equal:'.date('Y-m-d', strtotime("+5 day", strtotime($request->input('start_date'))))
        ]);
        if ($validator->fails()) {
            $array['error'] = $validator->errors()->first();
            return response()->json($array, 422);
        }

        /* Verificando se o título existe */
        $title = Title::select()->where('id', $request->input('title_id'))->first();
        if ($title->count() < 1) {
            $array['error'] = 'Título Inexistente.';
            return response()->json($array, 404);
        }
        /* Verificando se o título não pertence ao usuário logado */
        if ($title->id_owner == Auth::user()->id) {
            $array['error'] = 'Título já pertence ao usuário';
            return response()->json($array, 422);
        }
        /* Verificando se as datas enviadas estão disponíveis para esse título */
        $resStartDate = Reservation::where('title_id', $request->input('title_id'))->whereBetween('start_date', [
            $request->input('start_date'),
            $request->input('end_date')
        ])->get();
        $resEndDate = Reservation::where('title_id', $request->input('title_id'))->whereBetween('end_date', [
            $request->input('start_date'),
            $request->input('end_date')
        ])->get();
        if ($resStartDate->count() > 0 || $resEndDate->count() > 0) {
            $array['error'] = 'Data de reserva não disponível para este título';
            return response()->json($array, 422);
        }
        /* Verificando se usuário tem a quantidade de reservas dísponíveis */
        $lesStartDate = Reservation::where('id_lessee', Auth::user()->id)->whereBetween('start_date', [
            $request->input('start_date'),
            $request->input('end_date')
        ])->get();
        $lesEndDate = Reservation::where('id_lessee', Auth::user()->id)->whereBetween('end_date', [
            $request->input('start_date'),
            $request->input('end_date')
        ])->get();
        if ($lesStartDate->count() >= 2 || $lesEndDate->count() >= 2) {
            $array['error'] = 'Não é permitido a reserva de mais de 2 itens em um mesmo período';
            return response()->json($array, 422);
        }

        $newReservation = new Reservation;
        $newReservation->title_id = $request->input('title_id');
        $newReservation->id_owner = $title->id_owner;
        $newReservation->id_lessee = Auth::user()->id;
        $newReservation->start_date = $request->input('start_date');
        $newReservation->end_date = $request->input('end_date');
        $newReservation->save();

        $array['msg'] = 'Reserva efetuada com sucesso!';

        return $array;
    }
}
