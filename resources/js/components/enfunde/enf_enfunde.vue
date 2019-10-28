<template>
    <div class="container-fluid">
        <div class="form-row mt-3 mb-0">
            <div class="form-group col-md-8">
                <select class="selectpicker show-tick form-control"
                        data-live-search="true" multiple data-max-options="1"
                        title="Seleccionar lotero a reportar enfunde..."
                        id="lotero">
                    <optgroup label="Loteros con saldo de fundas" data-max-options="2">
                        <option v-for="lotero in this.loteros" v-if="lotero.fundas"
                                :data-subtext="lotero.fundas ? ' - Tiene fundas despachadas' : ''"
                                :value="lotero.id">
                            {{lotero.empleado.nombre}}
                        </option>
                    </optgroup>
                    <optgroup label="Loteros pendientes despacho" data-max-options="2">
                        <option v-for="lotero in this.loteros" v-if="!lotero.fundas"
                                :value="lotero.id">
                            {{lotero.empleado.nombre}}
                        </option>
                    </optgroup>
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
                    <b>Saldo: {{saldoEnfunde()}}</b>, puede reportar enfunde
                </div>
                <div class="alert alert-danger" role="alert" v-else>
                    <b>Saldo: {{saldoEnfunde()}}</b>, no puede reportar enfunde
                </div>
                <p class="mb-1">Lotes asignados:</p>
                <table class="table table-bordered table-hover" id="enfunde-items">
                    <thead>
                    <tr class="text-center">
                        <th width="10%">Estado</th>
                        <th>Lote</th>
                        <th>Has.</th>
                        <th>%</th>
                        <th>Presente</th>
                        <th>Futuro</th>
                        <th>Desbunche</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(sec, index) in seccion" class="text-center">
                        <td width="10%">
                            <span v-if="status">
                                <span v-if="statusForm  && sec.seccion == lote_enfunde.seccion">
                                <button class="btn btn-primary btn-sm"
                                        v-on:click="saveForm(index)">
                                    <i class="fas fa-save"></i>
                                </button>
                            </span>
                            <span v-else>
                                <button class="btn btn-success btn-sm"
                                        v-on:click="editForm(index)">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </span>
                            </span>
                            <span class="badge badge-danger" v-else-if="fundas.length == 0">
                                 Sin despacho
                            </span>
                            <span class="badge badge-primary" v-else>
                                Reportado
                            </span>
                        </td>
                        <td width="10%">{{sec.lote}}</td>
                        <td width="10%">{{sec.has}}</td>
                        <td width="15%">
                            <b-progress :max="100">
                                <b-progress-bar :value="sec.porcentaje * 100"
                                                :label="`${(sec.porcentaje * 100).toFixed(2)}%`"></b-progress-bar>
                            </b-progress>
                        </td>
                        <td style="width: 15%">
                            <span v-if="statusForm && sec.seccion == lote_enfunde.seccion && presente">
                                <input class="form-control text-center cantidad-despacho" ref="presente"
                                       v-model="lote_enfunde.presente"
                                       type="number"/>
                            </span>
                            <span v-else>
                                {{sec.presente}}
                            </span>
                        </td>
                        <td style="width: 15%">
                            <span v-if="statusForm && sec.seccion == lote_enfunde.seccion && futuro">
                                <input class="form-control text-center cantidad-despacho" ref="futuro"
                                       v-model="lote_enfunde.futuro"
                                       type="number"/>
                            </span>
                            <span v-else>
                                {{sec.futuro}}
                            </span>
                        </td>
                        <td style="width: 15%">
                            <span v-if="statusForm && sec.seccion == lote_enfunde.seccion && futuro">
                                <input class="form-control text-center cantidad-despacho" ref="desbunche"
                                       v-model="lote_enfunde.desbunche"
                                       type="number"/>
                            </span>
                            <span v-else>
                                {{sec.desbunche}}
                            </span>
                        </td>
                    </tr>
                    <tr class="text-center table-sm" style="font-size: 18px">
                        <td width="10%">Total</td>
                        <td width="10%"></td>
                        <td width="10%"></td>
                        <td></td>
                        <td><b>{{totalPresente()}}</b></td>
                        <td><b>{{totalFuturo()}}</b></td>
                        <td><b>{{totalDesbunche()}}</b></td>
                    </tr>
                    </tbody>
                </table>
                <div class="form-row p-1 mb-2">
                    <input id="detalle-total" type="text" style="font-size: 22px"
                           class="form-control form-control-lg bg-white text-right"
                           :value="`Enfunde Total de la semana: ${totalEnfunde()} racimos enfundados`"
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
                            <th>Pre/Fut</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(funda, index) in fundas" class="text-center">
                            <td>{{funda.fecha}}</td>
                            <td>{{funda.cantidad}}</td>
                            <td>{{funda.status}}</td>
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
                            <th>Pre/Fut</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(reemplazo, index) in reemplazos" class="text-center">
                            <td>{{reemplazo.fecha}}</td>
                            <td>{{reemplazo.nombre}}</td>
                            <td>{{reemplazo.cantidad}}</td>
                            <td>{{reemplazo.status}}</td>
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
                desbunche: 0,
                fundas: [],
                reemplazos: [],
                totalfundas: 0,
                statusForm: false,
                lote_enfunde: {
                    seccion: 0,
                    idlote: 0,
                    presente: 0,
                    futuro: 0,
                    desbunche: 0
                },
                presente: 1,
                futuro: 0,
                status: 1,
                edicion: 0
            }
        },
        mounted(){
            var self = this;

            $('#lotero').on({
                change: function (e) {
                    e.preventDefault();
                    $('#btn-save').attr('disabled', false);
                    let id = $(this).val()[0];

                    self.totalfundas = 0;
                    self.seccion = [];
                    self.reemplazos = [];
                    self.fundas = [];
                    self.presente = 1;
                    self.futuro = 0;
                    self.status = 1;
                    self.edicion = 0;

                    if (id === undefined) {
                        alert('Seleccioone un lotero');
                        return;
                    }

                    self.idlotero = id;

                    axios.get(`/sistema/axios/enfunde/lotero/${id}/${self.semana}`)
                        .then(response => {
                            if (response.data) {

                                let datos4 = response.data[0].enfunde;

                                let presente = [];
                                let futuro = [];

                                let datos = response.data[0].seccion;
                                for (var i in datos) {
                                    var seccion = {
                                        seccion: datos[i].id,
                                        idlote: datos[i].idlote,
                                        lote: datos[i].lote.lote,
                                        has: (parseFloat(datos[i].has)).toFixed(2),
                                        hasTotal: (parseFloat(datos[i].lote.has)).toFixed(2),
                                        presente: 0,
                                        futuro: 0,
                                        desbunche: 0,
                                        porcentaje: (parseFloat(datos[i].has) / parseFloat(datos[i].lote.has)).toFixed(2)
                                    };


                                    if (datos4) {

                                        if (datos4.status == 0 && datos4.count == 2) {
                                            Swal.fire({
                                                position: 'center',
                                                type: 'error',
                                                title: 'Ya se reporto enfunde en la semana actual',
                                                showConfirmButton: false,
                                                timer: 1500
                                            })

                                            self.status = 0;
                                            $('#btn-save').attr('disabled', true);
                                        }

                                        for (var y in datos4.detalle) {
                                            seccion.iddetalle = datos4.detalle[y].id;
                                            if (datos4.detalle[y].presente == 1 && datos4.detalle[y].idseccion == seccion.seccion) {
                                                seccion.presente = datos4.detalle[y].cantidad;
                                                presente.push(true);
                                            }
                                            if (datos4.detalle[y].futuro == 1 && datos4.detalle[y].idseccion == seccion.seccion) {
                                                seccion.futuro = datos4.detalle[y].cantidad;
                                                seccion.desbunche = datos4.detalle[y].desbunchado;
                                                futuro.push(true);
                                            }
                                        }
                                    }

                                    self.seccion.push(seccion);

                                }

                                let datos3 = response.data[0].fundas;

                                if (datos3) {

                                    for (var i in datos3.egresos) {
                                        if (datos3.egresos[i].reemplazo == 0) {
                                            var despacho = {
                                                id: datos3.egresos[i].id,
                                                fecha: (datos3.egresos[i].fecha).toString("dd/MM/yyyy"),
                                                cantidad: parseInt(datos3.egresos[i].cantidad),
                                                status: datos3.egresos[i].presente == 1 ? 'Presente' : 'Futuro'
                                            };
                                            self.fundas.push(despacho);
                                        } else {
                                            var reemplazo = {
                                                id: datos3.egresos[i].id,
                                                fecha: (datos3.egresos[i].fecha).toString("dd/MM/yyyy"),
                                                nombre: datos3.egresos[i].nom_reemplazo.nombre,
                                                cantidad: parseInt(datos3.egresos[i].cantidad),
                                                status: datos3.egresos[i].presente == 1 ? 'Presente' : 'Futuro'
                                            };
                                            self.reemplazos.push(reemplazo);
                                        }

                                        self.totalfundas += parseInt(datos3.egresos[i].cantidad);

                                        if (datos3.egresos[i].futuro == 1 && presente.length > 0) {
                                            self.presente = 0;
                                            self.futuro = 1;
                                        }
                                    }

                                    let mensaje;
                                    if (self.presente) {
                                        mensaje = 'Lotero listo para reportar enfunde presente';
                                        if (presente.length == 0) {
                                            self.edicion = false;
                                        } else {
                                            self.edicion = true;
                                            mensaje = 'Edicion para enfunde presente activa';
                                        }
                                    } else {
                                        mensaje = 'Lotero listo para reportar enfunde futuro';
                                        if (futuro.length == 0) {
                                            self.edicion = false;
                                        } else {
                                            self.edicion = true;
                                            mensaje = 'Edicion para enfunde futuro activa';
                                        }
                                    }

                                    Swal.fire({
                                        position: 'center',
                                        type: 'info',
                                        title: mensaje,
                                        showConfirmButton: false,
                                        timer: 3000
                                    });
                                }

                                if (!datos3) {
                                    Swal.fire({
                                        position: 'center',
                                        type: 'error',
                                        title: 'No tiene saldo de despacho',
                                        showConfirmButton: false,
                                        timer: 1500
                                    });

                                    self.status = 0;
                                    $('#btn-save').attr('disabled', true);
                                } else {
                                    if (self.saldoEnfunde() < 0) {
                                        Swal.fire({
                                            position: 'center',
                                            type: 'error',
                                            title: 'Tiene una diferencia de saldo, corregir este error',
                                            showConfirmButton: false,
                                            timer: 3000
                                        });
                                    }
                                }
                            }
                        });
                }
            });

            $('#btn-save').on("click", function () {
                if (self.status) {
                    let enfunde = {
                        idhacienda: self.hacienda,
                        fecha: self.fecha,
                        semana: self.semana,
                        periodo: self.periodo,
                        lotero: self.idlotero,
                        total_pre: self.totalPresente(),
                        total_fut: self.totalFuturo(),
                        desbunchado: self.totalDesbunche(),
                        presente: self.presente,
                        futuro: self.futuro,
                        detalle_enfunde: self.seccion,
                        egresos: {
                            fundas: self.fundas,
                            reemplazos: self.reemplazos
                        },
                        edicion: self.edicion
                    };

                    axios.post('/sistema/enfunde/registro/save', enfunde)
                        .then(response => {
                            let resp = response.data;
                            Swal.fire({
                                position: 'top-end',
                                type: resp.status,
                                title: resp.message,
                                showConfirmButton: false,
                                timer: 1500
                            })
                            if (resp.status == 'success') {
                                //self.resetData();
                                $('#lotero').focus();
                            }
                        })
                        .catch(error => {
                            console.log(error.response)
                        });
                }
            })

        },
        methods: {
            editForm: function (index) {
                this.lote_enfunde.seccion = this.seccion[index].seccion;
                this.lote_enfunde.idlote = this.seccion[index].idlote;
                this.lote_enfunde.presente = this.seccion[index].presente;
                this.lote_enfunde.futuro = this.seccion[index].futuro;
                this.lote_enfunde.desbunche = this.seccion[index].desbunche;
                this.statusForm = true;
            },
            saveForm: function (index) {
                this.lote_enfunde.seccion = this.seccion[index].seccion;
                this.seccion[index].idlote = this.lote_enfunde.idlote;
                this.seccion[index].presente = this.lote_enfunde.presente;
                this.seccion[index].futuro = this.lote_enfunde.futuro;
                this.seccion[index].desbunche = this.lote_enfunde.desbunche;

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
            totalDesbunche: function () {
                let total = 0;
                for (var i in this.seccion) {
                    total += +this.seccion[i].desbunche;
                }

                return total;
            },
            totalEnfunde: function () {
                return this.totalPresente() + this.totalFuturo();
            },
            saldoEnfunde: function () {
                return +this.totalfundas - +this.totalEnfunde();
                totalEnfunde;
            },
            statusEnfunde: function () {
                return this.saldoEnfunde() > 0 ? true : false;
            },
            resetData: function () {
                this.idlotero = 0;
                this.seccion = [];
                this.total_pre = 0;
                this.total_fut = 0;
                this.desbunche = 0;
                this.fundas = [];
                this.reemplazos = [];
                this.totalfundas = 0;
                this.statusForm = false;
                this.lote_enfunde = {
                    seccion: 0,
                    idlote: 0,
                    presente: 0,
                    futuro: 0,
                    desbunche: 0
                };
                this.presente = 1;
                this.futuro = 0;
                this.status = 1;
                this.edicion = 0;
            }
        }
    }
</script>