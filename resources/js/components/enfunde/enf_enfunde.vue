<template>
    <div class="container-fluid">
        <div class="form-row mt-3 mb-0">
            <div class="form-group col-md-8">
                <select
                    class="selectpicker show-tick form-control form-control-lg"
                    data-style="btn-outline-dark"
                    data-live-search="true"
                    multiple
                    data-max-options="1"
                    title="Seleccionar lotero a reportar enfunde..."
                    id="lotero"
                    style="font-size: 20px"
                >
                    <optgroup label="Loteros con saldo de fundas" data-max-options="2">
                        <option style="font-size: 18px"
                                v-for="lotero in this.loteros"
                                v-if="lotero.fundas"
                                :data-subtext="lotero.fundas ? ' - Tiene fundas despachadas' : ''"
                                :value="lotero.id"
                        >{{lotero.nombres}}
                        </option>
                    </optgroup>
                    <optgroup label="Loteros pendientes despacho" data-max-options="2">
                        <option style="font-size: 18px"
                                v-for="lotero in this.loteros"
                                v-if="!lotero.fundas"
                                :value="lotero.id"
                        >{{lotero.nombres}}
                        </option>
                    </optgroup>
                </select>
                <small>Buscar lotero por nombre o apellido</small>
            </div>
            <div class="form-group col-md-4 mb-">
                <input
                    type="text"
                    class="form-control form-control-lg bg-dark text-right"
                    placeholder="Detalle"
                    :value="'Fundas Total: ' + totalfundas"
                    id="detalle"
                    style="color: #41DB00; font-size: 25px"
                    oninput="this.value = this.value.toUpperCase()"
                    disabled
                />
                <small>Total de fundas despachadas en la semana</small>
            </div>
        </div>
        <hr class="mt-0"/>

        <div class="modal fade" id="id-material-usado" tabindex="-1" role="dialog"
             aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="id-titulo">Fundas utilizadas</h5>
                    </div>
                    <div class="modal-body">
                        <enf_material_usado :materiales="saldo_semana"
                                            :presente="presente != 0" :futuro="futuro != 0"
                                            :datosenfunde="lote_enfunde"></enf_material_usado>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="btn-save-items">Guardar cambios</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-row mt-0">
            <div class="col-md-6">
                <div class="col-12 table-responsive">
                    <div class="alert alert-success" role="alert" v-if="statusEnfunde()">
                        <b>Saldo: {{saldoMaterialesSemana}}</b>, puede reportar enfunde
                    </div>
                    <div class="alert alert-danger" role="alert" v-else>
                        <b>Saldo: {{saldoMaterialesSemana}}</b>, no puede reportar enfunde
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
                                  <button class="btn btn-primary" v-on:click="saveForm(index)">
                                    <i class="fas fa-save"></i>
                                  </button>
                                </span>
                                <span v-else>
                                  <button class="btn btn-success" v-on:click="editForm(index)">
                                    <i class="fas fa-edit"></i>
                                  </button>
                                </span>
                              </span>
                                <span class="badge badge-danger" v-else-if="fundas.length == 0">Sin despacho</span>
                                <span class="badge badge-primary" v-else>Reportado</span>
                            </td>
                            <td width="10%">{{sec.lote}}</td>
                            <td width="10%">{{sec.has}}</td>
                            <td width="15%">
                                <b-progress :max="100">
                                    <b-progress-bar
                                        :value="(sec.porcentaje * 100)"
                                        :label="`${(sec.porcentaje * 100).toFixed(0)}%`"
                                    ></b-progress-bar>
                                </b-progress>
                            </td>
                            <td style="width: 15%">
                                <template v-if="statusForm && sec.seccion == lote_enfunde.seccion && presente">
                                    <input
                                        class="form-control text-center cantidad-despacho bg-white"
                                        ref="presente" name="presente" style="cursor: pointer"
                                        v-model="lote_enfunde.presente.cantidad"
                                        v-on:keyup.enter="saveForm(index)"
                                        type="number"
                                    />
                                </template>
                                <span v-else>{{sec.presente.cantidad}}</span>
                            </td>
                            <td style="width: 15%">
                                <template v-if="statusForm && sec.seccion == lote_enfunde.seccion && futuro">
                                    <input
                                        class="form-control text-center cantidad-despacho bg-white"
                                        ref="futuro" name="futuro" style="cursor: pointer"
                                        v-model="lote_enfunde.futuro.cantidad"
                                        v-on:keyup.enter="saveForm(index)"
                                        type="number"
                                    />
                                </template>
                                <span v-else>{{sec.futuro.cantidad}}</span>
                            </td>
                            <td style="width: 15%">
                                <template v-if="statusForm && sec.seccion == lote_enfunde.seccion && futuro">
                                    <input
                                        class="form-control text-center cantidad-despacho bg-white"
                                        ref="desbunche" name="desbunche" style="cursor: not-allowed"
                                        v-model="lote_enfunde.desbunche"
                                        v-on:keyup.enter="saveForm(index)"
                                        type="number"
                                    />
                                </template>
                                <span v-else>{{sec.desbunche}}</span>
                            </td>
                        </tr>
                        <tr class="text-center table-sm" style="font-size: 18px">
                            <td width="10%">Total</td>
                            <td width="10%"></td>
                            <td width="10%"></td>
                            <td></td>
                            <td>
                                <b>{{totalPresente()}}</b>
                            </td>
                            <td>
                                <b>{{totalFuturo()}}</b>
                            </td>
                            <td>
                                <b>{{totalDesbunche()}}</b>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-row p-1 mb-2">
                    <input
                        id="detalle-total"
                        type="text"
                        style="font-size: 22px"
                        class="form-control form-control-lg bg-white text-right"
                        :value="`Enfunde Total de la semana: ${totalEnfunde()} racimos enfundados`"
                        disabled
                    />
                </div>
                <div class="col-12" id="data_canvas">
                    <canvas id="dato_enfunde" width="100"></canvas>
                </div>
                <div class="form-row p-1">
                    <!--<p v-if="saldo_semana.length > 0  && saldo_semana.semana" style="font-size: 18px">El lotero tiene un saldo
                        pendiente de <b>{{saldo_semana.semana.saldo}} fundas.</b></p>
                    <p v-else-if="saldo_semana && saldo_semana.presente && saldo_semana.futuro" style="font-size: 18px">
                        El lotero tiene un saldo pendiente en la cinta presente de <b>{{saldo_semana.presente.saldo}}
                        fundas</b> y en la cinta futuro tiene un saldo pendiente de <b>{{saldo_semana.futuro.saldo}}
                        fundas.</b></p>-->
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12" v-if="fundas.length > 0">
                <p class="mb-1">Fundas despachadas:</p>
                <table class="table table-bordered table-hover" id="despacho-items">
                    <thead>
                    <tr class="text-center">
                        <th>Fecha</th>
                        <th>Material</th>
                        <th>Total Desp.</th>
                        <th>Pre/Fut</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="funda in fundas" class="text-center">
                        <td>{{funda.fecha}}</td>
                        <td>{{funda.material}}</td>
                        <td>{{funda.cantidad}}</td>
                        <td>{{funda.status}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-12" v-if="reemplazos.length > 0">
                <p class="mb-1">Lista de reemplazos y cantidad de fundas despachadas:</p>
                <table class="table table-bordered table-hover" id="reemplazo-items">
                    <thead>
                    <tr class="text-center">
                        <th>Fecha</th>
                        <th>Reemplazo</th>
                        <th>Material</th>
                        <th>Total Desp.</th>
                        <th>Pre/Fut</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(reemplazo) in reemplazos" class="text-center">
                        <td>{{reemplazo.fecha}}</td>
                        <td>{{reemplazo.nombre}}</td>
                        <td>{{reemplazo.material}}</td>
                        <td>{{reemplazo.cantidad}}</td>
                        <td>{{reemplazo.status}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
    import "bootstrap-daterangepicker/daterangepicker.css";
    import moment from "moment/moment";
    import SweetAlert from "sweetalert2/src/sweetalert2";
    import BootstrapVue from "bootstrap-vue";
    import enf_material_usado from "./enf_material_usado"

    Vue.use(BootstrapVue);

    const Swal = SweetAlert;

    export default {
        props: ["loteros"],
        components: {
            enf_material_usado
        },
        data() {
            return {
                fecha: $("#fecha").val(),
                hacienda: $("#id-hacienda").val(),
                semana: $("#semana").val(),
                periodo: $("#periodo").val(),
                idlotero: 0,
                seccion: [],
                total_pre: 0,
                total_fut: 0,
                saldo_semana: [],
                desbunche: 0,
                fundas: [],
                reemplazos: [],
                totalfundas: 0,
                statusForm: false,
                lote_enfunde: {
                    index: 0,
                    seccion: 0,
                    idlote: 0,
                    lote: '',
                    presente: {
                        cantidad: 0,
                        materiales: [],
                        status: false
                    },
                    futuro: {
                        cantidad: 0,
                        materiales: [],
                        status: false
                    },
                    desbunche: 0
                },
                presente: 1,
                futuro: 0,
                status: 1,
                edicion: 0,
                item_used: []
            };
        },

        mounted() {
            this.links();
            var self = this;

            $("#btn-save").attr("disabled", true);

            $("#lotero").on({
                change: function (e) {
                    e.preventDefault();

                    $("#btn-save").attr("disabled", false);
                    let id = $(this).val()[0];
                    self.getLotero(id);
                }
            });

            $('#btn-nuevo').on({
                click: function (e) {
                    self.resetData();
                    $('#lotero').val("");
                    $("#lotero").attr("disabled", false);
                    $("#lotero").selectpicker("refresh");
                    $("#lotero").focus();
                }
            });

            $("#btn-save").on("click", function () {
                if (self.status) {
                    $('#btn-save').attr('disabled', true);
                    if (+self.totalEnfunde() > 0) {
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

                        axios.post("/sistema/enfunde/registro/save", {json: JSON.stringify(enfunde)})
                            .then(response => {
                                let resp = response.data;
                                Swal.fire({
                                    position: "top-end",
                                    type: resp.status,
                                    title: resp.message,
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                                if (resp.status == "success") {
                                    self.resetData();
                                    $('#lotero').val("");
                                    $("#lotero").selectpicker("refresh");
                                    $("#lotero").focus();
                                    $('#btn-save').attr('disabled', false);
                                }
                            })
                            .catch(error => {
                                console.log(error.response);
                            });
                    } else {
                        Swal.fire({
                            position: "top-end",
                            type: 'info',
                            title: 'Debe registrar valores de enfunde',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        $('#lotero').val("");
                        $("#lotero").selectpicker("refresh");
                        $("#lotero").focus();
                        $('#btn-save').attr('disabled', false);
                    }
                }
            });

            $("#btn-save-items").on('click', function () {
                var total = 0;
                self.item_used = [];

                for (let lotero_fundas of self.saldo_semana) {

                    let materiales_usados = {
                        idmaterial: lotero_fundas.idmaterial,
                        cantidad: lotero_fundas.cantidad,
                        presente: self.presente,
                        futuro: self.futuro
                    };

                    total += +lotero_fundas.cantidad;

                    //lotero_fundas.cant_ocupada = +lotero_fundas.cant_ocupada + +lotero_fundas.cantidad;
                    lotero_fundas.cantidad = 0;

                    /*if (+materiales_usados.cantidad > 0) {
                        self.item_used.push(materiales_usados);
                    }*/

                    self.item_used.push(materiales_usados);

                    //lotero_fundas.saldo_backup = lotero_fundas.saldo;
                }

                if (self.presente == 1) {
                    self.lote_enfunde.presente.cantidad = total;
                    self.lote_enfunde.presente.materiales = self.item_used;
                } else {
                    self.lote_enfunde.futuro.cantidad = total;
                    self.lote_enfunde.futuro.materiales = self.item_used;
                }

                self.saveForm(self.lote_enfunde.index);
                $('#id-material-usado').modal('hide');
            });
        },
        updated: function () {
            let self = this;
            this.$nextTick(function () {
                if (this.statusForm) {
                    this.$nextTick(() => {
                        if (this.$refs.presente) {
                            this.$refs.presente[0].focus();
                            $(this.$refs.presente[0]).attr('readonly', true);
                            $(this.$refs.presente[0]).click(function () {
                                $('#id-material-usado').modal({backdrop: 'static', keyboard: false});
                            })
                        }

                    });
                    this.$nextTick(() => {
                        if (this.$refs.futuro) {
                            this.$refs.futuro[0].focus();
                            $(this.$refs.futuro[0]).attr('readonly', true);
                            $(this.$refs.desbunche[0]).attr('readonly', true);
                            $(this.$refs.futuro[0]).click(function () {
                                $('#id-material-usado').modal({backdrop: 'static', keyboard: false});
                            })
                        }
                    });
                }
            });

            this.graficarLotes();
        },
        methods: {
            getLotero: function (id) {
                let self = this;
                self.totalfundas = 0;
                self.seccion = [];
                self.reemplazos = [];
                self.fundas = [];
                self.presente = 1;
                self.futuro = 0;
                self.status = 1;
                self.edicion = 0;

                if (id === undefined || id == 0 || id == '') {
                    alert("Seleccioone un lotero");
                    return;
                }

                self.idlotero = id;

                axios.get(`/sistema/axios/enfunde/lotero/${id}/${self.semana}`)
                    .then(response => {
                        console.log(response.data);
                        if (response.data) {
                            let datos4 = response.data[0].enfunde;
                            self.saldo_semana = response.data[1].materiales;
                            let presente = [];
                            let futuro = [];

                            let datos = response.data[0].seccion;
                            for (var i in datos) {
                                var seccion = {
                                    seccion: datos[i].id,
                                    idlote: datos[i].idlote,
                                    lote: datos[i].lote.lote,
                                    has: parseFloat(datos[i].has).toFixed(2),
                                    hasTotal: parseFloat(datos[i].lote.has).toFixed(2),
                                    presente: {
                                        cantidad: 0,
                                        materiales: []
                                    },
                                    futuro: {
                                        cantidad: 0,
                                        materiales: []
                                    },
                                    desbunche: 0,
                                    porcentaje: (parseFloat(datos[i].has) / parseFloat(datos[i].lote.has)).toFixed(2)
                                };

                                if (datos4) {
                                    if (datos4.status == 0 && datos4.count == 2) {
                                        Swal.fire({
                                            position: "center",
                                            type: "error",
                                            title: "Ya se reporto enfunde en la semana actual",
                                            showConfirmButton: false,
                                            timer: 1500
                                        });

                                        self.status = 0;
                                        $("#btn-save").attr("disabled", true);
                                    }

                                    //Ojo con este proceso
                                    for (var y in datos4.detalle) {
                                        if (datos4.detalle[y].idseccion == seccion.seccion) {
                                            seccion.iddet = datos4.detalle[y].id;
                                            seccion.presente.cantidad += +datos4.detalle[y].cant_presente;
                                            seccion.futuro.cantidad += +datos4.detalle[y].cant_futuro;
                                            seccion.desbunche += +datos4.detalle[y].desbunchado;

                                            var material_presente = {
                                                idmaterial: datos4.detalle[y].id_material,
                                                cantidad: datos4.detalle[y].cant_presente,
                                                presente: true,
                                                futuro: false
                                            };

                                            if (!self.item_existe(seccion.presente.materiales, material_presente)) {
                                                seccion.presente.materiales.push(material_presente);
                                                presente.push(true);
                                            } else {
                                                self.edit_item_existe(seccion.presente.materiales, material_presente,)
                                            }


                                            if (datos4.detalle[y].cant_futuro != null) {
                                                var material_futuro = {
                                                    idmaterial: datos4.detalle[y].id_material,
                                                    cantidad: datos4.detalle[y].cant_futuro,
                                                    futuro: true,
                                                    presente: false
                                                };

                                                if (!self.item_existe(seccion.futuro.materiales, material_futuro)) {
                                                    seccion.futuro.materiales.push(material_futuro);
                                                    futuro.push(true);
                                                } else {
                                                    self.edit_item_existe(seccion.futuro.materiales, material_futuro,)
                                                }
                                            }
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
                                            fecha: datos3.egresos[i].fecha.toString("dd/MM/yyyy"),
                                            cantidad: parseInt(datos3.egresos[i].cantidad),
                                            material: datos3.egresos[i].get_material.nombre,
                                            status: datos3.egresos[i].presente == 1 ? "Presente" : "Futuro"
                                        };
                                        self.fundas.push(despacho);
                                    } else {
                                        var reemplazo = {
                                            id: datos3.egresos[i].id,
                                            fecha: datos3.egresos[i].fecha.toString("dd/MM/yyyy"),
                                            nombre: datos3.egresos[i].nom_reemplazo.nombre,
                                            cantidad: parseInt(datos3.egresos[i].cantidad),
                                            material: datos3.egresos[i].get_material.nombre,
                                            status: datos3.egresos[i].presente == 1 ? "Presente" : "Futuro"
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
                                    mensaje = "Lotero listo para reportar enfunde presente";
                                    if (presente.length == 0) {
                                        self.edicion = false;
                                    } else {
                                        self.edicion = true;
                                        mensaje = "Edicion para enfunde presente activa";
                                    }
                                } else {
                                    mensaje = "Lotero listo para reportar enfunde futuro";
                                    if (futuro.length == 0) {
                                        self.edicion = false;
                                    } else {
                                        self.edicion = true;
                                        mensaje = "Edicion para enfunde futuro activa";
                                    }
                                }

                                Swal.fire({
                                    position: "center",
                                    type: "info",
                                    title: mensaje,
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                            }

                            if (!datos3) {
                                Swal.fire({
                                    position: "center",
                                    type: "error",
                                    title: "No tiene saldo de despacho",
                                    showConfirmButton: false,
                                    timer: 1500
                                });

                                self.status = 0;
                                $("#btn-save").attr("disabled", true);
                            } else {
                                if (self.saldoEnfunde() < 0) {
                                    Swal.fire({
                                        position: "center",
                                        type: "error",
                                        title: "Tiene una diferencia de saldo, corregir este error",
                                        showConfirmButton: false,
                                        timer: 3000
                                    });
                                }
                                $("#btn-save").attr("disabled", false);
                            }

                            self.graficarLotes();
                        }
                    });
            },
            links: function () {
                let uri = window.location.search.substring(1);
                let params = new URLSearchParams(uri);
                let lotero = params.get("lotero");
                let semana = params.get("semana");
                if (lotero != null && semana != null) {
                    $('#lotero').val(lotero);
                    $("#lotero").selectpicker("refresh");
                    $("#lotero").attr("disabled", true);
                    $("#btn-save").attr("disabled", false);
                    this.semana = semana;
                    this.getLotero(lotero);

                    $('#btn-nuevo').attr('disabled', true);
                }
            },
            item_existe(array, item_duda) {
                for (var item of array) {
                    if (item.idmaterial == item_duda.idmaterial) {
                        return true;
                    }
                }

                return false;
            },
            edit_item_existe(array, item_duda) {
                for (var item of array) {
                    if (item.idmaterial == item_duda.idmaterial) {
                        item.cantidad += +item_duda.cantidad;
                    }
                }
                return true;
            },
            editForm: function (index, b = false) {
                /*let tabla = document.getElementById('enfunde-items');
                for (var i = 0; i < tabla.rows.length; i++) {
                    tabla.rows[i].classList.remove("bg-success");
                    tabla.rows[i].classList.remove("text-white");
                }
                tabla.rows[index + 1].classList.add("bg-success");
                tabla.rows[index + 1].classList.add("text-white");*/

                this.lote_enfunde.index = index;
                this.lote_enfunde.seccion = this.seccion[index].seccion;
                this.lote_enfunde.idlote = this.seccion[index].idlote;
                this.lote_enfunde.presente.cantidad = this.seccion[index].presente.cantidad;
                this.lote_enfunde.presente.materiales = this.seccion[index].presente.materiales;
                this.lote_enfunde.futuro.cantidad = this.seccion[index].futuro.cantidad;
                this.lote_enfunde.futuro.materiales = this.seccion[index].futuro.materiales;
                this.lote_enfunde.desbunche = this.seccion[index].desbunche;
                this.lote_enfunde.presente.status = this.presente;
                this.lote_enfunde.futuro.status = this.futuro;
                this.lote_enfunde.lote = this.seccion[index].lote;

                var array = [];


                if (this.presente == 1) {
                    array = this.lote_enfunde.presente.materiales;
                } else {
                    array = this.lote_enfunde.futuro.materiales;
                }


                if (array.length > 0) {
                    for (let saldos of array) {
                        for (let saldo_sem of this.saldo_semana) {
                            if (saldos.idmaterial === saldo_sem.idmaterial) {
                                saldo_sem.cantidad = 0;
                                saldo_sem.cantidad += +saldos.cantidad;
                            }
                        }
                    }
                } else {
                    for (let saldo_sem of this.saldo_semana) {
                        saldo_sem.cantidad = 0;
                    }
                }


                this.statusForm = true;
            },
            saveForm: function (index) {
                /*let tabla = document.getElementById('enfunde-items');
                tabla.rows[index + 1].classList.remove("bg-success");
                tabla.rows[index + 1].classList.remove("text-white");*/

                this.lote_enfunde.seccion = this.seccion[index].seccion;
                this.seccion[index].idlote = this.lote_enfunde.idlote;
                this.seccion[index].presente.cantidad = this.lote_enfunde.presente.cantidad;
                this.seccion[index].presente.materiales = this.lote_enfunde.presente.materiales;
                this.seccion[index].futuro.cantidad = this.lote_enfunde.futuro.cantidad;
                this.seccion[index].futuro.materiales = this.lote_enfunde.futuro.materiales;
                this.seccion[index].desbunche = this.lote_enfunde.desbunche;

                this.lote_enfunde.idlote = 0;
                this.lote_enfunde.presente.cantidad = 0;
                this.lote_enfunde.presente.materiales = [];
                this.lote_enfunde.futuro.cantidad = 0;
                this.lote_enfunde.futuro.materiales = [];
                this.lote_enfunde.desbunche = 0;
                this.statusForm = false;
                this.lote_enfunde.material = [];
                this.lote_enfunde.lote = '';
            },
            totalPresente: function () {
                let total = 0;
                for (var i in this.seccion) {
                    total += +this.seccion[i].presente.cantidad;
                }

                return total;
            },
            totalFuturo: function () {
                let total = 0;
                for (var i in this.seccion) {
                    total += +this.seccion[i].futuro.cantidad;
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
            saldoEnfunde: function (actualiza_saldo = 0, edicion = false, saldo_edicion = 0) {
                let saldos = this.saldoMaterialesSemana;
                if (saldos) {
                    if (saldos.semana) {
                        if (edicion) {
                            saldos.semana.saldo = parseInt(saldos.semana.saldo) + +saldo_edicion;
                        }
                        return saldos.semana.saldo = saldos.semana.saldo - +actualiza_saldo;
                    } else {
                        if (saldos.presente) {
                            if (saldos.futuro) {
                                if (this.presente) {
                                    if (edicion) {
                                        saldos.presente.saldo = parseInt(saldos.presente.saldo) + +saldo_edicion;
                                    }
                                    return saldos.presente.saldo = saldos.presente.saldo - +actualiza_saldo;
                                } else {
                                    if (edicion) {
                                        saldos.futuro.saldo = parseInt(saldos.futuro.saldo) + +saldo_edicion;
                                    }
                                    return saldos.futuro.saldo = saldos.futuro.saldo - +actualiza_saldo;
                                }
                            }
                        }
                    }
                }

                return +0;
            },
            statusEnfunde: function () {
                return this.saldoEnfunde() > 0 ? true : false;
            },
            graficarLotes: function () {
                var labels = [];

                var Presente = {
                    label: ['Enfunde Presente (Rac/Has.)'],
                    data: [],
                    backgroundColor: [],
                    borderColor: [],
                    showLine: true,
                    fill: false,
                    borderWidth: 1
                };

                var Futuro = {
                    label: ['Enfunde Futuro (Rac/Has.)'],
                    data: [],
                    backgroundColor: [],
                    borderColor: [],
                    showLine: true,
                    fill: false,
                    borderWidth: 1
                };

                for (var x in this.seccion) {
                    Presente.data.push(parseFloat(+this.seccion[x].presente.cantidad / +this.seccion[x].has).toFixed(0));
                    Presente.backgroundColor.push("rgba(255, 99, 132, 0.2)");
                    Presente.borderColor.push("rgba(255, 99, 132, 1)");

                    Futuro.data.push(parseFloat(+this.seccion[x].futuro.cantidad / +this.seccion[x].has).toFixed(0));
                    Futuro.backgroundColor.push("rgba(54, 162, 235, 0.2)");
                    Futuro.borderColor.push("rgba(54, 162, 235, 1)");
                    labels.push(this.seccion[x].lote);
                }

                document.getElementById("data_canvas").innerHTML = "";
                document.getElementById("data_canvas").innerHTML = ' <canvas id="dato_enfunde" width="100"></canvas>';

                var ctx = document.getElementById("dato_enfunde");

                var myChart = new Chart(ctx, {
                    type: "bar",
                    data: {
                        labels: labels,
                        datasets: [Presente, Futuro]
                    },
                    options: {
                        scales: {
                            yAxes: [
                                {
                                    ticks: {
                                        beginAtZero: true
                                    }
                                }
                            ]
                        }
                    }
                });

            },
            resetData: function () {
                this.idlotero = 0;
                this.seccion = [];
                this.total_pre = 0;
                this.total_fut = 0;
                this.desbunche = 0;
                this.fundas = [];
                this.saldo_semana = [];
                this.reemplazos = [];
                this.totalfundas = 0;
                this.statusForm = false;
                this.lote_enfunde = {
                    index: 0,
                    seccion: 0,
                    idlote: 0,
                    lote: '',
                    presente: {
                        cantidad: 0,
                        materiales: [],
                        status: false
                    },
                    futuro: {
                        cantidad: 0,
                        materiales: [],
                        status: false
                    },
                    desbunche: 0
                };
                this.presente = 1;
                this.futuro = 0;
                this.status = 1;
                this.edicion = 0;
            }
        },
        computed: {
            saldoMaterialesSemana: function () {
                let saldo = 0;
                if (this.saldo_semana.length > 0) {
                    for (var materiales of this.saldo_semana) {
                        saldo += +materiales.saldo;
                    }
                }
                return saldo;
            },
        }
    };
</script>
