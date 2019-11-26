<template>
    <div class="form-row mt-0">
        <div class="col-12 table-responsive">
            <table class="table table-hover" id="detalle-liquidacion">
                <thead>
                <tr>
                    <th scope="col">Descripcion</th>
                    <th scope="col">Ubicacion</th>
                    <th scope="col">Tipo de Caja</th>
                    <th scope="col" class="text-center">#Cajas</th>
                    <th scope="col" class="text-center">Tarifa</th>
                    <th scope="col" class="text-center">Bono Emp</th>
                    <th scope="col" class="text-center">V-Bono</th>
                    <th scope="col" class="text-center">Monto</th>
                    <th scope="col" class="text-center">Acción</th>
                </tr>
                </thead>
                <tbody class="table-sm">
                <template v-for="(liquidacion, index1) in liquidaciones">
                    <tr class="table-active">
                        <td colspan="1"><b>{{liquidacion[0]}}</b></td>
                        <td colspan="1"><b>{{liquidacion[1]}}</b></td>
                        <td colspan="7"><b>{{liquidacion[2]}}</b></td>
                    </tr>
                    <template v-for="(cajas, index) in liquidacion['Detalle']">
                        <tr>
                            <td>{{cajas[0]}}</td>
                            <td>{{cajas[1]}}</td>
                            <td>{{cajas[2]}}</td>
                            <td class="text-center"><b>{{cajas[4] = parseInt(cajas[4]) | formatNumber}}</b></td>
                            <td class="text-center">$ {{cajas[5] = parseFloat(cajas[5].split("$")[1]).toFixed(3)}}</td>
                            <td class="text-center">
                                {{cajas[7] ? '$ ' + parseFloat(cajas[7].tarifa.split("$")[1]).toFixed(3) : ''}}
                            </td>
                            <td class="text-center">
                                {{cajas[7] ? '$ ' + (+cajas[7].cantidad * parseFloat(cajas[7].tarifa.split("$")[1]).toFixed(3)) : ''}}
                            </td>
                            <td class="text-center">
                                <b>$ {{cajas[6] = (parseFloat(cajas[4] * cajas[5]).toFixed(2)) | formatNumber}}</b>
                            </td>
                            <td class="text-center">
                                <template v-if="cajas[0] != 'BONO DE EMPAQUE'">
                                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                                            v-on:click="formLiquidacion(index1,index)"
                                            data-target="#exampleModal">
                                        <i class="fas fa-external-link-alt"></i> Liquidar
                                    </button>
                                </template>
                            </td>
                        </tr>
                    </template>
                    <tr class="table-success table-sm">
                        <td colspan="7"><h5 class="mb-0"><b>TOTAL</b></h5></td>
                        <td colspan="1" class="text-center"><h5 class="mb-0">
                            <b>$ {{parseFloat(totalMonto(liquidacion)).toFixed(2) | formatNumber}}</b></h5></td>
                        <td></td>
                    </tr>
                </template>
                </tbody>
            </table>
        </div>
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Formulario de Liquidacion | Finca: <b>{{cabecera.finca}}</b>
                            - Documento: <b>{{cabecera.documento}}</b></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <formulario_liquidacion :liquidacion="liquidacion"></formulario_liquidacion>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    var numeral = require("numeral");

    import formulario_liquidacion from './prd_form_liquidacion.vue';
    import moment from "moment/moment";
    import SweetAlert from "sweetalert2/src/sweetalert2";
    import BootstrapVue from "bootstrap-vue";

    Vue.filter("formatNumber", function (value) {
        return numeral(value).format("0,0"); // displaying other groupings/separators is possible, look at the docs
    });

    Vue.use(BootstrapVue);
    const Swal = SweetAlert;

    export default{
        components: {
            formulario_liquidacion
        },
        props: ['liquidaciones', 'cabecera'],
        data(){
            return {
                fecha: '',
                detalle: [],
                liquidacion: {
                    semana: this.cabecera.semana,
                    hacienda: this.cabecera.finca,
                    nviaje: '',
                    descripcion: '',
                    ubicacion: '',
                    tipo_caja: '',
                    cantidad: 0,
                    tarifa: 0,
                    bono: false,
                    bono_emp: 0,
                    val_bono: 0,
                    monto: 0,
                    token: this.cabecera.token,
                    liquidacion_semanal: []
                }
            }
        },
        mounted(){
            var self = this;
            //console.log(this.liquidaciones);
        },
        methods: {
            totalMonto: function (values) {
                return values['Detalle'].reduce((acc, val) => {
                    return acc + parseFloat(val[6]);
                }, 0);
            },
            total: function (values) {
                return values.reduce
            },
            formLiquidacion: function (index, index2) {
                let liquidacion = this.liquidaciones[index]['Detalle'];
                this.liquidacion.nviaje = this.liquidaciones[index][0];
                this.liquidacion.descripcion = liquidacion[index2][0];
                this.liquidacion.ubicacion = liquidacion[index2][1];

                this.liquidacion.tipo_caja = liquidacion[index2][2];
                //Traer liquidacion de la semana correspondiente a este tipo de caja
                this.getCajasSemana(liquidacion[index2][2]);

                this.liquidacion.cantidad = liquidacion[index2][4];
                this.liquidacion.tarifa = liquidacion[index2][5];

                if (liquidacion[index2][7]) {
                    this.liquidacion.bono = true;
                    this.liquidacion.bono_emp = parseFloat(liquidacion[index2][7].tarifa.split("$")[1]).toFixed(3);
                    this.liquidacion.val_bono = +liquidacion[index2][7].cantidad * parseFloat(liquidacion[index2][7].tarifa.split("$")[1]).toFixed(3);
                } else {
                    this.liquidacion.bono = false;
                    this.liquidacion.bono_emp = 0;
                    this.liquidacion.val_bono = 0;
                }
                this.liquidacion.monto = liquidacion[index2][6];
            },
            getCajasSemana: function (tipo_caja) {
                var self = this;
                let json = {
                    semana: this.liquidacion.semana,
                    hacienda: this.liquidacion.hacienda == "150343" ? 1 : 2,
                    tipo_caja: tipo_caja
                };

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': this.liquidacion.token
                    }
                });
                $.ajax({
                    type: "POST",
                    url: '/sistema/produccion/liquidacion/cajas/semana',//this is only changes
                    data: {
                        json: JSON.stringify(json)
                    },
                    success: function (data) {
                        if (data.liquidacion_semana) {
                            self.liquidacion.liquidacion_semanal = data.liquidacion_semana;
                        }
                    }
                });
            }
        }
    }
</script>