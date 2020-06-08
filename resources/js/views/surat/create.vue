<template>
    <component v-bind:is="currentComponent"></component>

</template>

<script>
import SuratMasukComponent from '../../components/suratMasuk/create'
import SuratKeluarkComponent from '../../components/suratKeluar/suratKeluar'
export default {
    data() {
        return {
            currentComponent: ''
        }
    },
    created(){
        // console.log(this.$route)
        if (this.$route.params.surat == 'surat-masuk') {
            this.currentComponent = 'SuratMasuk'
        }else {
           this.currentComponent = 'SuratKeluar'
        }
    },
    beforeRouteUpdate (to, from, next) {
        this.loading = true
        if (to.params.surat == 'surat-masuk') {
            this.currentComponent = 'SuratMasuk'
        }else {
           this.currentComponent = 'SuratKeluar'
        }
        next()
    },
    components:{
        'SuratMasuk':SuratMasukComponent,
        'SuratKeluar' : SuratKeluarkComponent
    },
}
</script>
