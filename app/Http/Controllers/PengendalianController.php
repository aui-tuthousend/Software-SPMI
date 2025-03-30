<?php

namespace App\Http\Controllers;

use App\Models\BuktiPelaksanaan;
use App\Models\BuktiPengendalian;
use App\Models\BuktiEvaluasi;
use App\Models\Evaluasi;
use App\Models\Indikator;
use App\Models\Penetapan;
use App\Models\Peningkatan;
use App\Models\Sheet;
use App\Models\Standar;
use App\Models\Target;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PengendalianController extends Controller
{
    public function index()
    {
        $pengendalians = BuktiPengendalian::all();
        return response()->json($pengendalians);
    }

    public function getPengendalian($jurusan, $periode, $tipePendidikan, $tipe) {
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
                $buktiPengendalian = BuktiPengendalian::all();

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
                            $idBPengendalian = '';
                            $temuan = '';
                            $akar_masalah = '';
                            $rtl = '';
                            $pelaksanaan_rtl = '';
                            $pengendalianEditor = '';
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

                                            foreach ($buktiPengendalian as $bp) {
                                                if ($bp->id_bukti_evaluasi == $e->id) {
                                                    $idBPengendalian = $bp->id;
                                                    $temuan = $bp->temuan;
                                                    $akar_masalah = $bp->akar_masalah;
                                                    $rtl = $bp->rtl;
                                                    $pelaksanaan_rtl = $bp->pelaksanaan_rtl;
                                                    $pengendalianEditor = $bp->edited_by;
                                                }
                                            }

                                        }
                                    }
                                }
                            }

                            $newIndicator = [
                                'idPelaksanaan' => $shiit->id,
                                'id' => $i->id,
                                'indicator' => $i->note,
                                'target' => $tar->value,
                                'idBukti' => $idB,
                                'bukti' => $buk,
                                'editorPelaksanaan' => $pelaksanaanEditor,
                                'idEvaluasi' => $idE,
                                'idBuktiEvaluasi' => $idBE,
                                'evaluasi' => $eva,
                                'adjusment' => $adj,
                                'editorEval' => $evalEditor,
                                'idBPengendalian' => $idBPengendalian,
                                'temuan' => $temuan,
                                'akarMasalah' => $akar_masalah,
                                'rtl' => $rtl,
                                'pelaksanaanRtl' => $pelaksanaan_rtl,
                                'editorPengendali' => $pengendalianEditor,
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

    public function submitPengendalian(Request $request)
    {
        try {
            $item = $request->input('data');

            if (!isset($item['idBuktiEvaluasi'], $item['temuan'], $item['akarMasalah'], $item['rtl'], $item['pelaksanaanRtl'], $item['userName'])) {
                return response()->json([
                    'message' => 'Data tidak lengkap',
                    'error' => 'Beberapa parameter hilang'
                ], 400);
            }

            $idBuktiEvaluasi = $item['idBuktiEvaluasi'];
            $temuan = $item['temuan'];
            $akarMasalah = $item['akarMasalah'];
            $rtl = $item['rtl'];
            $pelaksanaanRtl = $item['pelaksanaanRtl'];
            $userName = $item['userName'];

            if (!BuktiEvaluasi::where('id', $idBuktiEvaluasi)->exists()) {
                return response()->json([
                    'message' => 'Id bukti evaluasi tidak valid'
                ], 400);
            }

            $pengendalian = BuktiPengendalian::where('id_bukti_evaluasi', $idBuktiEvaluasi)->first();

            if ($pengendalian) {
                $pengendalian->update([
                    'temuan' => $temuan,
                    'akar_masalah' => $akarMasalah,
                    'rtl' => $rtl,
                    'pelaksanaan_rtl' => $pelaksanaanRtl,
                    'edited_by' => $userName,
                ]);
            } else {
                BuktiPengendalian::create([
                    'temuan' => $temuan,
                    'akar_masalah' => $akarMasalah,
                    'rtl' => $rtl,
                    'pelaksanaan_rtl' => $pelaksanaanRtl,
                    'id_bukti_evaluasi' => $idBuktiEvaluasi,
                    'edited_by' => $userName,
                ]);
            }

            return response()->json([
                'message' => 'Pengendalian berhasil diproses'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat memproses data',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function destroy($id)
    {
        $pengendalian = BuktiPengendalian::findOrFail($id);
        $pengendalian->delete();
        return response()->json(null, 204);
    }
}
