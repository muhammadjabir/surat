import {mapActions} from 'vuex'
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
        tgl_terima:null,
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
        menu2:''
      }),
    methods: {
        ...mapActions({
            setSnakbar: 'snakbar/setSnakbar',
            setAuth : 'auth/setAuth'
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
    },
    mixins:[middleware]

}
