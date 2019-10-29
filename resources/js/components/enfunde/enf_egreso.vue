<template>
    <table class="table table-bordered table-hover" id="despacho-items">
        <thead>
        <tr class="text-center">
            <th scope="col" style="width: 5%; font-size: 18px">Accion</th>
            <th scope="col" style="width: 10%; font-size: 18px">Fecha</th>
            <th scope="col" style="font-size: 18px">Detalle</th>
            <th style="font-size: 18px">Reemp.</th>
            <th scope="col" style="width: 15%; font-size: 18px">Presente</th>
            <th scope="col" style="width: 15%; font-size: 18px">Futuro</th>
        </tr>
        </thead>
        <tbody id="detalle">
        <tr v-for="(egreso, index) in despachos" class="table-sm" v-if="!('enfunde' in despachos[index])">
            <td style="width: 10%" class="text-center">
                <span v-if="!('enfunde' in despachos[index])">
                    <span v-if="statusForm && despacho.idmaterial == egreso.idmaterial && despacho.fecha == egreso.fecha
                        && despacho.presente == egreso.presente && despacho.futuro == egreso.futuro
                        && despacho.reemplazo == egreso.reemplazo && despacho.idempleado == egreso.idempleado">
                        <button class="btn btn-success btn-sm"
                                v-on:click="saveForm(index)">
                            <i class="fas fa-save"></i> Guardar
                        </button>
                    </span>
                <span v-else>
                        <button class="btn btn-primary btn-sm"
                                v-on:click="editForm(index)">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" v-on:click="deleteDespacho(index)">
                            <i class="fas fa-minus"></i>
                        </button>
                    </span>
                </span>

            </td>
            <td class="text-center" style="font-size: 16px">{{egreso.fecha}}</td>
            <td style="font-size: 16px">{{egreso.desmaterial}}</td>
            <td class="text-center">
                <span v-if="!('enfunde' in despachos[index])">
                    <b-button variant="primary"
                              v-b-popover.hover.top="egreso.reemplazo ? egreso.empleado : 'No hace relevo'"
                              title="Nombre lotero">
                    {{egreso.reemplazo ? "Si" : "No"}}
                </b-button>
                </span>
            </td>
            <td class="text-center" style="font-size: 16px">
                    <span v-if="egreso.presente">
                        <span v-if="statusForm && despacho.idmaterial == egreso.idmaterial && despacho.fecha == egreso.fecha
                        && despacho.presente == egreso.presente && despacho.futuro == egreso.futuro
                        && despacho.reemplazo == egreso.reemplazo && despacho.idempleado == egreso.idempleado">
                            <input class="form-control text-center cantidad-despacho" ref="cant" style="font-size: 16px"
                                   v-model="despacho.cantidad"
                                   v-on:focusout=""
                                   type="number"/>
                        </span>
                        <span v-else>
                            {{egreso.cantidad}}
                        </span>
                    </span>
                <span v-else></span>
            </td>
            <td class="text-center" style="font-size: 16px">
                    <span v-if="egreso.futuro">
                        <span v-if="statusForm && despacho.idmaterial == egreso.idmaterial && despacho.fecha == egreso.fecha
                            && despacho.presente == egreso.presente && despacho.futuro == egreso.futuro
                            && despacho.reemplazo == egreso.reemplazo && despacho.idempleado == egreso.idempleado">
                            <input class="form-control text-center cantidad-despacho" ref="cant" style="font-size: 16px"
                                   v-model="despacho.cantidad"
                                   v-on:focusout=""
                                   type="number"/>
                        </span>
                        <span v-else>
                            {{egreso.cantidad}}
                        </span>
                    </span>
                <span v-else></span>
            </td>
        </tr>
        <tr v-for="(egreso, index) in dato_enfunde" class="table-sm" v-if="('enfunde' in dato_enfunde[index])">
            <td style="width: 10%" class="text-center"></td>
            <td class="text-center" style="font-size: 16px">{{egreso.fecha}}</td>
            <td style="font-size: 16px"><b>{{egreso.desmaterial}}</b></td>
            <td class="text-center"></td>
            <td class="text-center" style="font-size: 16px">
               <span v-if="egreso.presente">
                   <b style="color: red">{{egreso.cantidad}}</b>
               </span>
            </td>
            <td class="text-center" style="font-size: 16px">
                <span v-if="egreso.futuro">
                    <b style="color: red">{{egreso.cantidad}}</b>
                </span>
            </td>
        </tr>
        </tbody>
    </table>
</template>

<script>
    import 'bootstrap-daterangepicker/daterangepicker.css';
    import moment from 'moment/moment';
    import SweetAlert from 'sweetalert2/src/sweetalert2';
    import BootstrapVue from 'bootstrap-vue';
    Vue.use(BootstrapVue);

    const Swal = SweetAlert;
    export default{
        data(){
            return {
                hacienda: $('#id-hacienda').val(),
                fecha: $('#fecha').val(),
                empleado: '',
                statusForm: false,
                despacho: {
                    fecha: '',
                    idmaterial: 0,
                    desmaterial: '',
                    reemplazo: 0,
                    idempleado: 0,
                    cantidad: 0,
                    presente: 1,
                    futuro: 0,
                    estado: 1
                },
                despachos: [],
                dato_enfunde: [],
                enfunde: null,
            }
        },
        mounted(){
            var self = this;
            moment.locale('es');

            $('#nombre-producto').attr('disabled', true);
            $('#cantidad').attr('disabled', true);
            $('#add-despacho').attr('disabled', true);

            /*self.getAutocompleteEmpleado();*/
            self.getAutocompleteEmpleadoReemplazo();
            self.getAutocompleteProducto();

            $('#detalle-total').val(self.totalizaDespacho());

            $("div.easy-autocomplete").removeAttr('style');

            $('input[name="fecha"]').daterangepicker({
                opens: 'center',
                startDate: moment().startOf('day'),
                singleDatePicker: true,
                showDropdowns: true,
            });

            $('input[name="fecha"]').on({
                change: function () {
                    self.fecha = $(this).val();
                }
            });

            if ($('#nombre-empleado').val() != "") {
                self.empleado = $('#nombre-empleado').val();
                $('#nombre-producto').attr('disabled', false);
                $('#nombre-producto').focus();

                if (self.empleado != '') {
                    self.getDataEmpleado(self.empleado, $('#semana').val(), self.hacienda);
                }
            }

            $('#nombre-empleado').on('change', function () {
                if ($(this).val() == '') {
                    return;
                }
                self.empleado = $(this).val();
                $('#nombre-producto').attr('disabled', false);
                $('#nombre-producto').focus();

                if (self.empleado != '') {
                    self.getDataEmpleado(self.empleado, $('#semana').val(), self.hacienda);
                }
            });

            $('#cantidad').on('keydown', function (e) {
                if (e.which == 13) {
                    e.preventDefault();
                    $('#add-despacho').click();
                }
            });

            $('#id-hacienda').on({
                change: function (e) {
                    self.hacienda = $(this).val();
                }
            });

            $('#btn-nuevo').on({
                click: function (e) {
                    self.resetForm();
                    $('#nombre-empleado').attr('disabled', false);
                    $('#nombre-empleado').focus();
                    $('#nombre-producto').attr('disabled', true);
                    $('#cantidad').attr('disabled', true);
                    $('#add-despacho').attr('disabled', true);

                    $('#detalle-total').val(self.totalizaDespacho());
                }
            });

            $('input[name=status-semana]').on('change', function () {
                var radios = $(this);
                if (radios.filter('[value=futuro]').is(':checked')) {
                    if (self.enfunde) {
                        Swal.fire({
                            position: 'center',
                            type: 'info',
                            title: 'Lotero tiene enfunde reportado: ' + self.enfunde.total_pre,
                            showConfirmButton: false,
                            timer: 1500
                        });

                        //Logica para saldo de fundas en la semana
                        //Traer el total de enfunde presente y futuro
                        //Restar por tipo de funda segun el estado del despacho ya sea presente o futuro
                        //el status es para tener el saldo de las fundas OJO
                    } else {
                        $('input[name=status-semana][value=presente]').prop('checked', true);
                        Swal.fire({
                            position: 'center',
                            type: 'info',
                            title: 'Lotero no tiene enfunde reportado',
                            showConfirmButton: false,
                            timer: 1500
                        })
                    }
                }
            });

            $('#add-despacho').on({
                click: function (e) {
                    var fecha, des_material, idmaterial, cantidad, presente = 0, futuro = 0;

                    var radios = $('input[name=status-semana]');

                    fecha = $('#fecha');
                    idmaterial = $('#codigo-producto');
                    des_material = $('#nombre-producto');
                    cantidad = $('#cantidad');
                    presente = radios.filter('[value=presente]').is(':checked');
                    futuro = radios.filter('[value=futuro]').is(':checked');

                    if (idmaterial.val() == '') {
                        return;
                    }

                    if (cantidad.val() == 0 || cantidad.val() == '') {
                        return;
                    }

                    let egreso = {
                        fecha: fecha.val(),
                        idmaterial: idmaterial.val(),
                        desmaterial: des_material.val(),
                        reemplazo: 0,
                        idempleado: 0,
                        empleado: '',
                        cantidad: cantidad.val(),
                        presente: presente,
                        futuro: futuro,
                        estado: 1
                    };


                    if ($('#id-reemplazo').prop('checked')) {
                        $('#emp-reemplazo').modal('show');
                        guardaRelevo(egreso);

                        idmaterial.val('');
                        des_material.val('');
                        cantidad.val('');
                        des_material.focus();

                        $('#id-empleado-reemplazo').val('');
                        $('#nombre-empleado-reemplazo').val('');
                    } else {
                        self.addDespacho(egreso);
                        $('#detalle-total').val(self.totalizaDespacho());
                        idmaterial.val('');
                        des_material.val('');
                        cantidad.val('');
                        des_material.focus();

                        $('#id-empleado-reemplazo').val('');
                        $('#nombre-empleado-reemplazo').val('');
                    }

                }
            });

            function guardaRelevo(egreso) {
                $('#btn-save-reemplazo').one("click", function () {
                    egreso.reemplazo = 1;
                    egreso.idempleado = +$('#id-empleado-reemplazo').val();
                    egreso.empleado = $('#nombre-empleado-reemplazo').val();
                    self.addDespacho(egreso);
                    $('#detalle-total').val(self.totalizaDespacho());
                    $('#emp-reemplazo').modal('hide');
                });
            }

            $('#btn-save').on({
                click: function (e) {
                    e.preventDefault();

                    let object_data = {
                        fecha: self.fecha,
                        semana: $('#semana').val(),
                        hacienda: self.hacienda,
                        idempleado: self.empleado,
                        total: self.totalizar(),
                        saldo: 0,
                        status: 0,
                        despachos: self.despachos
                    };

                    axios.post('/sistema/enfunde/despacho/save', object_data)
                        .then(response => {
                            let respuesta = response.data;
                            let tipo = 'danger', title = 'Error al intentar guardar el registro';
                            console.log(respuesta)
                            if (respuesta.code == 200) {
                                tipo = 'success';
                                title = 'Registro guardado con éxito';
                                self.despachos = [];
                                self.dato_enfunde = [];

                                if (respuesta.reg) {
                                    console.log(respuesta.reg);

                                    self.enfunde = respuesta.reg.empleado.lotero.enfunde;

                                    for (var x in respuesta.reg.egresos) {
                                        let egreso = {
                                            id: respuesta.reg.egresos[x].id,
                                            fecha: (respuesta.reg.egresos[x].fecha).toString("dd/MM/yyyy"),
                                            idmaterial: respuesta.reg.egresos[x].idmaterial,
                                            desmaterial: respuesta.reg.egresos[x].get_material.nombre,
                                            reemplazo: +respuesta.reg.egresos[x].reemplazo,
                                            idempleado: +respuesta.reg.egresos[x].idempleado,
                                            empleado: respuesta.reg.egresos[x].nom_reemplazo != null ? respuesta.reg.egresos[x].nom_reemplazo.nombre : '',
                                            cantidad: +respuesta.reg.egresos[x].cantidad,
                                            presente: +respuesta.reg.egresos[x].presente,
                                            futuro: +respuesta.reg.egresos[x].futuro,
                                            estado: respuesta.reg.egresos[x].status
                                        };

                                        self.despachos.push(egreso);
                                    }

                                    self.getEnfunde(self.enfunde, self.dato_enfunde);


                                    $('#detalle-total').val(self.totalizaDespacho());

                                    Swal.fire({
                                        position: 'top-end',
                                        type: tipo,
                                        title: title,
                                        showConfirmButton: false,
                                        timer: 1500
                                    })
                                } else {
                                    Swal.fire({
                                        position: 'top-end',
                                        type: 'info',
                                        title: 'Despacho de la semana cerrado',
                                        showConfirmButton: false,
                                        timer: 1500
                                    })
                                }
                            }


                        })
                        .catch(error => {
                            console.log(error.response)
                        });
                }
            })
        },
        methods: {
            addDespacho: function (data) {
                if (!this.existeDespacho(data)) {
                    this.despachos.push(data);
                    return true;
                } else {
                    return this.editDespacho(data);
                }
            },
            existeDespacho: function (data) {
                for (var i in this.despachos) {
                    let self = this.despachos[i];
                    if (data.idmaterial == self.idmaterial) {
                        if (data.fecha == self.fecha) {
                            if (data.presente == self.presente && data.futuro == self.futuro) {
                                if (data.reemplazo == self.reemplazo && data.idempleado == self.idempleado) {
                                    return true;
                                }
                            }
                        }
                    }
                }
                return false;
            },
            editDespacho: function (data) {
                for (var i in this.despachos) {
                    let self = this.despachos[i];
                    if (data.idmaterial == self.idmaterial) {
                        if (data.fecha == self.fecha) {
                            if (data.presente == self.presente && data.futuro == self.futuro) {
                                if (data.reemplazo == self.reemplazo && data.idempleado == self.idempleado) {
                                    self.cantidad = +self.cantidad + +data.cantidad;
                                    $('#detalle-total').val(this.totalizaDespacho());
                                    return true;
                                }
                            }
                        }
                    }
                }
                return false;
            },
            deleteDespacho: function (index) {
                let self = this;
                let alerta = 'Eliminado!';
                let mensaje = 'Registro eliminado con éxito!';
                Swal.fire(this.datasweetalert()).then((result) => {
                    if (result.value) {
                        if ('id' in self.despachos[index]) {
                            axios.delete(`/sistema/enfunde/despacho/delete/${self.empleado}/${$('#semana').val()}/${self.hacienda}/${self.despachos[index].id}`)
                                .then(response => {
                                    if (response.data.status == 'success') {
                                        this.despachos.splice(index, 1);
                                    }
                                    Swal.fire(alerta, response.data.message, response.data.status);
                                    $('#detalle-total').val(this.totalizaDespacho());
                                });
                        } else {
                            Swal.fire(alerta, mensaje, 'success');
                            this.despachos.splice(index, 1);
                            $('#detalle-total').val(this.totalizaDespacho());
                            return true;
                        }
                    }
                });
                return false;
            },
            totalizar: function () {
                var total = 0;
                for (var i in this.despachos) {
                    if (!('enfunde' in this.despachos[i])) {
                        total += parseInt(this.despachos[i].cantidad);
                    }
                }
                return total;
            },
            totalPresente: function () {
                var total = 0;
                for (var i in this.despachos) {
                    if (!('enfunde' in this.despachos[i])) {
                        if (this.despachos[i].presente) {
                            total += parseInt(this.despachos[i].cantidad);
                        }
                    }
                }
                return total;
            },
            totalFuturo: function () {
                var total = 0;
                for (var i in this.despachos) {
                    if (!('enfunde' in this.despachos[i])) {
                        if (this.despachos[i].futuro) {
                            total += parseInt(this.despachos[i].cantidad);
                        }
                    }
                }
                return total;
            },
            totalizaDespacho: function () {
                let string = 'TOTAL DESPACHO SEMANA ' + $('#semana').val() + ': ';
                let total = 0;
                let presente = 0;
                let futuro = 0;
                for (var i in this.despachos) {
                    if (!('enfunde' in this.despachos[i])) {
                        if (this.despachos[i].presente) {
                            presente += parseInt(this.despachos[i].cantidad);
                        } else {
                            futuro += parseInt(this.despachos[i].cantidad);
                        }
                    }
                }
                total = +presente + +futuro;
                return string + total + ' / PRESENTE: ' + presente + ' / FUTURO: ' + futuro;
            },
            editForm: function (index) {
                this.despacho.fecha = this.despachos[index].fecha;
                this.despacho.idmaterial = this.despachos[index].idmaterial;
                this.despacho.reemplazo = this.despachos[index].reemplazo;
                this.despacho.idempleado = this.despachos[index].idempleado;
                this.despacho.cantidad = this.despachos[index].cantidad;
                this.despacho.presente = this.despachos[index].presente;
                this.despacho.futuro = this.despachos[index].futuro;
                this.statusForm = true;
            },
            saveForm: function (index) {
                if (this.despacho.cantidad > 0 && this.despacho.cantidad != '') {
                    this.despachos[index].cantidad = this.despacho.cantidad;
                } else {
                    this.despacho.cantidad = this.despachos[index].cantidad;
                }

                this.despacho.idmaterial = '';
                this.despacho.cantidad = 0;
                this.statusForm = false;
                $('#detalle-total').val(this.totalizaDespacho());
            },
            /*getAutocompleteEmpleado: function () {
             var object = this;
             var semana = $('#semana').val();
             var options = {
             url: function (criterio) {
             return `/api/empleados/${criterio}`;
             },
             getValue: "nombre",
             ajaxSettings: {
             method: 'GET',
             dataType: "json"
             },
             theme: "green-light",
             list: {
             maxNumberOfElements: 5,
             match: {
             enabled: true
             },
             onClickEvent: function () {
             var data = $('#nombre-empleado').getSelectedItemData();
             $('#codigo-empleado').val(data.codigo);
             object.empleado = data.codigo;
             $('#nombre-producto').attr('disabled', false);
             $('#nombre-producto').focus();

             if (object.empleado != '') {
             object.getDataEmpleado(object.empleado, semana, object.hacienda);
             }
             },
             onKeyEnterEvent: function () {
             var data = $('#nombre-empleado').getSelectedItemData();
             $('#codigo-empleado').val(data.codigo);
             object.empleado = data.codigo;
             $('#nombre-producto').attr('disabled', false);
             $('#nombre-producto').focus();

             if (object.empleado != '') {
             object.getDataEmpleado(object.empleado, semana, object.hacienda);
             }
             },
             }
             };

             $("#nombre-empleado").easyAutocomplete(options);
             $("#nombre-empleado").on({
             change: function () {
             if ($(this).val() == '') {
             $('#codigo-empleado').val('');
             }
             }
             });
             },*/
            getAutocompleteEmpleadoReemplazo: function () {
                var object = this;
                var semana = $('#semana').val();
                var options = {
                    url: function (criterio) {
                        return `/api/empleados/${criterio}`;
                    },
                    getValue: "nombre",
                    ajaxSettings: {
                        method: 'GET',
                        dataType: "json"
                    },
                    theme: "green-light",
                    list: {
                        maxNumberOfElements: 5,
                        match: {
                            enabled: true
                        },
                        onClickEvent: function () {
                            var data = $('#nombre-empleado-reemplazo').getSelectedItemData();
                            $('#id-empleado-reemplazo').val(data.codigo);
                        },
                        onKeyEnterEvent: function () {
                            var data = $('#nombre-empleado-reemplazo').getSelectedItemData();
                            $('#id-empleado-reemplazo').val(data.codigo);
                        },
                    }
                };

                $("#nombre-empleado-reemplazo").easyAutocomplete(options);
                $("#nombre-empleado-reemplazo").on({
                    change: function () {
                        if ($(this).val() == '') {
                            $('#id-empleado-reemplazo').val('');
                        }
                    }
                });
            },
            getAutocompleteProducto: function () {
                var options = {
                    url: function (criterio) {
                        var bodega = $('#bodega').val();

                        if (bodega == '') {
                            alert("Seleccione una bodega");
                            return;
                        }

                        return `/api/productos/${bodega}/${criterio}`;
                    },
                    getValue: "nombre",
                    template: {
                        type: "description",
                        fields: {
                            description: "stock_det"
                        }
                    },
                    ajaxSettings: {
                        method: 'GET',
                        dataType: "json"
                    },
                    theme: "green-light",
                    list: {
                        maxNumberOfElements: 5,
                        match: {
                            enabled: true
                        },
                        onClickEvent: function () {
                            var data = $('#nombre-producto').getSelectedItemData();
                            $('#codigo-producto').val(data.codigo);
                            $('#cantidad').attr('disabled', false);
                            $('#add-despacho').attr('disabled', false);
                            $('#nombre-empleado').attr('disabled', true);
                            $('#cantidad').focus();
                        },
                        onKeyEnterEvent: function () {
                            var data = $('#nombre-producto').getSelectedItemData();
                            $('#codigo-producto').val(data.codigo);
                            $('#cantidad').attr('disabled', false);
                            $('#add-despacho').attr('disabled', false);
                            $('#nombre-empleado').attr('disabled', true);
                            $('#cantidad').focus();
                        },
                    }
                };

                $("#nombre-producto").easyAutocomplete(options);
                $("#nombre-producto").on({
                    change: function () {
                        if ($(this).val() == '') {
                            $('#codigo-producto').val('');
                        }
                    }
                });
            },
            getDataEmpleado: function (empleado, semana, hacienda) {
                let self = this;
                self.despachos = [];
                axios.get(`/sistema/enfunde/despacho/${empleado}/${semana}/${hacienda}/1`)
                    .then(response => {
                        if (response.data) {
                            console.log(response.data);
                            self.enfunde = response.data.empleado.lotero.enfunde;

                            for (var x in response.data.egresos) {
                                let egreso = {
                                    id: response.data.egresos[x].id,
                                    fecha: (response.data.egresos[x].fecha).toString("dd/MM/yyyy"),
                                    idmaterial: response.data.egresos[x].idmaterial,
                                    desmaterial: response.data.egresos[x].get_material.nombre,
                                    reemplazo: +response.data.egresos[x].reemplazo,
                                    idempleado: +response.data.egresos[x].idempleado,
                                    empleado: response.data.egresos[x].nom_reemplazo != null ? response.data.egresos[x].nom_reemplazo.nombre : '',
                                    cantidad: +response.data.egresos[x].cantidad,
                                    presente: +response.data.egresos[x].presente,
                                    futuro: +response.data.egresos[x].futuro,
                                    estado: response.data.egresos[x].status,
                                };

                                self.despachos.push(egreso);
                            }

                            self.getEnfunde(self.enfunde, self.dato_enfunde);

                            $('#detalle-total').val(self.totalizaDespacho());
                        }
                    })
            },
            getEnfunde: function (enfunde, array) {
                if (enfunde) {
                    if (parseInt(enfunde.total_pre) > 0) {
                        let tot_enfunde = {
                            fecha: '',
                            desmaterial: 'TOTAL ENFUNDE PRESENTE',
                            reemplazo: 0,
                            idempleado: 0,
                            empleado: '',
                            cantidad: -(parseInt(enfunde.total_pre)),
                            presente: 1,
                            futuro: 0,
                            estado: 0,
                            enfunde: 1
                        };
                        array.push(tot_enfunde);
                    }
                    if (parseInt(enfunde.total_fut) > 0) {
                        let tot_enfunde = {
                            fecha: '',
                            desmaterial: 'TOTAL ENFUNDE FUTURO',
                            reemplazo: 0,
                            idempleado: 0,
                            empleado: '',
                            cantidad: -(parseInt(enfunde.total_fut)),
                            presente: 0,
                            futuro: 1,
                            estado: 0,
                            enfunde: 1
                        };
                        array.push(tot_enfunde);
                    }
                }
            },
            resetData: function () {
                this.despacho.fecha = $('#fecha').val();
                this.despacho.idmaterial = '';
                this.despacho.des_material = '';
                this.despacho.cantidad = 0;
            },
            resetForm: function () {
                this.empleado = '';
                this.statusForm = false;
                this.despacho.fecha = $('#fecha').val();
                this.despacho.idmaterial = '';
                this.despacho.des_material = '';
                this.despacho.cantidad = 0;
                this.despacho.presente = 1;
                this.despacho.futuro = 0;
                this.despacho.estado = 1;
                this.despachos = [];
                this.dato_enfunde = [];

                $('#codigo-empleado').val('');
                $('#codigo-producto').val('');
                $('#nombre-producto').val('');
                $('#cantidad').val('');
            },
            datasweetalert(){
                var data = {
                    title: 'Estas seguro de realizar esta acción?',
                    text: "Tu no podras revertir esto!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Si, hazlo!'
                };
                return data;
            }
        }
    }
</script>