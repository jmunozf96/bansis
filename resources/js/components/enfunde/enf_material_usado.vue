<template>
    <div class="container-fluid">
        <div class="alert alert-danger" role="alert" v-if="error.status" v-html="error.msj">
        </div>
        <table class="table">
            <thead>
            <tr>
                <th scope="col" class="text-center">idMaterial</th>
                <th scope="col" class="text-center">Descripcion</th>
                <th scope="col" class="text-center">Saldo</th>
                <th scope="col" class="text-center">Cantidad</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(material, index) of materiales">
                <th scope="row" class="text-center">{{material.idmaterial}}</th>
                <td>{{material.material.nombre}}</td>
                <td class="text-center">{{material.saldo}}</td>
                <td>
                    <input type="number" class="form-control text-center"
                           v-model="material.cantidad"
                           :id="'material-' + material.idmaterial"
                           @change="verificaSaldo(material.idmaterial, index)"/>
                </td>
            </tr>
            </tbody>
        </table>
        <div class="row">
            <div class="col-12">
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <b>TOTAL FUNDAS USADAS: </b>
                        <span class="badge badge-primary badge-pill" style="font-size: 13px">{{total_usado}}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <b>SALDO: </b>
                        <span class="badge badge-primary badge-pill" style="font-size: 13px">{{saldo}}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "enf_material_usado",
        props: {
            materiales: Array,
            datosenfunde: Object
        },
        data() {
            return {
                nw_materiales: [],
                error: {
                    status: false,
                    msj: ''
                }
            }
        },
        methods: {
            verificaSaldo(idmaterial, index) {
                let material = this.materiales[index];
                if (material.idmaterial === idmaterial) {
                    if (+material.saldo >= +material.cantidad) {
                        this.error.status = false;
                        this.error.msj = ''
                    } else {
                        material.cantidad = 0;
                        this.error.status = true;
                        this.error.msj = '<b>Error!</b> no se puede agregar una cantidad mayor al saldo.'
                    }
                }
            },
        },
        computed: {
            total_usado() {
                var despacho = 0;
                for (let material of this.materiales) {
                    despacho += +material.cantidad;
                }

                return despacho;
            },
            saldo() {
                var saldo = 0;
                for (let material of this.materiales) {
                    saldo += +material.saldo;
                }
                return saldo = saldo - this.total_usado;
            }
        }
    }
</script>

<style scoped>

</style>
