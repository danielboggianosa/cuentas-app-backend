<h2>Editar Registro</h2>
<?php
global $wpdb;
$sql = "SELECT * FROM mm_registro INNER JOIN mm_cuenta ON mm_registro_cuenta_id = mm_cuenta_id WHERE mm_registro_id = $registro";
$resultado = $wpdb->get_results($sql,OBJECT);
$entrada = $resultado[0];

$empresa = $entrada->mm_cuenta_empresa_id;
$cuenta = $entrada->mm_cuenta_id;
$moneda = $entrada->mm_registro_moneda;
$fecha = $entrada->mm_registro_fecha;
$monto = $entrada->mm_registro_monto;
($monto > 0) ? $ingreso = "checked" : $ingreso = null;
$categoria = $entrada->mm_registro_categoria_id;
$subcategoria = $entrada->mm_registro_subcategoria_id;
$descripcion = $entrada->mm_registro_descripcion;
$entidad = $entrada->mm_registro_entidad;
$operacion = $entrada->mm_registro_operacion;
$foto = $entrada->mm_registro_foto;
SeleccionarEmpresa($empresa, 1);
SeleccionarCuenta($cuenta, 1);
SeleccionarMoneda($moneda, 1);
//print_r($entrada);
?>    
    
<label for="fecha">Fecha:</label>
<input type="date" name="fecha" id="fecha" class="form-control" value="<?php echo $fecha; ?>" required><br>

<div class="text text-center">
<label for="ingreso">Salida</label>
<label class="switch">
  <input type="checkbox" id="ingreso" name="ingreso" <?php echo $ingreso; ?>>
  <span class="slider round"></span>
</label>
<label for="ingreso">Ingreso</label>
</div>

<br>
<label for="monto">Monto:</label>
<input type="number" step="0.01" name="monto" id="monto" class="form-control" value="<?php echo $monto ?>" required><p>*mantener el signo negativo (-) de ser el caso</p><br>

<?php SeleccionarCategoria($categoria, null, 1) ?>
<?php SeleccionarSubcategoria($subcategoria, null, 1) ?>

<label for="descripcion">Descripcion:</label>
<input type="text" name="descripcion" id="descripcion" value="<?php echo $descripcion ?>" class="form-control">
<label for="entidad">Entidad:</label>
<input type="text" name="entidad" id="entidad" value="<?php echo $entidad ?>" class="form-control">
<label for="operacion">Operación:</label>
<input type="text" name="operacion" id="operacion" value="<?php echo $operacion ?>" class="form-control">
<label for="fileToUpload">Foto:<br>
<div class="imagen">
<p>Cambiar Foto</p>
<img src="<?php echo $foto ?>" width="150">
</div>
</label>
<input type="file" name="fileToUpload" id="fileToUpload" class="hidden">
<input type="hidden" name="editar" value="no">
<br>
<style>
    .imagen{
        display:block;
    }
    .imagen p{
        display: none;
        width: 150px;
        height: 50px;
        background-color: rgba(0,0,0,0.5);
        color: white;
        position: absolute;
        text-align: center;
        text-transform: uppercase;
    }
    .imagen:hover p{
        display: block;
    }
</style>