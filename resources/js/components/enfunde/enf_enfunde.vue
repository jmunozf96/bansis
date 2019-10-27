<template>
    <div class="container-fluid">
        <div class="form-row mt-3 mb-0">
            <div class="form-group col-md-8">
                <select class="selectpicker show-tick form-control"
                        data-live-search="true" multiple data-max-options="1"
                        title="Seleccionar lotero a reportar enfunde..."
                        id="lotero">
                    <option v-for="lotero in this.loteros" :value="lotero.id">{{lotero.empleado.nombre}}</option>
                </select>
                <small>Buscar lotero por nombre o apellido</small>
            </div>
            <div class="form-group col-md-4 mb-">
                <input type="text" class="form-control form-control-lg bg-dark text-right"
                       placeholder="Detalle"
                       :value="'Fundas Total: ' + totalfundas"
                       id="detalle" style="color: #41DB00; font-size: 25px"
                       oninput="this.value = this.value.toUpperCase()" disabled>
                <small>Total de fundas despachadas en la semana</small>
            </div>
        </div>
        <hr class="mt-0">
        <div class="form-row mt-0">
            <div class="col-md-6">
                <div class="alert alert-success" role="alert" v-if="statusEnfunde()">
                    Puede reportar enfunde
                </div>
                <div class="alert alert-danger" role="alert" v-else>
                    No puede reportar enfunde
                </div>
                <p class="mb-1">Lotes asignados:</p>
                <table class="table table-bordered table-hover" id="enfunde-items">
                    <thead>
                    <tr class="text-center">
                        <th>Estado</th>
                        <th>Lote</th>
                        <th>Has.</th>
                        <th>%</th>
                        <th>Presente</th>
                        <th>Futuro</th>
                        <th>Chapeo</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(sec, index) in seccion" class="text-center">
                        <td>
                            <span v-if="statusForm  && sec.idlote == lote_enfunde.idlote">
                                <button class="btn btn-primary btn-sm"
                                        v-on:click="saveForm(index)">
                                    <i class="fas fa-save"></i> Guardar
                                </button>
                            </span>
                            <span v-else>
                                <button class="btn btn-success btn-sm"
                                        v-on:click="editForm(index)">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                            </span>
                        </td>
                        <td>{{sec.lote}}</td>
                        <td>{{sec.has}}</td>
                        <td>
                            <b-progress :max="100">
                                <b-progress-bar :value="sec.porcentaje * 100"
                                                :label="`${(sec.porcentaje * 100).toFixed(2)}%`"></b-progress-bar>
                            </b-progress>
                        </td>
                        <td style="width: 15%">
                            <span v-if="statusForm && sec.idlote == lote_enfunde.idlote && presente">
                                <input class="form-control text-center cantidad-despacho" ref="presente"
                                       v-model="lote_enfunde.presente"
                                       type="number"/>
                            </span>
                            <span v-else>
                                {{sec.presente}}
                            </span>
                        </td>
                        <td style="width: 15%">
                            <span v-if="statusForm && sec.idlote == lote_enfunde.idlote && futuro">
                                <input class="form-control text-center cantidad-despacho" ref="futuro"
                                       v-model="lote_enfunde.futuro"
                                       type="number"/>
                            </span>
                            <span v-else>
                                {{sec.futuro}}
                            </span>
                        </td>
                        <td style="width: 15%">
                            <span v-if="statusForm && sec.idlote == lote_enfunde.idlote && futuro">
                                <input class="form-control text-center cantidad-despacho" ref="chapeo"
                                       v-model="lote_enfunde.chapeo"
                                       type="number"/>
                            </span>
                            <span v-else>
                                {{sec.chapeo}}
                            </span>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="form-row p-1 mb-2">
                    <input id="detalle-total" type="text" style="font-size: 22px"
                           class="form-control form-control-lg bg-white text-right"
                           :value="`Enfunde Total de la semana: ${totalEnfunde()}`"
                           disabled>
                </div>
            </div>
            <div class="col-md-6">
                <div class="col-12">
                    <p class="mb-1">Fundas despachadas:</p>
                    <table class="table table-bordered table-hover" id="despacho-items">
                        <thead>
                        <tr class="text-center">
                            <th>Fecha</th>
                            <th>Fundas Desp.</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(fundas, index) in fundas" class="text-center">
                            <td>{{fundas.fecha}}</td>
                            <td>{{fundas.cantidad}}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-12">
                    <p class="mb-1">Lista de reemplazos y cantidad de fundas despachadas:</p>
                    <table class="table table-bordered table-hover" id="reemplazo-items">
                        <thead>
                        <tr class="text-center">
                            <th>Fecha</th>
                            <th>Reemplazo</th>
                            <th>Fundas Desp.</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(reemplazo, index) in reemplazos" class="text-center">
                            <td>{{reemplazo.fecha}}</td>
                            <td>{{reemplazo.nombre}}</td>
                            <td>{{reemplazo.cantidad}}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import 'bootstrap-daterangepicker/daterangepicker.css';
    import moment from 'moment/moment';
    import SweetAlert from 'sweetalert2/src/sweetalert2';
    import BootstrapVue from 'bootstrap-vue';
    Vue.use(BootstrapVue);

    const Swal = SweetAlert;

    export default{

        props: ['loteros'],
        data(){
            return {
                fecha: $('#fecha').val(),
                hacienda: $('#id-hacienda').val(),
                semana: $('#semana').val(),
                periodo: $('#periodo').val(),
                idlotero: 0,
                seccion: [],
                total_pre: 0,
                total_fut: 0,
                chapeo: 0,
                fundas: [],
                reemplazos: [],
                totalfundas: 0,
                statusForm: false,
                lote_enfunde: {
                    idlote: 0,
                    presente: 0,
                    futuro: 0,
                    chapeo: 0
                },
                presente: 1,
                futuro: 0
            }
        },
        mounted(){
            console.log('cargo vue');
            var self = this;

            $('#lotero').on({
                change: function (e) {
                    e.preventDefault();
                    let id = $(this).val()[0];

                    if (id == '') {
                        alert('Seleccioone un lotero');
                        return;
                    }

                    axios.get(`/sistema/axios/enfunde/lotero/${id}/${self.semana}`)
                        .then(response => {
                            if (response.data) {
                                console.log(response.data);
                                self.totalfundas = 0;
                                self.seccion = [];
                                self.reemplazos = [];
                                self.fundas = [];

                                let datos = response.data[0].seccion;
                                for (var i in datos) {
                                    var seccion = {
                                        idlote: datos[i].idlote,
                                        lote: datos[i].lote.lote,
                                        has: (parseFloat(datos[i].has)).toFixed(2),
                                        hasTotal: (parseFloat(datos[i].lote.has)).toFixed(2),
                                        presente: 0,
                                        futuro: 0,
                                        chapeo: 0,
                                        porcentaje: (parseFloat(datos[i].has) / parseFloat(datos[i].lote.has)).toFixed(2)
                                    };
                                    self.seccion.push(seccion);
                                }

                                let datos2 = response.data[0].fundas_reemplazo;

                                if (datos2.length > 0) {
                                    for (var i in datos2) {
                                        var reemplazo = {
                                            fecha: (datos2[i].fecha).toString("dd/MM/yyyy"),
                                            nombre: datos2[i].get_egreso.empleado.nombre,
                                            cantidad: parseInt(datos2[i].cantidad),
                                        }

                                        self.totalfundas += parseInt(datos2[i].cantidad);
                                        self.reemplazos.push(reemplazo);
                                    }
                                }

                                let datos3 = response.data[0].fundas;

                                if (datos3) {
                                    for (var i in datos3.egresos) {
                                        var despacho = {
                                            fecha: (datos3.egresos[i].fecha).toString("dd/MM/yyyy"),
                                            cantidad: parseInt(datos3.egresos[i].cantidad)
                                        }
                                        self.totalfundas += parseInt(datos3.egresos[i].cantidad);
                                        self.fundas.push(despacho);
                                    }
                                }
                            }
                        });
                }
            })
        },
        methods: {
            editForm: function (index) {
                this.lote_enfunde.idlote = this.seccion[index].idlote;
                this.lote_enfunde.presente = this.seccion[index].presente;
                this.lote_enfunde.futuro = this.seccion[index].chapeo;
                this.lote_enfunde.chapeo = this.seccion[index].chapeo;
                this.statusForm = true;
            },
            saveForm: function (index) {
                this.seccion[index].idlote = this.lote_enfunde.idlote;
                this.seccion[index].presente = this.lote_enfunde.presente;
                this.seccion[index].futuro = this.lote_enfunde.futuro;
                this.seccion[index].chapeo = this.lote_enfunde.chapeo;

                this.lote_enfunde.idlote = 0;
                this.lote_enfunde.presente = 0;
                this.lote_enfunde.futuro = 0;
                this.lote_enfunde.futuro = 0;
                this.statusForm = false;
            },
            totalPresente: function () {
                let total = 0;
                for (var i in this.seccion) {
                    total += +this.seccion[i].presente;
                }

                return total;
            },
            totalFuturo: function () {
                let total = 0;
                for (var i in this.seccion) {
                    total += +this.seccion[i].futuro;
                }

                return total;
            },
            totalEnfunde: function () {
                return this.totalPresente() + this.totalFuturo();
            },
            statusEnfunde: function () {
                let saldo = +this.totalfundas - +this.totalEnfunde();
                return saldo > 0 ? true : false;
            }
        }
    }
</script>