<template>
  <div class="text-center">
    <v-dialog
      v-model="dialogDisposisi"
      width="540"
    >
      <v-card
      tile
      >
        <v-card-title
          class="red darken-4 white--text"
          primary-title
        >
          Disposisikan
        </v-card-title>

        <v-card-text>
          <v-container>
               <v-form
                    ref="form"
                    v-model="valid"
                    :lazy-validation="lazy"
                    >

                    <v-menu
                            v-model="menu3"
                            :close-on-content-click="false"
                            :nudge-right="40"
                            transition="scale-transition"
                            offset-y
                            min-width="290px"
                    >
                        <template v-slot:activator="{ on }">
                        <v-text-field
                            v-model="tgl_disposisi"
                            label="Tanggal Disposisi"
                            readonly
                            v-on="on"
                        ></v-text-field>
                        </template>
                        <v-date-picker v-model="tgl_disposisi" @input="menu3 = false"></v-date-picker>
                    </v-menu>
                    <v-select
                        v-model="value_disposisi"
                        :items="disposisi_data"
                        item-text="name"
                        item-value="id"
                        attach
                        chips
                        label="Disposisikan"
                        multiple
                    ></v-select>

                    <v-textarea
                    label="Isi Disposisi"
                    v-model="isi_disposisi"
                    ></v-textarea>
               </v-form>
          </v-container>
        </v-card-text>

        <v-divider></v-divider>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="red"
            tile
            class="white--text"
            @click="disposisi()"
          >
            Cancel
          </v-btn>

           <v-btn
            color="success"
            tile
            @click="save()"
            :loading="loading"
          >
            Save
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import {mapActions,mapGetters} from 'vuex'
import middleware from '../../mixins/middleware'
import SuratMasukMixin from '../../mixins/SuratMasukMixin'
  export default {
      props:['disposisi_data','id_surat','value_disposisi'],
        methods:{
       ...mapActions({
           setDialog : 'dialog/setDialog',
           setAuth : 'auth/setAuth' ,
           setSnakbar : 'snakbar/setSnakbar'
       }),

       async save(){
            this.loading = true
            let url =this.$route.path + '/disposisi'
            this.id_surat = this.id_surat

            let data = new FormData()
            data.append('id_surat',this.id_surat)
            data.append('tgl_disposisi',this.tgl_disposisi)
            data.append('isi_disposisi',this.isi_disposisi)
            data.append('value_disposisi', JSON.stringify(this.value_disposisi))
            await this.axios.post(url,data,this.config)
            .then((ress) => {
                console.log(ress)

                this.setSnakbar({
                   color:'success',
                   pesan : 'Berhasil Simpan Disposisi',
                   status: true
               })
                this.setDisposisi(!this.dialogDisposisi)
            })
            .catch((err) => {
                console.log(err.response)

            })
           this.loading = false
       },
   },
   mixins:[middleware,SuratMasukMixin],

  }
</script>
