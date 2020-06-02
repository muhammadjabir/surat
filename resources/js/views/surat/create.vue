<template>
    <v-app>
        <v-container>
            <v-btn small color="red accent-4" class="white--text" tile>Tambah Jenis Surat</v-btn>
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

                        <v-menu
                            v-model="menu1"
                            :close-on-content-click="false"
                            :nudge-right="40"
                            transition="scale-transition"
                            offset-y
                            min-width="290px"
                        >
                            <template v-slot:activator="{ on }">
                            <v-text-field
                                v-model="tgl_terima"
                                label="Tanggal Terima"
                                readonly
                                v-on="on"
                            ></v-text-field>
                            </template>
                            <v-date-picker v-model="tgl_terima" @input="menu1 = false"></v-date-picker>
                        </v-menu>

                        <v-text-field
                        v-model="asal_surat"
                        :rules="asalRules"
                        label="Asal Surat"
                        required
                        ></v-text-field>

                        <v-text-field
                        v-model="nomor_surat"
                        :rules="asalRules"
                        label="Nomor Surat"
                        required
                        ></v-text-field>

                        <v-menu
                            v-model="menu2"
                            :close-on-content-click="false"
                            :nudge-right="40"
                            transition="scale-transition"
                            offset-y
                            min-width="290px"
                        >
                            <template v-slot:activator="{ on }">
                            <v-text-field
                                v-model="tgl_surat"
                                label="Tanggal Surat"
                                readonly
                                v-on="on"
                            ></v-text-field>
                            </template>
                            <v-date-picker v-model="tgl_surat" @input="menu2 = false"></v-date-picker>
                        </v-menu>
                         <v-select
                            v-model="perioritas"
                            :items="items"
                            :rules="[v => !!v || 'Tidak Boleh Kosong']"
                            label="Prioritas"
                            required
                        ></v-select>

                        <v-textarea
                        autocomplete="email"
                        label="Perihal"
                        v-model="perihal"
                        ></v-textarea>

                         <v-textarea
                        autocomplete="email"
                        label="Keterangan"
                        v-model="keterangan"
                        ></v-textarea>

                        <input type="file" @change="getFile" >

                        <v-checkbox
                        v-model="tindak_lanjut"
                        :label="`Perlu Ditindaklanjuti : ${tindak_lanjut == false ? 'Tidak' : 'Ya'}`"
                        ></v-checkbox>
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
import SuratMasuk from '../../mixins/SuratMasukMixin'
import middleware from '../../mixins/middleware'
export default {
    name: 'surat.create',

    mixins:[SuratMasuk,middleware],
    methods:{
        getFile(event){
            this.file_surat = event.target.files[0]
            console.log(this.file_surat)
        },
        async save(){
            this.loading = true
            let url = window.location.pathname
            url = url.split('/')
            url = "/" + url[1] + '/'+ url[2]
            let data = new FormData()
            data.append('asal_surat',this.asal_surat)
            data.append('nomor_surat',this.nomor_surat)
            data.append('perihal',this.perihal)
            data.append('keterangan',this.keterangan)
            data.append('tindak_lanjut',this.tindak_lanjut)
            data.append('tgl_terima',this.tgl_terima)
            data.append('tgl_surat',this.tgl_surat)
            data.append('perioritas',this.perioritas)
            data.append('file_surat',this.file_surat)
            await this.axios.post(url,data,this.config)
            .then((ress) => {
                console.log(ress)
                this.setSnakbar({
                    status:true,
                    pesan:ress.data.message,
                    color:'success'
                })
                this.me()
                this.$router.push(url)
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

    }

}
</script>

<style lang="css" scoped>
</style>
