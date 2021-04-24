<?php
$folder = "/wordpress";
require_once($_SERVER['DOCUMENT_ROOT'] . $folder . '/wp-config.php');
require_once($_SERVER['DOCUMENT_ROOT'] . $folder . '/wp-load.php');

if(isset($_POST["cuenta"])){
    global $wpdb;
    $cuenta = $_POST["cuenta"];
    $sql = "SELECT * FROM mm_cuenta WHERE mm_cuenta_id = $cuenta";
    //echo $sql;
    $resultados = $wpdb->get_results( $sql , OBJECT );
    $row = $resultados[0];
?><option value="<?php echo $row->mm_cuenta_moneda; ?>"><?php echo $row->mm_cuenta_moneda; ?></option><?php
}
?>