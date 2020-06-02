import Progress from '../components/Progress'
import {mapActions, mapGetters} from 'vuex'
import middleware from './middleware'

export default {
    data(){
        return {
            data : [],
            page: null,
            lengthpage: null,
            loading:true,
            keyword:'',
            urlcreate: '',
            url: '',
            dialog:false,
            idDelete: null,
            value:false
        }
    },

    components:{
        'Progress' : Progress
    },

    mixins:[middleware],
    methods : {
        ...mapActions({
            setSnakbar:'snakbar/setSnakbar'
        }),


        // method edit
        edit(id){
            let url = this.url + `/${id}/edit`
            console.log(url)
            this.$router.push(url)
        },
        async go(){
            let url = this.url
            if(this.page > 1) {
                url = url + '?page=' +this.page + "&keyword=" + this.keyword
            }else{
                url = url + "?keyword=" + this.keyword
            }
            await this.axios.get(url,this.config)
            .then((ress)=>{
                // console.log(ress)
                this.data = ress.data.data
                this.page = ress.data.current_page ? ress.data.current_page : ress.data.meta.current_page
                this.lengthpage = ress.data.last_page ? ress.data.last_page : ress.data.meta.last_page
            })
            .catch((err)=>{
                console.log(err.response)
            })
            this.loading = false
        },

        async teruskan(id_surat){
            let index = this.data.findIndex(x => x.id === id_surat)
            let data = new FormData()
            data.append('id',id_surat)
            let url = window.location.pathname + '/status'
            await this.axios.post(url,data,this.config)
            .then((ress) => {
                let data = ress.data.surat
                this.data.splice(index,1,data)
                this.setSnakbar({
                    status:true,
                    pesan:ress.data.message,
                    color:'success'
                })

            })
            .catch((err) => {
                console.log(err.response)


            })
        }

    },

    mounted() {

    },

    created(){
        this.url = window.location.pathname
        this.go()
        this.urlcreate = this.url + '/create'
    }
}
