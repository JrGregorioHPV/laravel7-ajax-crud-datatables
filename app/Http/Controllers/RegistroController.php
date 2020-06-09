<?php

namespace App\Http\Controllers;

use App\Registro;
use Illuminate\Http\Request;
use DataTables;
use Validator;

class RegistroController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){
          $data = Registro::latest()->get();
          return DataTables::of($data)
                  ->addColumn('action', function($data){
                    $boton = '<button type="button" name="edit" id="'.$data->id.'" class="btn-editar btn btn-primary btn-sm">Editar</button>';
                    $boton .= '&nbsp;&nbsp;&nbsp;<button type="button" name="eliminar" id="'.$data->id.'" class="btn-eliminar btn btn-danger btn-sm">Eliminar</button>';
                    return $boton;
                  })->rawColumns(['action'])->make(true);
        }
        return view('registro');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $reglas = array(
            '_Nombre'   => 'required',
            '_Apellido' => 'required',
            '_Edad'   => 'required'
        );

        $error = Validator::make($request->all(), $reglas);

        if ($error->fails()) {
            return response()->json([
                'errors' => $error->errors()->all()]);
        }

        $datos = array(
            'nombre'   => $request->_Nombre,
            'apellido' => $request->_Apellido,
            'edad' => $request->_Edad
        );

        Registro::create($datos);

        return response()->json([
                'success' => 'Datos agregados con exito.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Registro  $registro
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        $datis = Registro::findOrFail($id);
        return response()->json($datis);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Registro  $registro
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(request()->ajax()){
            $data = Registro::findOrFail($id);
            return response()->json(['datitos' => $data]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Registro  $registro
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $reglas = array(
            '_Nombre'   => 'required',
            '_Apellido' => 'required',
            '_Edad'   => 'required',
        );

        $error = Validator::make($request->all(), $reglas);

        if ($error->fails()) {
            return response()->json([
                'errors' => $error->errors()->all()]);
        }

        $datos = array(
            'nombre'   => $request->_Nombre,
            'apellido' => $request->_Apellido,
            'edad' => $request->_Edad
        );

        Registro::whereId($request->hidden_id)->update($datos);

        return response()->json([
                'success' => 'Datos modificados con exito.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Registro  $registro
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Registro::find($id);
        $data->delete();

        return response()->json(['success'=>'Product deleted successfully.']);
    }
}
