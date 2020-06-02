import {mapActions} from 'vuex'
import middleware from '../mixins/middleware'
export default {
    data: () => ({
        valid: true,
        lazy:false,
        loading:false,
        nama_surat: '',
        nameRules: [
          v => !!v || 'Tidak Boleh Kosong',
        ],
        inisial: '',
        inisialRules: [
          v => !!v || 'Tidak Boleh Kosong',
        ],
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
