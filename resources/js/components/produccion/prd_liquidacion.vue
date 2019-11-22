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
                    <th scope="col" class="text-center">Monto</th>
                </tr>
                </thead>
                <tbody class="table-sm">
                <template v-for="(liquidacion, index) in liquidaciones">
                    <tr class="table-active">
                        <td colspan="1"><b>{{liquidacion[0]}}</b></td>
                        <td colspan="1"><b>{{liquidacion[1]}}</b></td>
                        <td colspan="4"><b>{{liquidacion[2]}}</b></td>
                    </tr>
                    <template v-for="(cajas, index) in liquidacion['Detalle']">
                        <tr>
                            <td>{{cajas[0]}}</td>
                            <td>{{cajas[1]}}</td>
                            <td>{{cajas[2]}}</td>
                            <td class="text-center"><b>{{cajas[4] = parseInt(cajas[4])}}</b></td>
                            <td class="text-center">$ {{cajas[5] = parseFloat(cajas[5].split("$")[1]).toFixed(3)}}</td>
                            <td class="text-center"><b>$ {{cajas[7] = parseFloat(cajas[4] * cajas[5]).toFixed(2)}}</b>
                            </td>
                        </tr>
                    </template>
                    <tr class="table-success table-sm">
                        <td colspan="5"><h5 class="mb-0"><b>TOTAL</b></h5></td>
                        <td colspan="1" class="text-center"><h5 class="mb-0"><b>$ {{parseFloat(totalMonto(liquidacion)).toFixed(2)}}</b></h5></td>
                    </tr>
                </template>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
    import moment from "moment/moment";
    import SweetAlert from "sweetalert2/src/sweetalert2";
    import BootstrapVue from "bootstrap-vue";

    Vue.use(BootstrapVue);
    const Swal = SweetAlert;

    export default{
        props: ['liquidaciones'],
        data(){
            return {
                fecha: '',
                detalle: []
            }
        },
        mounted(){
            var self = this;
            //console.log(this.liquidaciones);
        },
        methods: {
            totalMonto: function (values) {
                return values['Detalle'].reduce((acc, val) => {
                    return acc + parseFloat(val[7]);
                }, 0);
            },
            total: function(values){
                return values.reduce
            }
        }
    }
</script>