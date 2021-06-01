<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\Models\Title;
use App\Models\Reservation;
use App\Models\User;

use DateTime;
use DatePeriod;
use DateInterval;

class TitleController extends Controller
{
    public function getAll(Request $request) {
        $array = ['error' => '', 'list' => ''];

        if ($request->input('mytitles')) {
            $titles = Title::where('id_owner', Auth::user()->id)->get();
        } else {
            $titles = Title::all();
        }


        foreach ($titles as $title) {
            $owner = User::select('name')->where('id', $title['id_owner'])->first();
            $title['owner'] = $owner->name;
        }

        $array['list'] = $titles;

        return $array;
    }

    public function addTitle(Request $request) {
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'title' => 'required'
        ]);
        if ($validator->fails()) {
            $array['error'] = $validator->errors()->first();
            return response()->json($array, 422);
        }

        $newTitle = new Title;
        $newTitle->title = $request->input('title');
        $newTitle->id_owner = Auth::user()->id;
        $newTitle->save();

        $array['msg'] = 'Título inserido com sucesso!';

        return $array;
    }

    public function getDisabledDates(Request $request) {
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'title_id' => 'required'
        ]);
        if ($validator->fails()) {
            $array['error'] = $validator->errors()->first();
            return response()->json($array, 422);
        }

        $titles = Reservation::where('title_id', $request->input('title_id'))
            ->where('end_date', '>=', date('Y-m-d'))
        ->get();

        $disabledDates = [];

        foreach ($titles as $title) {
            $begin = new DateTime($title['start_date']);
            $end = new DateTime($title['end_date']);
            $end->add(new DateInterval('P1D'));

            $dateRange = new DatePeriod($begin, new DateInterval('P1D'), $end);

            foreach($dateRange as $date){
                /* echo $date->format("Y-m-d") . "\n"; */
                $disabledDates[] = $date->format("Y-m-d");
            }
        }

        $array['disabledDates'] = $disabledDates;

        return $array;
    }

    public function delTitle($id) {
        $array = ['error' => ''];

        $title = Title::where('id', $id)->get();
        if ($title->count() < 1) {
            $array['error'] = 'Título inexistente';
            return response()->json($array, 404);
        }

        $reservations = Reservation::where('title_id', $id)
            ->where('end_date', '>=', date('Y-m-d'))
        ->get();
        if ($reservations->count() > 0) {
            $array['error'] = 'Este item possui reservas no momento, portanto não pode ser excluído';
            return response()->json($array, 422);
        }

        $titleToDel = Title::find($id);
        $titleToDel->delete();
        
        $array['msg'] = 'Título retirado com sucesso!';

        return $array;
    }
}
