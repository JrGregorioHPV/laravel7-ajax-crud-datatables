<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CRUD Laravel con DataTables</title>
    
    <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.min.css') }}">
    
    
    <link rel="stylesheet" type="text/css" href="{{ asset('css/dataTables.bootstrap4.min.css') }}">
    
  </head>
  <body>

    <div class="container">
      <h1>CRUD Laravel 7 y DataTables</h1>
      <br>
      <div align="right">
        <button type="button" class="btn btn-success btn-sm" id="btn-crear" data-toggle="modal" data-target="#abrirModal">
  Crear
</button>

      </div>
      <br>
      <div class="table-responsive">
        <table id="tabla" class="table table-bordered table-striped table-hover">
          <thead>
            <tr>
              <th width="10%">Id</th>
              <th width="25%">Nombre</th>
              <th width="25%">Apellido</th>
              <th width="10%">Edad</th>
              <th width="35%">Acciones</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>


  </body>
<script src="{{ asset('js/jquery-3.3.1.min.js') }}" charset="utf-8"></script>
<script src="{{ asset('js/bootstrap.min.js') }}" charset="utf-8"></script>
  <script src="{{ asset('js/jquery.dataTables.min.js') }}" charset="utf-8"></script>
  <script src="{{ asset('js/dataTables.bootstrap4.min.js') }}" charset="utf-8"></script>

  <script type="text/javascript">
    $(document).ready(function(){

      $('#tabla').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        "order": [[ 0, "desc" ]],
        ajax: {
          url: '{{ route('registro.index') }}'
        },
        
        columns: [
          {data: 'id', name: 'id'},
          {data: 'nombre', name: 'nombre'},
          {data: 'apellido', name: 'apellido'},
          {data: 'edad', name: 'edad'},
          {data: 'action', name: 'action', orderable: false}
        ],

        // Colores en Filas
        rowCallback: function( row, data, dataIndex){
          // None
          if(data.edad <= 0){
             $(row).css('background-color', '#e0e0e0')
             .hover(function() {
                $(this).css("background-color","#bdbdbd")
             .mouseout(function(){
                $(this).css({"background-color":"#e0e0e0"});
                });
             });
          }
          // Danger
          if(data.edad >= 1 && data.edad <= 3){
             $(row).css('background-color', '#ef9a9a')
             .hover(function() {
                $(this).css("background-color","#e57373")
             .mouseout(function(){
                $(this).css({"background-color":"#ef9a9a"});
                });
             });
          }
          // Warning
          else if(data.edad >= 4 && data.edad <= 10){
            $(row).css('background-color', '#ffe0b2')
              .hover(function() {
                $(this).css("background-color","#ffcc80")
              .mouseout(function(){
                $(this).css({"background-color":"#ffe0b2"});
                });
              });
          }
          // Success
          else if(data.edad >= 11){
            $(row).css('background-color', '#c8e6c9')
              .hover(function() {
                $(this).css("background-color","#a5d6a7")
              .mouseout(function(){
                $(this).css({"background-color":"#c8e6c9"});
                });
              });
          }
        }
      });

      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });

      $('#btn-crear').click(function(){
        $('.modal-title').text('Agregar Nuevo Registros');
        $('#boton').val('Agregar');
        $('#accion').val('Add');
        $('#resultados').html('');
        $('#abrirModal').modal('show');

      });

      $('#Formulario').on('submit', function(e){
        e.preventDefault();
        var accion_Url = '';
        var metodo = '';

        if($('#accion').val() == 'Add'){
          accion_Url = "{{ route('registro.store') }}";
          metodo= 'POST';
        }

        if($('#accion').val() == 'Edit'){
          var id = $(this).attr('id');
          accion_Url = "{{ route('registro.update', '"+id+"') }}";
          metodo= 'PUT';
        }
        // Guardar
        $.ajax({
          url: accion_Url,
          method: metodo,
          data:$(this).serialize(),
          dataType: 'json',
          beforeSend:function(){
            $('#boton').val('Guardando...');
          },
          success:function(data){
            var html = '';
            if(data.errors){
              html = '<div class="alert alert-danger">';
              for (var count = 0; count < data.errors.length; count++) {
                html += '<p>' + data.errors[count] + '</p>';
              }
              html += '</div>';
            }
            if (data.success) {
              html = '<div class="alert alert-success">' + data.success + '</div>';
              $('#Formulario')[0].reset();
              $('#tabla').DataTable().ajax.reload();

            }
            $('#resultados').html(html);
            console.log($('#Formulario').serialize());
          },
          complete: function() {
            $('#boton').val('Guardado');
          }
        })
      });

      
      // Mostrar datos
      $('body').on('click', '.btn-editar', function(){
      var id = $(this).attr('id');
      $('#resultados').html('');
      $.get('crud/' + id +'/edit', function (data) {
      //$.ajax({
        
        //url: "{{ route('registro.index') }}" +'/' + id +'/edit',

        //dataType: 'json',
        //success:function(data){
          $('#_Nombre').val(data.datitos.nombre);
          $('#_Apellido').val(data.datitos.apellido);
          $('#_Edad').val(data.datitos.edad);
          $('#hidden_id').val(id);
          $('.modal-title').text('Editar Registro');
          $('#boton').val('Editar');
          $('#accion').val('Edit');
          $('#abrirModal').modal('show');
          console.log('apellido: '+data.datitos.apellido);
        })
    });

      //eliminar
      var registro_id;
      $(document).on('click', '.btn-eliminar', function(){
        registro_id = $(this).attr('id');
        $('#modalEliminar').modal('show');
      

      $('#ok-boton').click(function(){
        $.ajax({
          type: 'DELETE',
         
          url: "{{ route('registro.store') }}"+'/'+registro_id,
          
          beforeSend:function(){
            $('#ok-boton').text('Borrando...');
          },
          success:function(data){
            setTimeout(function(){
              $('#modalEliminar').modal('hide');
              $('#tabla').DataTable().ajax.reload();
              alert('Registro Borrado! ' + registro_id);
            }, 2000);
          },
          error: function (data) {
                console.error('Error:', data);
            }
        });
      });
      });
    });

    

  </script>
</html>

<!-- Modal -->
<div class="modal fade" id="abrirModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form method="post" id="Formulario" class="form-horizontal">
          @csrf
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Agregar Nuevo Registro</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <span id="resultados"></span>
        
          <div class="form-group">
            <label class="control-label col-md-4">Nombre: </label>
            <div class="col-md-8">
              <input type="text" name="_Nombre" id="_Nombre" class="form-control">
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-md-4">Apellido: </label>
            <div class="col-md-8">
              <input type="text" name="_Apellido" id="_Apellido" class="form-control">
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-md-4">Edad: </label>
            <div class="col-md-8">
              <input type="text" name="_Edad" id="_Edad" class="form-control">
            </div>
          </div>
          <br />

          <input type="text" name="accion" id="accion" value="Add">
          <input type="hidden" name="hidden_id" id="hidden_id">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
         <input type="submit" name="boton" id="boton" class="btn btn-warning" value="Agregar">
      </div>
      </form>
    </div>
  </div>
</div>



<!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalEliminar">
  Launch demo modal
</button>

<!-- Modal -->
<div class="modal fade" id="modalEliminar" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Confirmacion</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <h4 align="center" style="margin:0;">Desea eliminar este registro?</h4>  
      </div>
      <div class="modal-footer">
        <button type="button" id="" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" id="ok-boton" class="btn btn-danger">Eliminar</button>
      </div>
    </div>
  </div>
</div>
