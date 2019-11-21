<template>
    <div class="form-row mt-0">
        <div class="col-12 table-responsive">
            <table class="table table-hover" id="detalle-liquidacion">
                <thead>
                <tr>
                    <th scope="col">Descripcion</th>
                    <th scope="col">Ubicacion</th>
                    <th scope="col">Tipo de Caja</th>
                    <th scope="col">#Cajas</th>
                    <th scope="col">Tarifa</th>
                    <th scope="col">Monto</th>
                </tr>
                </thead>
                <tbody class="table-sm">
                <template v-for="(liquidacion, index) in liquidaciones">
                    <tr class="table-active">
                        <td colspan="4">{{liquidacion[0]}}</td>
                        <td>{{liquidacion[1]}}</td>
                        <td>{{liquidacion[2]}}</td>
                    </tr>
                    <template v-for="(cajas, index) in liquidacion['Detalle']">
                        <tr>
                            <td>{{cajas[0]}}</td>
                            <td>{{cajas[1]}}</td>
                            <td>{{cajas[2]}}</td>
                            <td>{{cajas[4]}}</td>
                            <td>{{cajas[5]}}</td>
                            <td>{{cajas[7]}}</td>
                        </tr>
                    </template>
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
            console.log(this.liquidaciones);
        }
    }
</script>