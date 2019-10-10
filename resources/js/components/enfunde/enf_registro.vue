<template>
    <div class="container-fluid p-0">
        <table class="table table-bordered table-hover">
            <thead>
            <tr class="text-center">
                <th scope="col" style="width: 15%">Fecha</th>
                <th scope="col">Detalle</th>
                <th scope="col" style="width: 15%">Cantidad</th>
                <th scope="col" style="width: 15%">Accion</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</template>

<script>
    import 'bootstrap-daterangepicker/daterangepicker.css';
    import moment from 'moment/moment'

    export default{
        data(){
            return {
                hacienda: $('#id-hacienda').val(),
                fecha: $('#fecha').val(),
                empleado: '',
                despacho: []
            }
        },
        mounted(){
            moment.locale('es');

            this.getAutocompleteEmpleado();
            this.getAutocompleteProducto();
            $("div.easy-autocomplete").removeAttr('style');

            $('input[name="fecha"]').daterangepicker({
                opens: 'center',
                startDate: moment().startOf('day'),
                singleDatePicker: true,
                showDropdowns: true,
            });
        },
        methods: {
            getAutocompleteEmpleado: function () {
                var object = this;
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
                        },
                        onKeyEnterEvent: function () {
                            var data = $('#nombre-empleado').getSelectedItemData();
                            $('#codigo-empleado').val(data.codigo);
                            object.empleado = data.codigo;
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
                        },
                        onKeyEnterEvent: function () {
                            var data = $('#nombre-producto').getSelectedItemData();
                            $('#codigo-producto').val(data.codigo);
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
            }
        }
    }
</script>