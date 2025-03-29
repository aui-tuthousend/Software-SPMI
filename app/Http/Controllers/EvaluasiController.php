<?php

namespace App\Http\Controllers;

use App\Models\BuktiEvaluasi;
use App\Models\BuktiPelaksanaan;
use App\Models\BuktiPengendalian;
use App\Models\Indikator;
use App\Models\link;
use App\Models\Penetapan;
use App\Models\Peningkatan;
use App\Models\Sheet;
use App\Models\Standar;
use App\Models\Target;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Evaluasi;
use Illuminate\Support\Facades\Log;

class EvaluasiController extends Controller {

    public function getEvaluasi($jurusan, $periode, $tipePendidikan, $tipe) {
        $sheets = Sheet::where('jurusan', '=', $jurusan)
            ->where('periode', '=', $periode)
            ->where('tipe_sheet', '=', $tipePendidikan)
            ->get();

        if ($sheets->isEmpty()) {
            return response()->json("Null");
        }

        $respond = [];
        foreach ($sheets as $shiit) {
            $penetapan = Penetapan::where('id_sheet', '=', $shiit->id)->first();
            if ($penetapan) {
                $standars = Standar::where('id_penetapan', $penetapan->id)->where('tipe', '=', $tipe)->get();
                $indikator = Indikator::all();
                $target = Target::all();
                $bukti = BuktiPelaksanaan::all();
                $buktieval = BuktiEvaluasi::all();

                foreach ($standars as $s) {
                    $data = [
                        'standar' => $s->note,
                        'indicators' => []
                    ];

                    foreach ($indikator as $i) {
                        if ($i->id_standar == $s->id) {
                            $tar = null;
                            foreach ($target as $t) {
                                if ($t->id_indikator == $i->id) {
                                    $tar = $t;
                                }
                            }

                            $buk = '';
                            $idB = '';
                            $pelaksanaanEditor = '';
                            $eva = '';
                            $adj = '';
                            $evalEditor = '';
                            $idE = '';
                            $idBE = '';
                            foreach ($bukti as $b) {
                                if ($b->id_indikator == $i->id) {
                                    $buk = $b->komentar;
                                    $idB = $b->id;
                                    $pelaksanaanEditor = $b->edited_by;

                                    foreach ($buktieval as $e) {
                                        if ($e->id_bukti_pelaksanaan == $b->id) {
                                            $idBE = $e->id;
                                            $eva = $e->komentar;
                                            $adj = $e->adjustment;
                                            $idE = $e->id_evaluasi;
                                            $evalEditor = $e->edited_by;
                                        }
                                    }
                                }
                            }

                            $newIndicator = [
                                'idBuktiPelaksanaan' => $idB,
                                'idPelaksanaan' => $shiit->id,
                                'komentarEvaluasi' => $eva,
                                'idIndikator' => $i->id,
                                'idEvaluasi' => $shiit->id,
                                'adjusment' => $adj,
                                'indicator' => $i->note,
                                'target' => $tar->value,

                                'bukti' => $buk,
                                'editorPelaksanaan' => $pelaksanaanEditor,
                                'idBuktiEval' => $idBE,
                                'editorEval' => $evalEditor,
                                'isUpdate' => false,
                            ];
                            array_push($data['indicators'], $newIndicator);
                        }
                    }

                    array_push($respond, $data);
                }
            }
        }

        return response()->json($respond);
    }

    public function submitEval(Request $request)
    {
        try {
            $item = $request->input('data');

            $idBP = $item['idBuktiPelaksanaan'];
            $idEvaluasi = $item['idEvaluasi'];
            $komentarEvaluasi = $item['komentarEvaluasi'];
            $adjusment = $item['adjusment'];
            $userName = $item['userName'];
            $idInd = $item['idIndikator'];
            $indica = $item['indicator'];

            $isEval = BuktiEvaluasi::where('id_bukti_pelaksanaan', $idBP)->first();

            if ($isEval) {
                $isEval->komentar = $komentarEvaluasi;
                $isEval->adjustment = $adjusment;
                $isEval->edited_by  = $userName;
                $isEval->save();
            } else {
                BuktiEvaluasi::create([
                    'id_bukti_pelaksanaan' => $idBP,
                    'id_evaluasi' => $idEvaluasi,
                    'komentar' => $komentarEvaluasi,
                    'adjustment' => $adjusment,
                    'edited_by' => $userName,
                ]);
            }

            $indicator = Indikator::find($idInd);
            if ($indicator && $indicator->note != $indica) {
                $indicator->note = $indica;
                $indicator->save();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil disimpan'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan data',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function delComment(Request $request) {
        $idBukti = $request->input('idBukti');
        $bukti = BuktiEvaluasi::find($idBukti);
        if (!$bukti) {
            return response()->json(['message' => 'Comment not found.'], 404);
        }
        $bukti->delete();
        return response()->json(['message' => 'Comment deleted successfully.']);
    }

    public function getLink($idBukti) {
        $data = link::where('tipe_link', 'bukti_evaluasi')->where('id_bukti', $idBukti)->get();
        if ($data) {
            return response()->json([
                'success' => 'false',
                'message' => 'id bukti evaluasi not found'
            ]);
        }
        return response()->json([
            'success' => 'true'
        ]);
    }
}
