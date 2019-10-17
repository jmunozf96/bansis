<template>
    <table class="table table-bordered table-hover" id="despacho-items">
        <thead>
        <tr class="text-center">
            <th scope="col" style="width: 5%; font-size: 18px">Accion</th>
            <th scope="col" style="width: 10%; font-size: 18px">Fecha</th>
            <th scope="col" style="font-size: 18px">Detalle</th>
            <th scope="col" style="width: 15%; font-size: 18px">Presente</th>
            <th scope="col" style="width: 15%; font-size: 18px">Futuro</th>
        </tr>
        </thead>
        <tbody id="detalle">
        <tr v-for="(egreso, index) in despachos" class="table-sm">
            <td style="width: 10%" class="text-center">
                    <span v-if="statusForm && despacho.idmaterial == egreso.idmaterial && despacho.fecha == egreso.fecha">
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
            </td>
            <td class="text-center" style="font-size: 16px">{{egreso.fecha}}</td>
            <td style="font-size: 16px">{{egreso.desmaterial}}</td>
            <td class="text-center" style="font-size: 16px">
                    <span v-if="egreso.presente">
                        <span v-if="statusForm && despacho.idmaterial == egreso.idmaterial && despacho.fecha == egreso.fecha">
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
                        <span v-if="statusForm && despacho.idmaterial == egreso.idmaterial && despacho.fecha == egreso.fecha">
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
        </tbody>
    </table>
</template>

<script>
    import 'bootstrap-daterangepicker/daterangepicker.css';
    import moment from 'moment/moment';
    import SweetAlert from 'sweetalert2/src/sweetalert2';

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
                    cantidad: 0,
                    presente: 1,
                    futuro: 0,
                    estado: 1
                },
                despachos: []
            }
        },
        mounted(){
            var self = this;
            moment.locale('es');

            $('#nombre-producto').attr('disabled', true);
            $('#cantidad').attr('disabled', true);
            $('#add-despacho').attr('disabled', true);

            this.getAutocompleteEmpleado();
            this.getAutocompleteProducto();
            $('#detalle-total').val(this.totalizaDespacho());

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

            $('#cantidad').on('keydown', function (e) {
                if (e.which == 13) {
                    e.preventDefault();
                    $('#add-despacho').click();
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
                        cantidad: cantidad.val(),
                        presente: presente,
                        futuro: futuro,
                        estado: 1
                    };

                    self.addDespacho(egreso);
                    $('#detalle-total').val(self.totalizaDespacho());

                    idmaterial.val('');
                    des_material.val('');
                    cantidad.val('');
                    des_material.focus();
                }
            });

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

                    console.log(object_data);
                    axios.post('/sistema/enfunde/save', object_data)
                        .then(response => {
                            console.log(response)
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
                    if (data.idmaterial == this.despachos[i].idmaterial) {
                        if (data.fecha == this.despachos[i].fecha) {
                            return true;
                        }
                    }
                }
                return false;
            },
            editDespacho: function (data) {
                for (var i in this.despachos) {
                    if (data.idmaterial == this.despachos[i].idmaterial) {
                        if (data.fecha == this.despachos[i].fecha) {
                            this.despachos[i].cantidad = +this.despachos[i].cantidad + +data.cantidad;
                            return true;
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
                                    if (response.data.code == 200) {
                                        this.despachos.splice(index, 1);
                                    }
                                    Swal.fire(alerta, response.data.message, response.data.status);
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
                    total += parseInt(this.despachos[i].cantidad);
                }
                return total;
            },
            totalizaDespacho: function () {
                let string = 'TOTAL DESPACHO SEMANA ' + $('#semana').val() + ': ';
                let total = 0;
                let presente = 0;
                let futuro = 0;
                for (var i in this.despachos) {
                    if (this.despachos[i].presente) {
                        presente += parseInt(this.despachos[i].cantidad);
                    } else {
                        futuro += parseInt(this.despachos[i].cantidad);
                    }
                }
                total = +presente + +futuro;
                return string + total + ' / PRESENTE: ' + presente + ' / FUTURO: ' + futuro;
            },
            editForm: function (index) {
                this.despacho.fecha = this.despachos[index].fecha;
                this.despacho.idmaterial = this.despachos[index].idmaterial;
                this.despacho.cantidad = this.despachos[index].cantidad;
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
            getAutocompleteEmpleado: function () {
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
                axios.get(`/sistema/enfunde/despacho/${empleado}/${semana}/${hacienda}/1`)
                    .then(response => {
                        if (response.data) {
                            for (var x in response.data.egresos) {
                                let egreso = {
                                    id: response.data.egresos[x].id,
                                    fecha: response.data.egresos[x].fecha,
                                    idmaterial: response.data.egresos[x].idmaterial,
                                    desmaterial: response.data.egresos[x].get_material.nombre,
                                    cantidad: response.data.egresos[x].cantidad,
                                    presente: +response.data.egresos[x].presente,
                                    futuro: +response.data.egresos[x].futuro,
                                    estado: response.data.egresos[x].status
                                };
                                self.despachos.push(egreso);
                            }
                        }
                    })
            },
            resetData: function () {
                this.despacho.fecha = $('#fecha').val();
                this.despacho.idmaterial = '';
                this.despacho.des_material = '';
                this.despacho.cantidad = 0;
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