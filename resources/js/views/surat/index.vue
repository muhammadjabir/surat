<template>
    <component v-bind:is="currentComponent"></component>

</template>

<script>
import SuratMasukComponent from '../../components/surat/suratMasuk'
import SuratKeluarkComponent from '../../components/surat/suratKeluar'
import SuratMixin from '../../mixins/SuratMixin'
export default {
    data() {
        return {
            currentComponent: ''
        }
    },
    created(){
        console.log(this.$route)
        if (this.$route.params.surat == 'surat-masuk') {
            this.currentComponent = 'SuratMasuk'
        }else {
           this.currentComponent = 'SuratKeluar'
        }
    },
    beforeRouteUpdate (to, from, next) {

        if (to.params.surat == 'surat-masuk') {
              this.currentComponent = 'SuratKeluar'
            this.currentComponent = 'SuratMasuk'
        }else {
           this.currentComponent = 'SuratKeluar'
           this.currentComponent = 'SuratMasuk'
        }
        next()
    },
    filters: {
        namasurat: function (val1) {
            let nama_surat = val1.replace('-',' ')
            nama_surat = nama_surat.toString()
            return nama_surat.charAt(0).toUpperCase() + nama_surat.slice(1)
        }
    },
    components:{
        'SuratMasuk':SuratMasukComponent,
        'SuratKeluar' : SuratKeluarkComponent
    },
     mixins:[SuratMixin],
}
</script>
