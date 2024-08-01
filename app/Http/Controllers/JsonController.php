<?php

namespace App\Http\Controllers;

use App\Models\Indikator;
use App\Models\Penetapan;
use App\Models\Sheet;
use App\Models\Standar;
use App\Models\Target;
use Illuminate\Http\Request;

class JsonController extends Controller
{
    public function index(){
        $penetapan = Penetapan::find(1);
        $standars = Standar::where('id_penetapan', $penetapan->id);
        $indikator = Indikator::all();
        $target = Target::all();

        $response = [];
        foreach ($standars as $s) {

            $data = [
                'standar' => $s->note,
                'indicators' => []
            ];


            foreach ($indikator as $i){
                if ($i->id_id_standar == $s->id){
                    $newIndicator = ['indicator' => $i->note, 'target' => 'Target 1.3', 'komen'=>'Ireland'];
                    array_push($data['indicators'], $newIndicator);
                }
            }

            array_push($response, $data);
        }

        return response()->json($response);
    }

    public function GetSheet(){
        $penetapan = Penetapan::find(7);
        $standars = Standar::where('id_penetapan', $penetapan->id)->where('tipe', '=', 'input')->get();
        $indikator = Indikator::all();
        $target = Target::all();

        $response = [];
        foreach ($standars as $s) {

            $data = [
                'standar' => $s->note,
                'indicators' => []
            ];


            foreach ($indikator as $i){
                if ($i->id_standar == $s->id){
                    $tar = null;
                        foreach ($target as $t){
                        if ($t->id_indikator == $i->id){
                            $tar = $t;
                        }
                    }

                    $newIndicator = ['indicator' => $i->note, 'target' => $tar->value];
                    array_push($data['indicators'], $newIndicator);
                }
            }

            array_push($response, $data);
        }


        return view('welcome', ['name' => $response]);
    }

    public function aa(){
        $penetapan = Penetapan::find(27);
        $standars = Standar::where('id_penetapan', $penetapan->id)->where('tipe', '=', 'input')->get();
        $indikator = Indikator::all();
        $target = Target::all();

        $respond = [];
        foreach ($standars as $s) {

            $data = [
                'standar' => $s->note,
                'indicators' => []
            ];


            foreach ($indikator as $i){
                if ($i->id_standar == $s->id){
                    $tar = null;
                    foreach ($target as $t){
                        if ($t->id_indikator == $i->id){
                            $tar = $t;
                        }
                    }

                    $newIndicator = ['indicator' => $i->note, 'target' => $tar->value];
                    array_push($data['indicators'], $newIndicator);
                }
            }

            array_push($respond, $data);
        }


        return response()->json($respond);
    }

    public function ax(){
        $penetapan = Penetapan::find(27);
        $standars = Standar::where('id_penetapan', $penetapan->id)->where('tipe', '=', 'proses')->get();
        $indikator = Indikator::all();
        $target = Target::all();

        $respond = [];
        foreach ($standars as $s) {

            $data = [
                'standar' => $s->note,
                'indicators' => []
            ];


            foreach ($indikator as $i){
                if ($i->id_standar == $s->id){
                    $tar = null;
                    foreach ($target as $t){
                        if ($t->id_indikator == $i->id){
                            $tar = $t;
                        }
                    }

                    $newIndicator = ['indicator' => $i->note, 'target' => $tar->value];
                    array_push($data['indicators'], $newIndicator);
                }
            }

            array_push($respond, $data);
        }


        return response()->json($respond);
    }

    public function ay(){
        $penetapan = Penetapan::find(27);
        $standars = Standar::where('id_penetapan', $penetapan->id)->where('tipe', '=', 'output')->get();
        $indikator = Indikator::all();
        $target = Target::all();

        $respond = [];
        foreach ($standars as $s) {

            $data = [
                'standar' => $s->note,
                'indicators' => []
            ];


            foreach ($indikator as $i){
                if ($i->id_standar == $s->id){
                    $tar = null;
                    foreach ($target as $t){
                        if ($t->id_indikator == $i->id){
                            $tar = $t;
                        }
                    }

                    $newIndicator = ['indicator' => $i->note, 'target' => $tar->value];
                    array_push($data['indicators'], $newIndicator);
                }
            }

            array_push($respond, $data);
        }


        return response()->json($respond);
    }


}
