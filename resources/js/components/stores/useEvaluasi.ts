import { reactive } from 'vue';
import axios from 'axios';

interface EvaluasiPayload {
    idBuktiPelaksanaan: string;
    idEvaluasi: string;
    idIndikator: string;
    komentarEvaluasi: string;
    adjusment: string;
    userName: string;
    indicator: string;
}

// Store Evaluasi dengan reactive()
export const useEvaluasi = reactive({
    loading: false,
    list: [],
    model: reactive<EvaluasiPayload>({
        idBuktiPelaksanaan: '',
        idEvaluasi: '',
        idIndikator: '',
        komentarEvaluasi: '',
        adjusment: '',
        userName: '',
        indicator: '',
    }),
    initial(data?: EvaluasiPayload) {
        this.model.idBuktiPelaksanaan = data?.idBuktiPelaksanaan || '';
        this.model.idEvaluasi = data?.idEvaluasi || '';
        this.model.idIndikator = data?.idIndikator || '';
        this.model.komentarEvaluasi = data?.komentarEvaluasi || '';
        this.model.adjusment = data?.adjusment || '';
        this.model.userName = data?.userName || '';
        this.model.indicator = data?.indicator || '';
    },
});

// Fungsi untuk mengirim data Evaluasi (digabung dari fetchEvaluasi & saveEvaluasi)
export async function submitEvaluasi() {
    useEvaluasi.loading = true;
    try {
        const token = localStorage.getItem('token');
        const response = await axios.post('/api/submitEvaluasi', { data: useEvaluasi.model }, {
            headers: {
                "Authorization": `Bearer ${token}`
            }
        });

        console.log('Data submitted successfully:', response.data);
    } catch (error) {
        console.error('Error submitting data:', error.response?.data || error.message);
    } finally {
        useEvaluasi.loading = false;
    }
}

export async function fetchEvaluasi(jurusan: string, periode: string, currentSheet: string, current: string) {
    useEvaluasi.loading = true;
    try {
        const response = await fetch(`/api/getEvaluasi/${jurusan}/${periode}/${currentSheet}/${current}`);

        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        useEvaluasi.list = await response.json();

    } catch (error) {
        console.error('Error fetching data:', error.message);
    } finally {
        useEvaluasi.loading = false;
    }
}

