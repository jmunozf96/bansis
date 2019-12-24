<template>
    <div class="container-fluid">
        <div class="alert alert-danger" role="alert" v-if="error.status" v-html="error.msj">
        </div>
        <table class="table">
            <thead>
            <tr>
                <th scope="col" class="text-center">Accion</th>
                <th scope="col" class="text-center">idMaterial</th>
                <th scope="col" class="text-center">Descripcion</th>
                <th scope="col" class="text-center">Saldo</th>
                <th scope="col" class="text-center">Cantidad</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(material, index) of materiales" class="">
                <th class="text-center">
                    <template v-if="data_material.edit && +data_material.codigo === +material.idmaterial">
                        <button class="btn btn-success" @click="saveForm(index)">
                            <i class="fas fa-save"></i></button>
                    </template>
                    <template v-else>
                        <button class="btn btn-primary" @click="editForm(index)">
                            <i class="fas fa-plus"></i>
                        </button>
                    </template>
                </th>
                <th scope="row" class="text-center">{{material.idmaterial}}</th>
                <td>{{material.material.nombre}}</td>
                <td class="text-center">{{material.saldo}}</td>
                <td class="text-center" style="width: 20%">
                    <template v-if="data_material.edit && +data_material.codigo === +material.idmaterial">
                        <input type="number" class="form-control text-center"
                               v-model="data_material.cantidad"
                               :id="'material-' + material.idmaterial"
                               @change="saveForm(index)"/>
                    </template>
                    <template v-else>
                        {{material.cantidad}}
                    </template>
                </td>
            </tr>
            </tbody>
        </table>
        <template v-if="futuro">
            <hr>
            <div class="row">
                <div class="col-12">
                    <label for="racimosDesbunchados">Racimos Desbunchados</label>
                    <input type="number" id="racimosDesbunchados" class="form-control" v-model="desbunchados"
                           @change="datosenfunde.desbunche = desbunchados"
                           aria-describedby="racimosDesbunchados" value="0" min="0" max="50">
                    <small id="racimosDesbunchados-det" class="form-text text-muted">
                        <b v-if="+datosenfunde.desbunche > 0">Lotero tiene registrado {{datosenfunde.desbunche}} racimos
                            desbunchados.</b>
                        En caso de haber desbunchado racimos en el lote {{datosenfunde.lote}}, detallar la cantidad en
                        este componente, caso contrario puede dejarlo como está.
                    </small>
                </div>
            </div>
            <hr>
        </template>
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
            datosenfunde: Object,
            presente: Boolean,
            futuro: Boolean
        },
        data() {
            return {
                materiales_usados: [],
                data_material: {
                    codigo: 0,
                    cantidad: 0,
                    edit: false,
                },
                error: {
                    status: false,
                    msj: ''
                },
                desbunchados: 0
            }
        },
        methods: {
            verificaSaldo(idmaterial, index) {
                let material = this.materiales[index];
                if (material.idmaterial === idmaterial) {
                    if (+material.saldo >= +material.cantidad) {
                        this.error.status = false;
                        this.error.msj = ''
                        return true;
                    } else {
                        material.cantidad = 0;
                        this.error.status = true;
                        this.error.msj = '<b>Error!</b> no se puede agregar una cantidad mayor al saldo.'
                        return false;
                    }
                }
            },
            actualizar_cantidad(index, cantidad) {
                let material = this.materiales[index];
                material.cant_ocupada = +material.cant_ocupada + +cantidad;
            },
            editForm(index) {
                var material = this.materiales[index];
                this.data_material.edit = true;
                this.data_material.codigo = material.idmaterial;
                this.data_material.cantidad = +material.cantidad;
                material.saldo = +material.saldo + +material.cantidad;

                /*if (+this.acumulaCantidad(material) > 0) {
                    material.cant_ocupada = this.acumulaCantidad(material);
                }*/
            },
            saveForm(index) {
                let material = this.materiales[index];

                //Preguntar si el saldo es mayor a la cantidad ingresada
                if (+material.saldo >= this.data_material.cantidad) {

                    //Detalle del metodo dentro de la funcion
                    this.cantidadOcupada(this.data_material.cantidad);
                    material.cantidad = this.data_material.cantidad;

                    this.error.status = false;
                    this.error.msj = '';

                    this.data_material.edit = false;
                    this.data_material.codigo = 0;
                    this.data_material.cantidad = 0;
                    this.data_material.desbunchados = 0;
                    material.saldo = (+material.saldo_backup - +material.cant_ocupada);
                } else {
                    this.data_material.cantidad = material.cantidad;
                    this.error.status = true;
                    this.error.msj = '<b>Error!</b> no se puede agregar una cantidad mayor al saldo.'
                }
            },
            acumulaCantidad(material) {
                var total = 0;
                var array = [];

                if (this.datosenfunde.presente.status)
                    array = this.datosenfunde.presente.materiales;
                else
                    array = this.datosenfunde.futuro.materiales;

                if (array.length > 0) {
                    for (var item of array) {
                        if (+item.idmaterial === +material.idmaterial) {
                            total += +item.cantidad;
                        }
                    }
                }

                return total;
            },
            cantidadOcupada(cantidad) {
                //Se hace una sumatoria de la cantidad ocupada de esa funda para los lotes correspondientes
                var materiales = this.materiales;
                for (var material of materiales) {
                    //Se escoge solo el item del material activo para edicion
                    if (material.idmaterial == this.data_material.codigo) {
                        //Si ya tenia un ingreso registrado, se lo resta para agregar la nueva cantidad de ser necesario
                        if (this.acumulaCantidad(material.idmaterial) > 0) {
                            if (this.acumulaCantidad(material.idmaterial) > 0) {
                                material.cant_ocupada = +material.cant_ocupada - +this.acumulaCantidad(material.idmaterial);
                            }
                        } else {
                            material.cant_ocupada = +material.cant_ocupada - +material.cantidad;
                        }
                        material.cant_ocupada = +material.cant_ocupada + +cantidad;
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
                return saldo;
            }
        }
    }
</script>

<style scoped>

</style>
