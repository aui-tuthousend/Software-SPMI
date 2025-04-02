<?php

namespace App\Http\Controllers;

use App\Models\BuktiEvaluasi;
use App\Models\BuktiPelaksanaan;
use App\Models\BuktiPengendalian;
use App\Models\Evaluasi;
use App\Models\Indikator;
use App\Models\Penetapan;
use App\Models\Peningkatan;
use App\Models\Sheet;
use App\Models\Standar;
use App\Models\Target;
use Illuminate\Http\Request;

class PeningkatanController extends Controller
{
    public function getPeningkatan($jurusan, $periode, $tipePendidikan, $tipe) {
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
            $evaluasi = Evaluasi::where('id_sheet', '=', $shiit->id)->first();
            if ($penetapan) {
                $standars = Standar::where('id_penetapan', $penetapan->id)->where('tipe', '=', $tipe)->get();
                $indikator = Indikator::all();
                $target = Target::all();
                $bukti = BuktiPelaksanaan::all();
                $buktieval = BuktiEvaluasi::all();
                $buktiPengendalian = BuktiPengendalian::all();
                $buktiPeningkatan = Peningkatan::all();

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
                            $idPeningkatan = '';
                            $komenPeningkatan = '';
                            $peningkatanEditor = '';
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

                                                    foreach ($buktiPeningkatan as $p) {
                                                        if ($p->id_pengendalian == $bp->id) {
                                                            $idPeningkatan = $p->id;
                                                            $komenPeningkatan = $p->komentar;
                                                            $peningkatanEditor = $p->edited_by;
                                                        }
                                                    }

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
                                'idBuktiPengendalian' => $idBPengendalian,
                                'temuan' => $temuan,
                                'akar_masalah' => $akar_masalah,
                                'rtl' => $rtl,
                                'pelaksanaan_rtl' => $pelaksanaan_rtl,
                                'editorPengendali' => $pengendalianEditor,
                                'idPeningkatan' => $idPeningkatan,
                                'komenPeningkatan' => $komenPeningkatan,
                                'editorPeningkatan' => $peningkatanEditor,
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

    public function submitPeningkatan(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'data.idBuktiPengendalian' => 'required|exists:bukti_pengendalians,id',
                'data.komenPeningkatan' => 'required|string',
                'data.userName' => 'required|string',
            ]);

            $idBuktiPengendalian = $validatedData['data']['idBuktiPengendalian'];
            $komen = $validatedData['data']['komenPeningkatan'];
            $editor = $validatedData['data']['userName'];

            $peningkatan = Peningkatan::updateOrCreate(
                ['id_pengendalian' => $idBuktiPengendalian],
                ['komentar' => $komen, 'edited_by' => $editor]
            );

            return response()->json([
                'message' => 'Peningkatan berhasil diproses',
                'data' => $peningkatan
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat memproses data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
