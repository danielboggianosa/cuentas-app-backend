<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       www.danielboggiano.com
 * @since      1.0.0
 *
 * @package    Cuentas
 * @subpackage Cuentas/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div id="wpbody-content">
<div class="wrap impresion">

    <h2 class="bg-info">Registros</h2>
<?php
    global $wpdb;
    if(isset($_GET["registro"])){
        $registro = $_GET["registro"];
        $cuenta = $_GET["cuenta"];
        $empresa = $_GET["empresa"];
        $editar = $_POST["editar"];
        $eliminar = $_POST["eliminar"];
        $eliminarfoto = $_POST["eliminarfoto"];
        $agregar = $_POST["AgregarNuevo"];

        if(isset($_POST["actualizar"])){
            $categoria = $_POST["categoria"];
            $subcategoria = $_POST["subcategoria"];
            $nueva_categoria = AgregarCategoria($categoria);
            $nueva_subcategoria = AgregarSubcategoria($subcategoria, $nueva_categoria);
            ActualizarRegistro(
                $registro,
                array(
                    $tabla3."_"."cuenta_id" => $_POST["cuenta"],
                    $tabla3."_"."fecha" => $_POST["fecha"],
                    $tabla3."_"."categoria_id" => $nueva_categoria,
                    $tabla3."_"."subcategoria_id" => $nueva_subcategoria,
                    $tabla3."_"."descripcion" => $_POST["descripcion"],
                    $tabla3."_"."entidad" => $_POST["entidad"],
                    $tabla3."_"."operacion" => $_POST["operacion"],
                    $tabla3."_"."monto" => $_POST["monto"]
                ), $_POST["moneda"]
            );
            if(isset($_POST["fileToUpload"])){
                ActualizarFoto($tabla3, $registro);
            }
        }
        if($eliminarfoto == "foto"){
            EliminarFoto($tabla3,$registro);
        }
        if($eliminar == "registro"){
            EliminarRegistro($registro);
        }
        
        if($registro == "nuevo"){
            if(isset($_POST["guardar"])){
                $categoria = $_POST["categoria"];
                $subcategoria = $_POST["subcategoria"];
                $nueva_categoria = AgregarCategoria($categoria);
                $nueva_subcategoria = AgregarSubcategoria($subcategoria, $nueva_categoria);

                if($_POST["ingreso"] != true){
                    $monto = $_POST["monto"] * (-1);
                }
                else{
                    $monto = $_POST["monto"];
                }
                AgregarNuevoRegistro(
                    array(
                        $tabla3."_"."cuenta_id" => $_POST["cuenta"],
                        $tabla3."_"."fecha" => $_POST["fecha"],
                        $tabla3."_"."categoria_id" => $nueva_categoria,
                        $tabla3."_"."subcategoria_id" => $nueva_subcategoria,
                        $tabla3."_"."descripcion" => $_POST["descripcion"],
                        $tabla3."_"."entidad" => $_POST["entidad"],
                        $tabla3."_"."operacion" => $_POST["operacion"],
                        $tabla3."_"."monto" => $monto
                    ), $_GET["moneda"]
                );
                if(isset($_POST["fecha"])){AgregarNuevoRegistro();}
            }
            else{//echo "Registro $registro Agregado"; 
                AgregarNuevoRegistro();
            }
        }
        elseif($registro == "transferencia"){
            if(isset($_POST["guardar"])){
                $cuenta1 = $_POST['origen'];
                $cuenta2 = $_POST['destino'];
                $sql1 = "SELECT mm_cuenta_moneda as moneda FROM mm_cuenta WHERE mm_cuenta_id = $cuenta1";
                $sql2 = "SELECT mm_cuenta_moneda as moneda FROM mm_cuenta WHERE mm_cuenta_id = $cuenta2";
                $resultado1 = $wpdb->get_results($sql1, OBJECT);
                $resultado2 = $wpdb->get_results($sql2, OBJECT);
                $m_origen = $resultado1[0]->moneda;
                $m_destino = $resultado2[0]->moneda;
                $monto2 = $_POST["monto"] * $_POST["tc"];
                echo "<h3>Origen</h3>";
                AgregarNuevoRegistro(
                    array(
                        $tabla3."_"."cuenta_id" => $cuenta1,
                        $tabla3."_"."fecha" => $_POST["fecha"],
                        $tabla3."_"."categoria_id" => 1,
                        $tabla3."_"."subcategoria_id" => 9,
                        $tabla3."_"."descripcion" => "Transferencia a $cuenta2",
                        $tabla3."_"."monto" => ($_POST["monto"] * (-1))
                    ), $m_origen
                );
                echo "<h3>Destino</h3>";
                AgregarNuevoRegistro(
                    array(
                        $tabla3."_"."cuenta_id" => $cuenta2,
                        $tabla3."_"."fecha" => $_POST["fecha"],
                        $tabla3."_"."categoria_id" => 1,
                        $tabla3."_"."subcategoria_id" => 9,
                        $tabla3."_"."descripcion" => "Transferencia de $cuenta1",
                        $tabla3."_"."monto" => $monto2
                    ), $m_destino
                );
            }
            else{ AgregarTransferencia(); }
        }
        else{
            if($editar == "yes"){
                EditarRegistro($registro);
            }
            else{ MostrarRegistro($registro); }
        }
    }
    else{
        if(isset($_GET["limit"])){
            if(isset($_GET["f_inicio"])){
                MostrarReporteRegistros($_GET["moneda"], $_GET["f_inicio"], $_GET["f_fin"]);
            }
            else{
                MostrarReporteRegistros($_GET["moneda"]);            
            }
        }
        else{
            //MostrarListaRegistros();
        }    
    }
?>
    
</div>
</div>