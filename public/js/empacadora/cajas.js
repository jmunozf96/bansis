/**
 * Created by Estadisticas on 26/09/2019.
 */

window.addEventListener("load", function () {

    getDataApiAllwaeights('343', '2019-09-27', '2019-09-28');

    setInterval(function() {
        getDataApiAllwaeights('343', '2019-09-27', '2019-09-28');
        $('#exampleModal').modal('hide');
    }, 60000);

    function getDataApiAllwaeights(hacienda, desde, hasta) {
        document.getElementById('cajas_dia').innerHTML = '';
        $.ajax({
            url: peticion_url + "api-allweitghts/" + hacienda + "/" + desde + "/" + hasta,
            type: 'GET',
            beforeSend: function () {
                //$("#loaderDiv").show();
                document.getElementById('loading').innerHTML =
                    ` <div class="spinner-grow mt-5" style="width: 5rem; height: 5rem;" role="status">
                           <span class="sr-only">Loading...</span>
                        </div>`;
            },
            success: function (data) {
                document.getElementById('loading').innerHTML = '';
                if (data) {
                    document.getElementById('cajas_dia').innerHTML = '';
                    for (var caja in data.cajas) {
                        let objeto = data.cajas[caja];
                        document.getElementById('cajas_dia').innerHTML += template(objeto);
                        get_embaladores();
                    }
                }
            }
        })
    }

    function template(objeto) {
        var html = `<div class="form-row col-3"><div class="card mb-4 shadow-sm"><div class="card-header">`;
        html += `<h5 class="my-0 font-weight-normal">${objeto.marca}</h5></div>`;
        html += `<div class="card-body" id="detalles">`;
        html += `<h1 class="card-title pricing-card-title">${objeto.totalpesadas}<small class="text-muted">/ ${objeto.totaldecajas} cj's</small></h1>`;
        html += `<ul class="list-unstyled mt-3 mb-4">`;
        html += `<div class="card">
                  <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>Fecha: </strong> ${objeto.fecha}</li>
                    <li class="list-group-item"><strong>Peso: </strong> ${objeto.peso}</li>
                    <li class="list-group-item"><h5><span class="badge badge-pill badge-success">${objeto.porcentajepesadas}%</span></h5></li>
                    <li class="list-group-item"><small>${objeto.puerto}</small> - <strong>${objeto.transporte}</strong></li>
                  </ul>
                </div><hr>`;
        html += `<button type="button" class="btn btn-lg btn-block btn-outline-primary embalador" data-json='{"idcaja":"${objeto.marca}","embaladores":${JSON.stringify(objeto.idembalador)}}'>Embaladores (${objeto.idembalador.length})</button></div></div></div>`;

        return html;
    }

    function get_embaladores() {
        $('.embalador').bind('click', function () {
            $('#table-embaladores #detalle').html('');
            var datos = $(this).data('json');
            $('#exampleModalLabel').html(`<h3>${datos.idcaja}</h3>`);
            var html = "";
            datos.embaladores.sort(function(a, b){return b.total - a.total});
            for (var embalador in datos.embaladores) {
                var objeto = datos.embaladores[embalador];
                if(objeto.total > 100){
                    objeto.imagen = embalador_url_active;
                }else{
                    objeto.imagen = embalador_url;
                }
                html += modelodetalle(objeto);
            }
            $('#table-embaladores #detalle').append(html);

            $('#exampleModal').modal('show');
        })
    }

    function modelodetalle(objeto) {
        var html = `<tr>
                      <th class="text-center" scope="row" style="width: 25%">${objeto.idembalador}</th>
                      <td class="text-center"><img src="${objeto.imagen}" class="w-10 h-10"></td>
                      <td class="text-center" style="width: 25%">${objeto.total}</td>
                      <td class="text-center" style="width: 25%"><h5><span class="badge badge-pill badge-primary">${parseFloat(objeto.peso_promedio).toFixed(2)}</span></h5></td>
                    </tr>`;
        return html;
    }
});

