<template>
  <div class="container-fluid p-0">
    <table class="table table-bordered table-hover table-sm" v-if="saldo">
      <thead>
        <tr class="text-center">
          <th style="width: 5%">...</th>
          <th style="width: 25%; font-size: 18px">SaldoIni</th>
          <th style="width: 25%; font-size: 18px">SaldoEnf</th>
          <th style="width: 25%; font-size: 18px">SaldoFin</th>
          <th style="width: 20%; font-size: 18px">Status</th>
        </tr>
      </thead>
      <tbody class="table-sm">
        <tr style="font-size: 16px" class="text-center">
          <td>
            <div class="spinner-grow" role="status">
              <span class="sr-only">Loading...</span>
            </div>
          </td>
          <td>
            <b style="color: red; font-size: 20px">{{saldo.saldo_inicial}} fundas.</b>
          </td>
          <td>
            <b style="color: red; font-size: 20px">{{saldo.salida}}</b>
          </td>
          <td>
            <b style="color: red; font-size: 20px">{{saldo.saldo}} fundas.</b>
          </td>
          <td v-if="saldo.status == 1">
            <div class="custom-control custom-switch">
              <input
                type="checkbox"
                class="custom-control-input"
                disabled
                id="customSwitch1"
                checked
              />
              <label class="custom-control-label" for="customSwitch1">Saldo Activo</label>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
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
        <template v-for="(egreso, index) in despachos">
          <tr class="table-sm" v-if="!('enfunde' in despachos[index])" :key="egreso.idmaterial + `-${index+1}`">
            <td style="width: 10%" class="text-center">
              <span v-if="!('enfunde' in despachos[index])">
                <span
                  v-if="statusForm && despacho.idmaterial == egreso.idmaterial && despacho.fecha == egreso.fecha
                        && despacho.presente == egreso.presente && despacho.futuro == egreso.futuro
                        && despacho.reemplazo == egreso.reemplazo && despacho.idempleado == egreso.idempleado"
                >
                  <button class="btn btn-success btn-sm" v-on:click="saveForm(index)">
                    <i class="fas fa-save"></i> Guardar
                  </button>
                </span>
                <span v-else>
                  <button
                    class="btn btn-primary btn-sm"
                    v-if="(egreso.presente && !enfunde) || (egreso.futuro && (enfunde && +enfunde.total_fut == 0))"
                    v-on:click="editForm(index)"
                  >
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
                <b-button
                  variant="primary"
                  v-b-popover.hover.top="egreso.reemplazo ? egreso.empleado : 'No hace relevo'"
                  title="Nombre lotero"
                >{{egreso.reemplazo ? "Si" : "No"}}</b-button>
              </span>
            </td>
            <td class="text-center" style="font-size: 16px">
              <span v-if="egreso.presente">
                <span
                  v-if="statusForm && despacho.idmaterial == egreso.idmaterial && despacho.fecha == egreso.fecha
                        && despacho.presente == egreso.presente && despacho.futuro == egreso.futuro
                        && despacho.reemplazo == egreso.reemplazo && despacho.idempleado == egreso.idempleado"
                >
                  <input
                    class="form-control text-center cantidad-despacho"
                    ref="cant"
                    style="font-size: 16px"
                    v-model="despacho.cantidad"
                    type="number"
                  />
                </span>
                <span v-else>{{egreso.cantidad}}</span>
              </span>
              <span v-else></span>
            </td>
            <td class="text-center" style="font-size: 16px">
              <span v-if="egreso.futuro">
                <span
                  v-if="statusForm && despacho.idmaterial == egreso.idmaterial && despacho.fecha == egreso.fecha
                            && despacho.presente == egreso.presente && despacho.futuro == egreso.futuro
                            && despacho.reemplazo == egreso.reemplazo && despacho.idempleado == egreso.idempleado"
                >
                  <input
                    class="form-control text-center cantidad-despacho"
                    ref="cant"
                    style="font-size: 16px"
                    v-model="despacho.cantidad"
                    type="number"
                  />
                </span>
                <span v-else>{{egreso.cantidad}}</span>
              </span>
              <span v-else></span>
            </td>
          </tr>
        </template>
        <template v-for="(egreso, index) in dato_enfunde">
          <tr v-if="('enfunde' in dato_enfunde[index])" class="table-sm" :key="index">
            <td style="width: 10%" class="text-center"></td>
            <td class="text-center" style="font-size: 16px">{{egreso.fecha}}</td>
            <td style="font-size: 16px">
              <b>{{egreso.desmaterial}}</b>
            </td>
            <td class="text-center"></td>
            <td class="text-center" style="font-size: 16px">
              <span v-if="egreso.presente">
                <b style="color: darkgreen">{{egreso.cantidad}}</b>
              </span>
            </td>
            <td class="text-center" style="font-size: 16px">
              <span v-if="egreso.futuro">
                <b style="color: darkgreen">{{egreso.cantidad}}</b>
              </span>
            </td>
          </tr>
        </template>
      </tbody>
    </table>
    <div class="form-row ml-2 mr-2 mb-3">
      <input
        id="detalle-total"
        type="text"
        style="font-size: 22px"
        class="form-control form-control-lg bg-white text-right"
        disabled
      />
    </div>
  </div>
</template>

<script>
import "bootstrap-daterangepicker/daterangepicker.css";
import moment from "moment/moment";
import SweetAlert from "sweetalert2/src/sweetalert2";
import BootstrapVue from "bootstrap-vue";

Vue.use(BootstrapVue);

const Swal = SweetAlert;
export default {
  data() {
    return {
      hacienda: $("#id-hacienda").val(),
      fecha: $("#fecha").val(),
      empleado: "",
      statusForm: false,
      despacho: {
        fecha: "",
        idmaterial: 0,
        desmaterial: "",
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
      saldo: null
    };
  },
  mounted() {
    var self = this;
    moment.locale("es");

    $("#producto").attr("disabled", true);
    $("#producto").selectpicker("refresh");

    $("#nombre-producto").attr("disabled", true);
    $("#cantidad").attr("disabled", true);
    $("#add-despacho").attr("disabled", true);

    /*self.getAutocompleteEmpleado();*/
    self.getAutocompleteEmpleadoReemplazo();
    self.getAutocompleteProducto();

    $("#detalle-total").val(self.totalizaDespacho());

    $("div.easy-autocomplete").removeAttr("style");

    /*$('input[name="fecha"]').daterangepicker({
             opens: 'center',
             startDate: moment().startOf('day'),
             singleDatePicker: true,
             showDropdowns: true,
             });

             $('input[name="fecha"]').on({
             change: function () {
             self.fecha = $(this).val();
             }
             });*/

    if ($("#nombre-empleado").val() != null) {
      self.empleado = $("#nombre-empleado").val();
      $("#producto").attr("disabled", false);
      $("#producto").selectpicker("refresh");

      $("#nombre-producto").attr("disabled", false);
      $("#nombre-producto").focus();
      $("#cantidad").attr("disabled", false);
      $("#cantidad").focus();

      if (self.empleado != "") {
        self.getDataEmpleado(self.empleado, $("#semana").val(), self.hacienda);
        self.getSaldoPendienteEmpleado(
          self.empleado,
          $("#producto option:selected").val(),
          $("#semana").val()
        );
        $("#add-despacho").attr("disabled", false);
      }
    }

    $("#nombre-empleado").on("change", function() {
      self.empleado = $(this).val();
      let sVal = $("#producto option:first").val();
      $("#producto").val(sVal);
      $("#producto").attr("disabled", false);
      $("#producto").selectpicker("refresh");

      $("#nombre-producto").attr("disabled", false);
      $("#nombre-producto").focus();
      $("#cantidad").attr("disabled", false);
      $("#cantidad").focus();

      if (self.empleado != "") {
        self.getDataEmpleado(self.empleado, $("#semana").val(), self.hacienda);
        self.getSaldoPendienteEmpleado(
          self.empleado,
          $("#producto option:selected").val(),
          $("#semana").val()
        );
        $("#add-despacho").attr("disabled", false);
      }
    });

    $("#cantidad").on("keydown", function(e) {
      if (e.which == 13) {
        e.preventDefault();
        $("#add-despacho").click();
      }
    });

    $("#id-hacienda").on({
      change: function(e) {
        self.hacienda = $(this).val();
      }
    });

    $("#btn-nuevo").on({
      click: function(e) {
        self.resetForm();
        $("#nombre-empleado").val("");
        $("#producto").attr("disabled", true);
        $("#producto").selectpicker("refresh");

        $("#nombre-producto").attr("disabled", true);
        $("#cantidad").attr("disabled", true);
        $("#add-despacho").attr("disabled", true);

        $("#detalle-total").val(self.totalizaDespacho());
      }
    });

    $("#producto").on({
      change: function() {
        $("#codigo-producto").val($(this).val());
        $("#cantidad").attr("disabled", false);
        $("#add-despacho").attr("disabled", false);
        $("#cantidad").focus();

        if (self.empleado != "") {
          self.getSaldoPendienteEmpleado(
            self.empleado,
            $(this).val(),
            $("#semana").val()
          );
        }
      }
    });

    $("input[name=status-semana]").on("change", function() {
      var radios = $(this);
      if (radios.filter("[value=futuro]").is(":checked")) {
        if (self.enfunde) {
          /*Swal.fire({
                         position: 'center',
                         type: 'info',
                         title: 'Lotero tiene enfunde reportado: ' + self.enfunde.total_pre,
                         showConfirmButton: false,
                         timer: 1500
                         });*/
          //Logica para saldo de fundas en la semana
          //Traer el total de enfunde presente y futuro
          //Restar por tipo de funda segun el estado del despacho ya sea presente o futuro
          //el status es para tener el saldo de las fundas OJO
        } else {
          $("input[name=status-semana][value=presente]").prop("checked", true);
          Swal.fire({
            position: "center",
            type: "info",
            title: "Lotero no tiene enfunde reportado",
            showConfirmButton: false,
            timer: 1500
          });
        }
      }
    });

    $("#add-despacho").on({
      click: function(e) {
        var fecha,
          des_material,
          idmaterial,
          cantidad,
          presente = 0,
          futuro = 0;

        var radios = $("input[name=status-semana]");

        fecha = $("#fecha");
        idmaterial = $("#codigo-producto");
        des_material = $("#producto option:selected");
        cantidad = $("#cantidad");
        presente = radios.filter("[value=presente]").is(":checked");
        futuro = radios.filter("[value=futuro]").is(":checked");

        if (des_material.val() == "") {
          return;
        }

        if (cantidad.val() == 0 || cantidad.val() == "") {
          return;
        }

        let egreso = {
          fecha: fecha.val(),
          idmaterial: des_material.val(),
          desmaterial: des_material.text(),
          reemplazo: 0,
          idempleado: 0,
          empleado: "",
          cantidad: cantidad.val(),
          presente: presente,
          futuro: futuro,
          estado: 1
        };

        if ($("#id-reemplazo").prop("checked")) {
          $("#emp-reemplazo").modal("show");
          guardaRelevo(egreso);

          idmaterial.val("");
          cantidad.val("");

          $("#id-empleado-reemplazo").val("");
          $("#nombre-empleado-reemplazo").val("");
        } else {
          self.addDespacho(egreso);
          $("#detalle-total").val(self.totalizaDespacho());
          idmaterial.val("");
          cantidad.val("");

          $("#id-empleado-reemplazo").val("");
          $("#nombre-empleado-reemplazo").val("");
        }
      }
    });

    function guardaRelevo(egreso) {
      $("#btn-save-reemplazo").one("click", function() {
        if ($("#id-empleado-reemplazo").val() != "") {
          egreso.reemplazo = 1;
          egreso.idempleado = +$("#id-empleado-reemplazo").val();
          egreso.empleado = $("#nombre-empleado-reemplazo").val();
          self.addDespacho(egreso);
          $("#detalle-total").val(self.totalizaDespacho());
          $("#emp-reemplazo").modal("hide");
        }
      });
    }

    $("#btn-save").on({
      click: function(e) {
        e.preventDefault();

        if (self.empleado == "") {
          alert("Seleccione un empleado");
          return;
        }

        if (self.fecha == "") {
          alert("Debe registrar una fecha");
          return;
        }

        if (self.despachos.length == 0) {
          alert("Debe registrar algun despacho");
          return;
        }

        let data = {
          fecha: self.fecha,
          semana: $("#semana").val(),
          hacienda: self.hacienda,
          idempleado: self.empleado,
          total: self.totalizar(),
          saldo: 0,
          status: 0,
          despachos: self.despachos
        };

        $("#btn-save").attr("disabled", true);

        axios
          .post("/sistema/enfunde/despacho/save", {
            json: JSON.stringify(data)
          })
          .then(response => {
            let respuesta = response.data;
            let tipo = "danger",
              title = "Error al intentar guardar el registro";
            if (respuesta.code == 202) {
              self.resetForm();
              $("#detalle-total").val(self.totalizaDespacho());
              Swal.fire({
                position: "top-end",
                type: respuesta.status,
                title: respuesta.message,
                showConfirmButton: false,
                timer: 1500
              });
              $("#btn-save").attr("disabled", false);

              $("#nombre-empleado").html(respuesta.render.html);
              $("#nombre-empleado").val("");
              $("#nombre-empleado").selectpicker("refresh");
            }
          })
          .catch(error => {
            console.log(error.response);
          });
      }
    });
  },
  updated: function() {
    var self = this;
  },
  methods: {
    addDespacho: function(data) {
      if (!this.existeDespacho(data)) {
        this.despachos.push(data);
        return true;
      } else {
        return this.editDespacho(data);
      }
    },
    existeDespacho: function(data) {
      for (var i in this.despachos) {
        let self = this.despachos[i];
        if (data.idmaterial == self.idmaterial) {
          if (data.fecha == self.fecha) {
            if (data.presente == self.presente && data.futuro == self.futuro) {
              if (
                data.reemplazo == self.reemplazo &&
                data.idempleado == self.idempleado
              ) {
                return true;
              }
            }
          }
        }
      }
      return false;
    },
    editDespacho: function(data) {
      var enfunde = this.enfunde;
      for (var i in this.despachos) {
        let self = this.despachos[i];
        if (data.idmaterial == self.idmaterial) {
          if (data.fecha == self.fecha) {
            if (data.presente == self.presente && data.futuro == self.futuro) {
              if (
                data.reemplazo == self.reemplazo &&
                data.idempleado == self.idempleado
              ) {
                if (
                  (data.presente && !enfunde) ||
                  (data.futuro && enfunde && enfunde.total_fut == 0)
                ) {
                  self.cantidad = +self.cantidad + +data.cantidad;
                  $("#detalle-total").val(this.totalizaDespacho());
                  return true;
                }
              }
            }
          }
        }
      }
      return false;
    },
    material_unico: function(presente = true, idmaterial) {
      let resp = true;
      let no_es_unico = [];
      if (this.despachos.length > 0) {
        for (var i in this.despachos) {
          if (presente) {
            if (this.despachos[i].presente) {
              if (this.despachos[i].idmaterial != idmaterial) {
                no_es_unico.push(false);
              }
            }
          } else {
            if (this.despachos[i].futuro) {
              if (this.despachos[i].idmaterial != idmaterial) {
                no_es_unico.push(false);
              }
            }
          }
        }
      }

      if (no_es_unico.length > 0) {
        resp = false;
      }

      return resp;
    },
    deleteDespacho: function(index) {
      let self = this;
      let alerta = "Eliminado!";
      let mensaje = "Registro eliminado con éxito!";
      Swal.fire(this.datasweetalert()).then(result => {
        if (result.value) {
          if ("id" in self.despachos[index]) {
            axios
              .delete(
                `/sistema/enfunde/despacho/delete/${self.empleado}/${$(
                  "#semana"
                ).val()}/${self.hacienda}/${self.despachos[index].id}`
              )
              .then(response => {
                if (response.data.code == 200) {
                  this.despachos.splice(index, 1);
                  Swal.fire(
                    alerta,
                    response.data.message,
                    response.data.status
                  );
                  $("#detalle-total").val(this.totalizaDespacho());
                } else {
                  Swal.fire(
                    "Error " + response.data.code,
                    response.data.message,
                    response.data.status
                  );
                }
              });
          } else {
            Swal.fire(alerta, mensaje, "success");
            this.despachos.splice(index, 1);
            $("#detalle-total").val(this.totalizaDespacho());
            return true;
          }
        }

        if (self.despachos.length == 0) {
          self.resetForm();
        }
      });
      return false;
    },
    totalizar: function() {
      var total = 0;
      for (var i in this.despachos) {
        if (!("enfunde" in this.despachos[i])) {
          total += parseInt(this.despachos[i].cantidad);
        }
      }
      return total;
    },
    totalPresente: function() {
      var total = 0;
      for (var i in this.despachos) {
        if (!("enfunde" in this.despachos[i])) {
          if (this.despachos[i].presente) {
            total += parseInt(this.despachos[i].cantidad);
          }
        }
      }
      return total;
    },
    totalFuturo: function() {
      var total = 0;
      for (var i in this.despachos) {
        if (!("enfunde" in this.despachos[i])) {
          if (this.despachos[i].futuro) {
            total += parseInt(this.despachos[i].cantidad);
          }
        }
      }
      return total;
    },
    totalizaDespacho: function() {
      let string = "TOTAL DESPACHO SEMANA " + $("#semana").val() + ": ";
      let total = 0;
      let presente = 0;
      let futuro = 0;
      for (var i in this.despachos) {
        if (!("enfunde" in this.despachos[i])) {
          if (this.despachos[i].presente) {
            presente += parseInt(this.despachos[i].cantidad);
          } else {
            futuro += parseInt(this.despachos[i].cantidad);
          }
        }
      }
      total = +presente + +futuro;
      return (
        string + total + " / PRESENTE: " + presente + " / FUTURO: " + futuro
      );
    },
    editForm: function(index) {
      this.despacho.fecha = this.despachos[index].fecha;
      this.despacho.idmaterial = this.despachos[index].idmaterial;
      this.despacho.reemplazo = this.despachos[index].reemplazo;
      this.despacho.idempleado = this.despachos[index].idempleado;
      this.despacho.cantidad = this.despachos[index].cantidad;
      this.despacho.presente = this.despachos[index].presente;
      this.despacho.futuro = this.despachos[index].futuro;
      this.statusForm = true;
    },
    saveForm: function(index) {
      if (this.despacho.cantidad > 0 && this.despacho.cantidad != "") {
        this.despachos[index].cantidad = this.despacho.cantidad;
      } else {
        this.despacho.cantidad = this.despachos[index].cantidad;
      }

      this.despacho.idmaterial = "";
      this.despacho.cantidad = 0;
      this.statusForm = false;
      $("#detalle-total").val(this.totalizaDespacho());
    },
    getAutocompleteEmpleadoReemplazo: function() {
      var object = this;
      var semana = $("#semana").val();
      var options = {
        url: function(criterio) {
          return `/api/empleados/${criterio}`;
        },
        getValue: "nombre",
        ajaxSettings: {
          method: "GET",
          dataType: "json"
        },
        theme: "green-light",
        list: {
          maxNumberOfElements: 5,
          match: {
            enabled: true
          },
          onClickEvent: function() {
            var data = $("#nombre-empleado-reemplazo").getSelectedItemData();
            $("#id-empleado-reemplazo").val(data.codigo);
          },
          onKeyEnterEvent: function() {
            var data = $("#nombre-empleado-reemplazo").getSelectedItemData();
            $("#id-empleado-reemplazo").val(data.codigo);
          }
        }
      };

      $("#nombre-empleado-reemplazo").easyAutocomplete(options);
      $("#nombre-empleado-reemplazo").on({
        change: function() {
          if ($(this).val() == "") {
            $("#id-empleado-reemplazo").val("");
          }
        }
      });
    },
    getAutocompleteProducto: function() {
      let self = this;
      var options = {
        url: function(criterio) {
          var bodega = $("#bodega").val();

          if (bodega == "") {
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
          method: "GET",
          dataType: "json"
        },
        theme: "green-light",
        list: {
          maxNumberOfElements: 5,
          match: {
            enabled: true
          },
          onClickEvent: function() {
            var data = $("#nombre-producto").getSelectedItemData();
            $("#codigo-producto").val(data.codigo);
            $("#cantidad").attr("disabled", false);
            $("#add-despacho").attr("disabled", false);
            $("#cantidad").focus();

            self.getSaldoPendienteEmpleado(
              self.empleado,
              data.codigo,
              $("#semana").val()
            );
          },
          onKeyEnterEvent: function() {
            var data = $("#nombre-producto").getSelectedItemData();
            $("#codigo-producto").val(data.codigo);
            $("#cantidad").attr("disabled", false);
            $("#add-despacho").attr("disabled", false);
            $("#cantidad").focus();

            self.getSaldoPendienteEmpleado(
              self.empleado,
              data.codigo,
              $("#semana").val()
            );
          }
        }
      };

      $("#nombre-producto").easyAutocomplete(options);
      $("#nombre-producto").on({
        change: function() {
          if ($(this).val() == "") {
            $("#codigo-producto").val("");
          }
        }
      });
    },
    getDataEmpleado: function(empleado, semana, hacienda) {
      let self = this;
      $("input[name=status-semana][value=futuro]").prop("checked", false);
      $("input[name=status-semana][value=presente]").prop("disabled", false);
      $("input[name=status-semana][value=presente]").prop("checked", true);
      $("#id-reemplazo").prop("checked", false);

      self.despachos = [];
      if (empleado != null) {
        axios
          .get(`/sistema/enfunde/despacho/${empleado}/${semana}/${hacienda}/1`)
          .then(response => {
            if (response.data) {
              self.enfunde = response.data.lotero.enfunde;
              for (var x in response.data.egresos) {
                let egreso = {
                  id: response.data.egresos[x].idhash,
                  fecha: response.data.egresos[x].fecha.toString("dd/MM/yyyy"),
                  idmaterial: response.data.egresos[x].idmaterial,
                  desmaterial: response.data.egresos[x].get_material.nombre,
                  reemplazo: +response.data.egresos[x].reemplazo,
                  idempleado: +response.data.egresos[x].idempleado,
                  empleado:
                    response.data.egresos[x].nom_reemplazo != null
                      ? response.data.egresos[x].nom_reemplazo.nombre
                      : "",
                  cantidad: +response.data.egresos[x].cantidad,
                  presente: +response.data.egresos[x].presente,
                  futuro: +response.data.egresos[x].futuro,
                  estado: response.data.egresos[x].status
                };

                self.despachos.push(egreso);
              }

              if (self.enfunde) {
                $("input[name=status-semana][value=futuro]").prop(
                  "checked",
                  true
                );
                $("input[name=status-semana][value=presente]").prop(
                  "disabled",
                  true
                );
              }

              self.dato_enfunde = [];
              self.getEnfunde(self.enfunde, self.dato_enfunde);
            } else {
              Swal.fire({
                position: "center",
                type: "info",
                title: "Lotero no tiene despachos",
                showConfirmButton: false,
                timer: 1500
              });
            }
          });
      }
    },
    getSaldoPendienteEmpleado: function(idempleado, idmaterial, semana) {
      let self = this;
      self.saldo = null;
      axios
        .get(
          `/api/enfunde/saldo_empleado/${idempleado}/${idmaterial}/${semana}`
        )
        .then(response => {
          if (response.data) {
            self.saldo = response.data;
          }
        });
    },
    getEnfunde: function(enfunde, array) {
      if (enfunde) {
        if (parseInt(enfunde.total_pre) > 0) {
          let tot_enfunde = {
            fecha: "",
            desmaterial: "TOTAL ENFUNDE PRESENTE",
            reemplazo: 0,
            idempleado: 0,
            empleado: "",
            cantidad: parseInt(enfunde.total_pre),
            presente: 1,
            futuro: 0,
            estado: 0,
            enfunde: 1
          };
          array.push(tot_enfunde);
        }
        if (parseInt(enfunde.total_fut) > 0) {
          let tot_enfunde = {
            fecha: "",
            desmaterial: "TOTAL ENFUNDE FUTURO",
            reemplazo: 0,
            idempleado: 0,
            empleado: "",
            cantidad: parseInt(enfunde.total_fut),
            presente: 0,
            futuro: 1,
            estado: 0,
            enfunde: 1
          };
          array.push(tot_enfunde);
        }
      }
    },
    resetData: function() {
      this.despacho.fecha = $("#fecha").val();
      this.despacho.idmaterial = "";
      this.despacho.des_material = "";
      this.despacho.cantidad = 0;
    },
    resetForm: function() {
      this.empleado = "";
      this.statusForm = false;
      this.saldo = null;
      this.despacho.fecha = $("#fecha").val();
      this.despacho.idmaterial = "";
      this.despacho.des_material = "";
      this.despacho.cantidad = 0;
      this.despacho.presente = 1;
      this.despacho.futuro = 0;
      this.despacho.estado = 1;
      this.despachos = [];
      this.dato_enfunde = [];
      this.enfunde = null;
      this.saldo = null;

      $("#codigo-empleado").val("");
      $("#codigo-producto").val("");
      $("#nombre-producto").val("");
      $("#cantidad").val("");

      let sVal = $("#producto option:first").val();
      $("#producto").val(sVal);
      $("#producto").selectpicker("refresh");

      $("input[name=status-semana][value=futuro]").prop("checked", false);
      $("input[name=status-semana][value=presente]").prop("disabled", false);
      $("input[name=status-semana][value=presente]").prop("checked", true);
      $("#id-reemplazo").prop("checked", false);
    },
    datasweetalert() {
      var data = {
        title: "Estas seguro de realizar esta acción?",
        text: "Tu no podras revertir esto!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Si, hazlo!"
      };
      return data;
    }
  }
};
</script>
