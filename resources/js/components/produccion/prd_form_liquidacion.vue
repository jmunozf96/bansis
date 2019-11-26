<template>
    <form>
        <div class="form-group">
            <label for="id-viaje">Nº Viaje</label>
            <input type="text"
                   v-model="liquidacion_dole.nviaje"
                   class="form-control bg-white" id="id-viaje" readonly>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="id-descripcion">Descripcion</label>
                <input type="text"
                       v-model="liquidacion_dole.descripcion"
                       class="form-control bg-white" id="id-descripcion" readonly>
            </div>
            <div class="form-group col-md-6">
                <label for="id-ubicacion">Ubicacion</label>
                <input type="text"
                       v-model="liquidacion_dole.ubicacion"
                       class="form-control bg-white" id="id-ubicacion" readonly>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="id-tipo-caja">Tipo de Caja</label>
                <input type="text"
                       v-model="liquidacion_dole.tipo_caja"
                       class="form-control bg-white" id="id-tipo-caja" readonly>
            </div>
            <div class="form-group col-md-4">
                <label for="id-cantidad">Cantidad</label>
                <input type="number"
                       v-model="liquidacion_dole.cantidad"
                       class="form-control bg-white" id="id-cantidad" readonly>
            </div>
            <div class="form-group col-md-4">
                <label for="id-tarifa">Tarifa</label>
                <input type="number"
                       v-model="liquidacion_dole.tarifa"
                       class="form-control bg-white" id="id-tarifa" readonly>
            </div>
            <template v-if="liquidacion_dole.bono">
                <div class="form-group col-md-4">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="customSwitch1" disabled checked>
                        <label class="custom-control-label" for="customSwitch1">Bono de empaque</label>
                    </div>
                </div>
                <div class="form-group col-md-4">
                    <label for="id-bono_emp">Bono Tarifa</label>
                    <input type="number"
                           v-model="liquidacion_dole.bono_emp"
                           class="form-control bg-white" id="id-bono_emp" readonly>
                </div>
                <div class="form-group col-md-4">
                    <label for="id-val_bono">Valor Bono</label>
                    <input type="number"
                           v-model="liquidacion_dole.val_bono"
                           class="form-control bg-white" id="id-val_bono" readonly>
                </div>
            </template>
        </div>
        <div class="form-row mt-1">
            <table class="table">
                <thead>
                <tr class="text-center">
                    <th scope="col">Caja</th>
                    <th scope="col">Codigo Coorp.</th>
                    <th scope="col">Cantidad</th>
                    <th scope="col">Accion</th>
                </tr>
                </thead>
                <tbody>
                <template v-for="(liquidacion, index) in liquidacion_dole.liquidacion_semanal">
                    <tr>
                        <td class="text-center">{{liquidacion.des_detprod}}</td>
                        <td class="text-center">{{liquidacion.ds_cjubes}}</td>
                        <td class="text-center">{{liquidacion.cant_distrib}}</td>
                        <td class="text-center">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="customCheck1">
                                <label class="custom-control-label" for="customCheck1">Liquidar</label>
                            </div>
                        </td>
                    </tr>
                </template>
                </tbody>
            </table>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
</template>

<script>
    import BootstrapVue from "bootstrap-vue";
    Vue.use(BootstrapVue);

    export default{
        props: ['liquidacion'],
        data(){
            return {
                liquidacion_dole: this.liquidacion,
                fields: [
                    {key: 'des_detprod', label: 'CAJA'},
                    {key: 'ds_cjubes', label: 'CODIGO'},
                    {key: 'cant_distrib', label: 'CANTIDAD'},
                ]
            }
        },
        mounted(){
        },
        methods: {}
    }
</script>