<h2>Nueva Transferencia</h2>
<?php
global $wpdb;
$empresa = $_GET["empresa"];
$cuenta = $_GET["cuenta"];
$moneda = $_GET["moneda"];
SeleccionarEmpresa($empresa);
?>
<label for="monto">Monto:</label>
<input type="number" step="0.01" name="monto" id="monto" class="form-control">

<label for="origen">Cuenta de origen:</label>
<select name='origen' id='origen' class='form-control' required>
    <option>Elegir una Cuenta</option>
<?php
        $sql="SELECT mm_cuenta_id as id, mm_cuenta_nombre as nombre, mm_cuenta_banco as banco, mm_cuenta_moneda as moneda FROM mm_cuenta WHERE mm_cuenta_empresa_id = $empresa";
        $results = $wpdb->get_results( $sql , OBJECT );
            for($i=0;$i<sizeof($results);$i++){
                $register = $results[$i];
        ?>
        <option value="<?php echo $register->id; ?>"><?php echo $register->moneda." | ".$register->banco." | ".$register->nombre ?></option>
<?php        }
?>
</select>
<label for="destino">Cuenta de destino:</label>
<select name='destino' id='destino' class='form-control' required>
    <option>Elegir una Cuenta</option>
<?php for($i=0;$i<sizeof($results);$i++){
        $register = $results[$i];
        ?>
        <option value="<?php echo $register->id; ?>"><?php echo $register->moneda." | ".$register->banco." | ".$register->nombre ?></option>
<?php        }
?>
</select>
<label for="tc">Tipo de Cambio:</label>
<input type="number" step="0.001" name="tc" id="tc" value="1" class="form-control">

<label for="fecha">Fecha:</label>
<input type="date" name="fecha" id="fecha" class="form-control" value="<?php echo date("Y-m-d") ?>"><br>

