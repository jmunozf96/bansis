<template>

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
                        return `/sistema/empleados/${criterio}`;
                    },
                    getValue: "nombre",
                    ajaxSettings: {
                        method: 'GET',
                        dataType: "json"
                    },
                    list: {
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
                var object = this;
                var options = {
                    url: function (criterio) {

                        return `/productos/5/${criterio}`;
                    },
                    getValue: "nombre",
                    ajaxSettings: {
                        method: 'GET',
                        dataType: "json"
                    },
                    list: {
                        onClickEvent: function () {
                            var data = $('#nombre-producto').getSelectedItemData();
                            $('#codigo-producto').val(data.codigo);
                            object.empleado = data.codigo;
                        },
                        onKeyEnterEvent: function () {
                            var data = $('#nombre-producto').getSelectedItemData();
                            $('#codigo-producto').val(data.codigo);
                            object.empleado = data.codigo;
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