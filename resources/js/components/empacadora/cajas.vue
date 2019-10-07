<template>
    <div class="row" id="detalle-cajas">
        <div class="col-12">
            <div class="form-row">
                <div class="col-lg-2 col-md-2 col-6" v-for="caja1 in empacadora.cajas">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <input :value="caja1" type="checkbox"
                                       aria-label="Checkbox for following text input" v-model="cajas_check"
                                       v-on:change="embaladores_consolidar(empacadora.produccion_cajas)"
                                />
                            </div>
                        </div>
                        <input type="text" class="form-control bg-white" aria-label="Text input with checkbox"
                               :value="caja1.marca.split('/')[1]"
                               readonly/>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th class="text-center justify-content-center" style="width: 12.5%">
                        Embalador #1 <img src="/img/embalador_active.png"/>
                    </th>
                    <th class="text-center justify-content-center" style="width: 12.5%">
                        Embalador #2 <img src="/img/embalador_active.png"/>
                    </th>
                    <th class="text-center justify-content-center" style="width: 12.5%">
                        Embalador #3 <img src="/img/embalador_active.png"/>
                    </th>
                    <th class="text-center justify-content-center" style="width: 12.5%">
                        Embalador #4 <img src="/img/embalador_active.png"/>
                    </th>
                    <th class="text-center justify-content-center" style="width: 12.5%">
                        Embalador #5 <img src="/img/embalador_active.png"/>
                    </th>
                    <th class="text-center justify-content-center" style="width: 12.5%">
                        Embalador #6 <img src="/img/embalador_active.png"/>
                    </th>
                    <th class="text-center justify-content-center" style="width: 12.5%">
                        Embalador #7 <img src="/img/embalador_active.png"/>
                    </th>
                    <th class="text-center justify-content-center" style="width: 12.5%">
                        Embalador #8 <img src="/img/embalador_active.png"/>
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td v-for="embalador in embaladores_list">
                        <div class="input-group">
                            <input class="form-control bg-white text-center" readonly
                                   v-bind:value='embalador.total_cajas'/>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-primary form-control"
                                        v-on:click="(embaladores_cajas(embalador))">
                                    Detalle
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
            <hr>
        </div>
        <div class="col-12">
            <div class="d-flex justify-content-center">
                <div class="card-deck col-md-12 mb-3 text-center p-0">
                    <div class="col-md-4 col-sm-6 col-12" v-for="caja in empacadora.produccion_cajas">
                        <div class="card mb-4 shadow-sm">
                            <div class="card-header">
                                <h5 class="my-0 font-weight-normal">{{caja.marca}}</h5></div>
                            <div class="card-body" id="detalles">
                                <h1 class="card-title pricing-card-title">{{caja.totalpesadas}}
                                    <small class="text-muted">/ {{caja.totaldecajas}} cj's</small>
                                </h1>
                                <canvas v-bind:id="caja.idcaja + '-' + caja.totalpesadas" width="5" height="3"></canvas>
                                <ul class="list-unstyled mt-3 mb-4">
                                    <div class="card">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item"><h4>{{caja.last.split(' ')[0]}} - <strong>{{caja.last.split(' ')[1]}}</strong></h4>
                                            </li>
                                            <li class="list-group-item"><h4><strong>Peso: </strong> {{caja.peso}}</h4>
                                            </li>
                                            <li class="list-group-item"><h4><span
                                                    class="badge badge-pill badge-success">{{caja.porcentajepesadas}}%</span>
                                            </h4></li>
                                            <li class="list-group-item">
                                                <h5>
                                                    <small>{{caja.puerto.toUpperCase()}}</small>
                                                    - <strong>{{caja.transporte.toUpperCase()}}</strong></h5>
                                            </li>
                                        </ul>
                                    </div>
                                </ul>
                                <hr>
                                <button type="button" class="btn btn-lg btn-block btn-outline-primary embalador"
                                        v-on:click="embaladores(caja)">
                                    Embaladores ({{caja.idembalador.length}})
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import 'bootstrap-daterangepicker/daterangepicker.css';
    import moment from 'moment/moment'

    export default{
        data() {
            return {
                hacienda: $('#id-hacienda').selectpicker('val'),
                desde: '',
                hasta: '',
                empacadora: {
                    produccion_cajas: [],
                    cajas: []
                },
                cajas_check: [],
                embaladores_list: []
            }
        },
        mounted(){
            var hacienda = $('#id-hacienda').selectpicker('val');
            var funct = this;
            var startDate;
            var endDate;

            moment.locale('es');

            $('input[name="datetimes"]').daterangepicker({
                opens: 'center',
                startDate: moment().startOf('day'),
                endDate: moment().startOf('day').add(1, 'day'),
                dateLimit: {days: 1},
                showWeekNumbers: false,
                "showDropdowns": true,
            });

            $('#daterange').on('apply.daterangepicker', function (ev, picker) {
                var hacienda = $('#id-hacienda').selectpicker('val');
                startDate = picker.startDate.format('YYYY-MM-DD');
                endDate = picker.endDate.format('YYYY-MM-DD');
                funct.titulo(startDate, endDate);
                funct.getDataApiAllwaeights(hacienda, startDate, endDate);
            });

            $('#id-hacienda').on('change', function () {
                var hacienda = $(this).selectpicker('val');
                funct.getDataApiAllwaeights(hacienda, startDate, endDate);
            });

            $('#id-btn-refresh').on('click', function () {
                var hacienda = $('#id-hacienda').selectpicker('val');
                startDate = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
                endDate = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');

                funct.getDataApiAllwaeights(hacienda, startDate, endDate);
                funct.titulo(startDate, endDate);
            });

            startDate = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
            endDate = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');

            funct.getDataApiAllwaeights(hacienda, startDate, endDate);
            funct.titulo(startDate, endDate);

        },
        methods: {
            getDataApiAllwaeights: function (hacienda = 343, startDate, endDate) {
                var loading = $('#id-loading');
                loading.removeClass('d-none');
                loading.parent().removeClass('d-none');
                $('#detalle-cajas').addClass('d-none');
                let func = this;

                axios.get(`/empacadora/cajas/api-allweitghts/${hacienda}/${startDate}/${endDate}`)
                    .then(response => {
                        let objeto = response.data;
                        if (objeto.cajas.length > 0) {
                            this.empacadora.cajas = [];
                            this.empacadora.produccion_cajas = objeto.cajas;
                            var caja = objeto.cajas;
                            var respuesta = false;
                            $.each(caja, function (index, value) {
                                if (Array.isArray(func.empacadora.cajas) && func.empacadora.cajas.length) {
                                    $.each(func.empacadora.cajas, function (index2, value2) {
                                        if (value.idcaja == value2.idcaja) {
                                            respuesta = true;
                                        }
                                    });
                                    if (!respuesta) {
                                        func.empacadora.cajas.push(caja[index]);
                                    }
                                    respuesta = false;
                                } else {
                                    func.empacadora.cajas.push(caja[index]);
                                }
                            });

                            this.embaladores_consolidar(this.empacadora.produccion_cajas);
                            $('#detalle-cajas').removeClass('d-none');
                        } else {
                            alert("No existe registros para esta fecha");
                        }

                        loading.addClass('d-none');
                        loading.parent().addClass('d-none');
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            embaladores: function (objeto) {
                let cajas = this.empacadora.produccion_cajas;
                let func = this;
                $('#table-embaladores #detalle').html('');
                let html = '';

                $.each(cajas, function (index, value) {
                    if (value.idcaja == objeto.idcaja && (value.totalpesadas == objeto.totalpesadas)) {
                        $('#exampleModalLabel').html(`<h5><strong>${value.marca}</strong></h5>`)
                        value.idembalador.sort(function (a, b) {
                            return b.total - a.total
                        });
                        $.each(value.idembalador, function (i, embalador) {
                            html += func.modelodetalle(embalador);
                        });
                    }
                });

                $('#table-embaladores #detalle').append(html);
                $('#exampleModal').modal('show');
            },
            embaladores_cajas: function (objeto) {
                let cajas = objeto.cajas;
                let func = this;
                $('#table-embaladores-cajas #detalle-cajas').html('');
                let html = '';
                $('#titulo_embalador').html(`<h5><strong>Embalador #${objeto.idembalador}</strong></h5>`)

                cajas.sort(function (a, b) {
                    return b.total - a.total
                });

                $.each(cajas, function (index, value) {

                    html += func.modelodetalle_cajas_embalador(value, objeto.idembalador);
                });

                $('#table-embaladores-cajas #detalle-cajas').append(html);
                $('#embalador-detalle-modal').modal('show');
            },
            modelodetalle: function (objeto) {
                var html = `<tr>
                      <th class="text-center" scope="row" style="width: 25%">${objeto.idembalador}</th>`;
                if (objeto.total > 100) {
                    html += `<td class="text-center"><img src="/img/embalador_active.png" class="w-10 h-10"></td>`;
                } else {
                    html += `<td class="text-center"><img src="/img/embalador.png" class="w-10 h-10"></td>`;
                }
                html += `<td class="text-center" style="width: 25%">${objeto.total}</td>`;
                html += `<td class="text-center" style="width: 25%"><h5><span class="badge badge-pill badge-primary">${parseFloat(objeto.peso_promedio).toFixed(2)}</span></h5></td> </tr>`;
                return html;
            },
            modelodetalle_cajas_embalador: function (objeto, embalador) {
                var html = `<tr>
                      <th class="text-center" scope="row" style="width: 25%">${embalador}</th>`;
                html += `<td class="text-center" style="width: 25%">${objeto.detalle}</td>`;
                html += `<td class="text-center" style="width: 25%"><h5><span class="badge badge-pill badge-primary">${objeto.total}</span></h5></td> </tr>`;
                return html;
            },
            chart_met: function (element, data) {
                return new Chart(element,
                    {
                        type: 'doughnut',
                        data: {
                            labels: ['Pesadas', 'Total'],
                            datasets: [{
                                label: '# de cajas',
                                data: data,
                                backgroundColor: [
                                    'rgba(255, 45, 55,0.8)',
                                    'rgba(0, 70, 255,0.6)'
                                ],
                                borderColor: [
                                    'rgba(255, 45, 55, 1)',
                                    'rgba(0, 70, 255, 1)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        beginAtZero: true
                                    },
                                }]
                            }
                        }
                    });
            },
            titulo: function (startDate, endDate) {
                $('#titulo').html('');
                $('#titulo').append(`<h3 class="ml-1 mt-1">Datos de produccion de ${startDate} hasta ${endDate} </h3>`);
            },
            embaladores_consolidar: function (produccion) {
                var list_embaladores = [1, 2, 3, 4, 5, 6, 7, 8];
                var funct = this;
                funct.embaladores_list = [];
                $.each(list_embaladores, function (x, val) {
                    var datos = {
                        idembalador: val,
                        cajas: [],
                        total_cajas: 0
                    };

                    var cajas_establecidas = funct.empacadora.cajas;

                    if (funct.cajas_check.length) {
                        cajas_establecidas = funct.cajas_check;
                    }

                    $.each(cajas_establecidas, function (index3, value3) {
                        var cajas_total = {
                            idcaja: value3.idcaja,
                            detalle: value3.marca.split('/')[1],
                            total: 0
                        };
                        $.each(produccion, function (index, value) {
                            if (value3.idcaja == value.idcaja) {
                                $.each(value.idembalador, function (index2, value2) {
                                    if (val == value2.idembalador) {
                                        datos.total_cajas += +value2.total;
                                        cajas_total.total += +value2.total;
                                    }
                                });
                            }
                        });
                        datos.cajas.push(cajas_total);
                    });
                    funct.embaladores_list.push(datos);
                })
            }
        },
        updated: function () {
            let funct = this;
            var chart = '';

            if (chart != '')
                chart.destroy();

            $.each(this.empacadora.produccion_cajas, function (index, value) {
                var elemento = document.getElementById(`${value.idcaja}-${value.totalpesadas}`);
                chart = funct.chart_met(elemento, [value.totalpesadas, value.totaldecajas]);
                chart.destroy();
                chart = funct.chart_met(elemento, [value.totalpesadas, value.totaldecajas]);
            });
        }
    }
</script>