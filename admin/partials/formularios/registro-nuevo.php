<h2>Nuevo Registro</h2>
<?php
if($_GET["registro"] != "nuevo"){
    global $wpdb;
    $id = $_GET["registro"];
    $sql = "SELECT * FROM mm_registro INNER JOIN mm_cuenta ON mm_registro_cuenta_id = mm_cuenta_id INNER JOIN mm_empresa ON mm_cuenta_empresa_id = mm_empresa_id WHERE mm_registro_id = '$id'";
    $resultado = $wpdb->get_results($sql, OBJECT);
    $entrada = $resultado[0];
    $empresa = $entrada->mm_empresa_id;
    $cuenta = $entrada->mm_cuenta_id;
    $moneda = $entrada->mm_cuenta_moneda;
}
else{
    $empresa = $_GET["empresa"];
    $cuenta = $_GET["cuenta"];
    $moneda = $_GET["moneda"];
    $ingreso = $_GET["ingreso"];
}
SeleccionarEmpresa($empresa);
SeleccionarCuenta($cuenta);
SeleccionarMoneda($moneda);
?>
    
    

<label for="fecha">Fecha:</label>
<input type="date" name="fecha" id="fecha" class="form-control" value="<?php echo date("Y-m-d") ?>" required>
<br>
<div class="text text-center">
<label for="ingreso">Salida</label>
<label class="switch">
  <input type="checkbox" id="ingreso" name="ingreso" <?php echo $ingreso ?>>
  <span class="slider round"></span>
</label>
<label for="ingreso">Ingreso</label>
</div>
<label for="monto">Monto:</label>
<input type="number" step="0.01" name="monto" id="monto" class="form-control" required>

<?php SeleccionarCategoria(null, 1) ?>
<?php include("subcategoria-elegir.php") ?><br>

<label for="descripcion">Descripcion:</label>
<input type="text" name="descripcion" id="descripcion" class="form-control">
<label for="entidad">Entidad:</label>
<input type="text" name="entidad" id="entidad" class="form-control">
<label for="operacion">Operación:</label>
<input type="text" name="operacion" id="operacion" class="form-control">
<label for="foto">Foto:</label>
<input type="file" name="fileToUpload" id="fileToUpload" value="null" class="btn btn-info form-control">
<br>
