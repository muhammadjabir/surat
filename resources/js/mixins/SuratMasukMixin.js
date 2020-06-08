import {mapActions, mapGetters} from 'vuex'
import middleware from '../mixins/middleware'
export default {
    data: () => ({
        valid: true,
        lazy:false,
        loading:false,
        asal_surat: '',
        asalRules: [
          v => !!v || 'Tidak Boleh Kosong',
        ],
        nomor_surat: '',
        perihal: '',
        keterangan: '',
        tindak_lanjut: false,
        tgl_terima:'',
        tgl_surat:'',
        perioritas:'',
        file_surat: '',
        items: [
            'Sangat Segera',
            'Segera',
            'Rahasia',
            'Penting',
            'Biasa',
          ],
        menu1:'',
        menu2:'',
        menu3:'',
        tgl_disposisi:'',
        isi_disposisi:'',

      }),
    methods: {
        ...mapActions({
            setSnakbar: 'snakbar/setSnakbar',
            setAuth : 'auth/setAuth',
            setDisposisi: 'Disposisi/setDialog'
        }),

        me(){
            this.axios.get('/me',this.config)
            .then((ress) => {
                this.setAuth({
                    menu:ress.data.menu,
                    user:ress.data.user,
                    token:ress.data.access_token
                })
            })
            .catch((err) =>console.log(err.response))
        },
        async disposisi(id_surat = ''){

            this.setDisposisi(!this.dialogDisposisi)
        }

    },
    computed: {
        ...mapGetters({
            dialogDisposisi: 'Disposisi/dialogData'
        })
    },

    mixins:[middleware]

}
