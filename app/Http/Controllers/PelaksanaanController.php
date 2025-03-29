<?php

namespace App\Http\Controllers;

use App\Models\BuktiEvaluasi;
use App\Models\BuktiPelaksanaan;
use App\Models\Indikator;
use App\Models\link;
use App\Models\Penetapan;
use App\Models\Sheet;
use App\Models\Standar;
use App\Models\Target;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PelaksanaanController extends Controller {
    //
    public function postComment(Request $request) {
        // Log::info('posting comment');

        // validasi request
        // $request->validate([
        //     'data.bukti' => 'required|string'
        // ]);
        // $data = $request->json()->all();

        // Retrieve the JSON data from the request
        $bukti_pelaksanaan = $request->json()->all()['data'];
        // Log::info($bukti_pelaksanaan);

        // Ensure $data is always an array for consistent processing
        $bukti_pelaksanaan = is_array($bukti_pelaksanaan) ? $bukti_pelaksanaan : [$bukti_pelaksanaan];

        // loop to check if id pelaksanaan & indikator valid
        $buktiValid = [];
        foreach ($bukti_pelaksanaan as $bukti) {
            // Check if each link contains the necessary fields
            if (!isset($bukti['idBukti']) || !isset($bukti['judul_link']) || !isset($bukti['link'])) {
                // Log::info('Invalid data format', $bukti);
                return $this->sendError('Data harus memiliki idBukti, judul_link, dan link yang valid', $bukti);
            }

            // query to check id is match with $id_pelaksanaan return boolean
            $id_bukti_pelaksanaan = $bukti['idBukti'];
            $isExist = BuktiPelaksanaan::where('id', $id_bukti_pelaksanaan)->exists();
            // Log::info($isExist);
            if (!$isExist) {
                // Log::warning($bukti);
                return $this->sendError('Id bukti pelaksanaan tidak valid', $bukti);
            }
            $linkValid[] = $bukti;
        }

        // create bukti pelaksanaan yang valid saja
        foreach ($buktiValid as $bukti) {
            // Log::info($bukti);
            BuktiPelaksanaan::create([
                'id_pelaksanaan' => $bukti_pelaksanaan['idPelaksanaan'],
                'id_indikator' => $bukti_pelaksanaan['idIndikator'],
                'komentar' => $bukti_pelaksanaan['komentar']
            ]);
        }
        return $this->sendRespons($buktiValid, 'create comment success');
    }

    public function getComment() {
        $data = BuktiPelaksanaan::all();
        return $this->sendRespons($data, 'this is comment data');
    }

    public function delComment(Request $request) {
        $idBukti = $request->input('idBukti');
        $bukti = BuktiPelaksanaan::find($idBukti);
        if (!$bukti) {
            return response()->json(['message' => 'Comment not found.'], 404);
        }
        $bukti->delete();
        return response()->json(['message' => 'Comment deleted successfully.']);
    }

    public function getPelaksanaan($jurusan, $periode, $tipePendidikan, $tipe) {
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
                            foreach ($bukti as $b) {
                                if ($b->id_indikator == $i->id) {
                                    $buk = $b->komentar;
                                    $idB = $b->id;
                                    $pelaksanaanEditor = $b->edited_by;
                                }
                            }

                            $newIndicator = [
                                'idBuktiPelaksanaan' => $idB,
                                'idPelaksanaan' => $shiit->id,
                                'idIndikator' => $i->id,
                                'indicator' => $i->note,
                                'target' => $tar->value,
                                'komentarPelaksanaan' => $buk,
                                'editorPelaksanaan' => $pelaksanaanEditor,
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

    public function submitPelaksanaan(Request $request)
    {
        try {
            $item = $request->input('data');

            $idIndikator = $item['idIndikator'];
            $bukti = $item['komentarPelaksanaan'];
            $idPelaksanaan = $item['idPelaksanaan'];
            $userName = $item['userName'];

            $buktiPelaksanaan = BuktiPelaksanaan::where('id_indikator', $idIndikator)->first();

            if ($bukti !== null) {
                if ($buktiPelaksanaan) {
                    $buktiPelaksanaan->komentar = $bukti;
                    $buktiPelaksanaan->edited_by = $userName;
                    $buktiPelaksanaan->save();
                } else {
                    BuktiPelaksanaan::create([
                        'id_pelaksanaan' => $idPelaksanaan,
                        'id_indikator' => $idIndikator,
                        'komentar' => $bukti,
                        'edited_by' => $userName,
                    ]);
                }
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



    public function getLink($idBukti, $tipeLink) {
        $data = link::where('id_bukti', $idBukti)->where('tipe_link', $tipeLink)->get();

        return response()->json($data);
    }

    public function deleteLink(Request $request) {
        $idLink = $request->input('idLink');
        $id = $idLink['idL'];

        $link = link::find($id);

        if ($link) {
            $link->delete();
        }

        return response()->json("deleted");
    }
    public function postLink(Request $request){
        $data = $request->all();

        $idBukti = $data['idBukti'];
        $judul_link = $data['judul_link'];
        $link = $data['link'];
        $tipeLink = $data['tipeLink'];

        link::create([
            'id_bukti' => $idBukti,
            'judul_link' => $judul_link,
            'link' => $link,
            'tipe_link' => $tipeLink,
        ]);

            // Proses data lebih lanjut di sini

        return response()->json(['success' => 'Link submitted successfully']);

//        return response()->json("null");
    }
}
