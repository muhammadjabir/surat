<template>
    <v-app>
        <Progress v-if="loading"/>
        <v-container v-if="!loading">
            <v-btn small color="red accent-4" class="white--text" tile>{{ $route.params.surat | namasurat }} : {{ $route.params.jenis | namasurat }}</v-btn>
            <v-card
            class="border-edit"
            tile
            >
                <v-card-text class="text-center">
                    <v-container>
                        <v-row justify="center" align="center">
                            <v-col
                                cols="6"
                            >
                            <v-text-field
                                v-model="keyword"
                                label="Pencarian"
                                v-on:keyup = "go"
                                color="red accent-4"
                            ></v-text-field>
                            </v-col>

                            <v-col
                                cols="6"
                                align="right"
                            >
                                <v-btn color="primary"  :to="urlcreate" small tile>
                                    Tambah Data
                                </v-btn>
                            </v-col>
                        </v-row>
                    </v-container>

                    <v-simple-table>
                        <template v-slot:default>
                        <thead>
                            <tr>
                            <th class="text-left">ID Surat</th>

                            <th class="text-left" v-if="user.role.description == 'Kesekretariatan'">Dikirim</th>
                            <th class="text-left">Tanggal Terima</th>
                            <th class="text-left">Nomor Surat</th>
                            <th class="text-left">Tanggal Surat</th>
                            <th class="text-left">Asal</th>
                            <th class="text-left">Perihal</th>
                            <th v-if="user.role.description == 'Developer'
                            || user.role.id == 37
                            || user.role.description == 'Administrator'
                            || user.role.description == 'Kasi'
                            ">
                            Ekspedisi
                            </th>
                            <th class="text-left">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in data" :key="item.id">
                                <td class="text-left">{{item.id}}</td>
                                <td class="text-left" v-if="user.role.description == 'Kesekretariatan'" >
                                    <span v-if="item.dikirim">
                                        {{ item.dikirim }}
                                    </span>
                                    <v-btn color="success" @click="teruskan(item.id)" v-else x-small dark >
                                        Teruskan
                                    </v-btn>
                                </td>
                                 <td class="text-left">{{item.tanggal_terima}}</td>
                                 <td class="text-left">{{item.nomor_surat}}</td>
                                 <td class="text-left">{{item.tanggal_surat}}</td>
                                 <td class="text-left">{{item.asal_surat}}</td>
                                 <td class="text-left">{{item.perihal}}</td>
                                 <td class="text-left"
                                  v-if="user.role.description == 'Developer'
                                    || user.role.id == 37
                                    || user.role.description == 'Administrator'
                                    || user.role.description == 'Kasi'
                                    "
                                 >Ekspedisi</td>
                                <td class="text-left">
                                <v-menu
                                v-model="value"
                                :close-on-click="true"
                                :close-on-content-click="true"
                                :offset-y="true"
                                >
                                <template v-slot:activator="{ on }">
                                    <v-btn
                                    color="primary"
                                    dark
                                    v-on="on"
                                    small
                                    >
                                    Aksi
                                    </v-btn>
                                </template>
                                <v-list>
                                    <v-list-item
                                    @click=""
                                    v-if="item.status == 0"
                                    >
                                    <v-list-item-title>Edit</v-list-item-title>
                                    </v-list-item>

                                    <v-list-item
                                    @click=""
                                    >
                                    <v-list-item-title>View</v-list-item-title>
                                    </v-list-item>

                                    <v-list-item
                                    @click=""
                                     v-if="user.role.description == 'Developer'
                                    || user.role.id == 37
                                    || user.role.description == 'Administrator'
                                    || user.role.description == 'Kasi'
                                    "
                                    >
                                    <v-list-item-title>Disposisikan</v-list-item-title>
                                     </v-list-item>
                                    <v-list-item
                                    @click=""
                                     v-if="user.role.description == 'Developer'
                                    || user.role.id == 37
                                    || user.role.description == 'Administrator'
                                    || user.role.description == 'Kasi'
                                    "
                                    >
                                    <v-list-item-title>Cetak Disposisi</v-list-item-title>
                                     </v-list-item>
                                    <v-list-item
                                    @click=""

                                    >
                                    <v-list-item-title>Dokumen</v-list-item-title>
                                    </v-list-item>
                                </v-list>
                                </v-menu>
                                </td>
                            </tr>
                        </tbody>
                        </template>
                    </v-simple-table>
                </v-card-text>
                <div class="text-center">
                    <v-pagination
                    v-model="page"
                    :length="lengthpage"
                    :total-visible="7"
                    @input="go"
                    color="red accent-4"
                    ></v-pagination>
                </div>
                <v-card-actions class="">

                </v-card-actions>
            </v-card>
        </v-container>
    </v-app>

</template>

<script>
import SuratMixin from '../../mixins/SuratMixin'
export default {
    created(){
        console.log(this.$route)
    },
    beforeRouteUpdate (to, from, next) {
        this.loading = true
        this.url = to.fullPath
        this.urlcreate = to.fullPath + `/create`
        this.go()
        next()
    },
    filters: {
        namasurat: function (val1) {
            let nama_surat = val1.replace('-',' ')
            nama_surat = nama_surat.toString()
            return nama_surat.charAt(0).toUpperCase() + nama_surat.slice(1)
        }
    },
    mixins:[SuratMixin],
    created(){
    }
}
</script>
