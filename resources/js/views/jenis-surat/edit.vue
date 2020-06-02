<template>
    <v-app>
        <v-container>
            <v-btn small color="red accent-4" class="white--text" tile>Edit Jenis Surat</v-btn>
            <v-card
            class="border-edit"
            tile
            >
                <!-- <v-card-text class="text-center"> -->
                <v-card-text>
                    <v-container>
                        <v-form
                        ref="form"
                        v-model="valid"
                        :lazy-validation="lazy"
                        >
                        <label for="" align="left">Tambah Jenis Surat</label>

                        <v-text-field
                        v-model="nama_surat"
                        :rules="nameRules"
                        label="Nama Surat"
                        required
                        ></v-text-field>

                        <v-text-field
                        v-model="inisial"
                        :rules="inisialRules"
                        label="Inisial Surat"
                        required
                        ></v-text-field>

                        <v-row>
                            <v-col
                            cols="6"
                            align="center"
                            >
                              <v-btn
                                tile
                                block
                                @click="$router.go(-1)"
                                >
                                Cancel
                                </v-btn>
                            </v-col>
                            <v-col
                            cols="6"
                            align="center"
                            >
                              <v-btn
                                :disabled="!valid"
                                color="success"
                                tile
                                block
                                @click="save()"
                                :loading="loading"
                                >
                                Simpan
                                </v-btn>
                            </v-col>
                        </v-row>

                    </v-form>
                    </v-container>

                </v-card-text>

                <v-card-actions class="">

                </v-card-actions>
            </v-card>
        </v-container>
    </v-app>

</template>
<script>
// import {mapActions} from 'vuex'
import JenisSurat from '../../mixins/JenisSuratMixin'
import middleware from '../../mixins/middleware'
export default {
    name: 'masterdata.edit',

    mixins:[JenisSurat,middleware],
    methods:{
        async save(){
            this.loading = true
            let url = window.location.pathname
            url = url.split('/')
            url = `${url[1]}/${url[2]}`
            let data = new FormData()
            data.append('nama_surat',this.nama_surat)
            data.append('inisial',this.inisial)
            data.append('_method','PUT')

            await this.axios.post(url,data,this.config)
            .then((ress) => {
                this.setSnakbar({
                    status:true,
                    pesan:ress.data.message,
                    color:'success'
                })
                this.me()
                this.$router.go(-1)
            })
            .catch((err)=>{
                if (err.response.status == 400 ) {
                    this.setSnakbar({
                    color:'red',
                    status:true,
                    pesan:err.response.data.message,
                    })
                }else{
                    this.setSnakbar({
                    status:true,
                    color:'red',
                    pesan:"Terjadi Kesalahan",
                    })
                }

                console.log(err.response)
            })
            this.loading = false

        },
        getDetail(){
            let url = window.location.pathname
            this.axios.get(url,this.config)
            .then((ress) => {
             this.nama_surat = ress.data.nama_surat
             this.inisial = ress.data.inisial
            })
         .catch((err) => console.log(err.response))
        }

    },
    created(){
       this.getDetail()
    }

}
</script>

<style lang="css" scoped>
</style>
