<?php
$folder = "/wordpress";
require_once($_SERVER['DOCUMENT_ROOT'] . $folder . '/wp-config.php');
require_once($_SERVER['DOCUMENT_ROOT'] . $folder . '/wp-load.php');

if(isset($_POST["categoria"])){
    global $wpdb;
    $categoria = $_POST["categoria"];
    $sql = "SELECT * FROM mm_subcategoria WHERE mm_subcategoria_categoria_id = $categoria";
    //echo $sql;
    $resultados = $wpdb->get_results( $sql , OBJECT );
    foreach($resultados as $row){
        ?><option value="<?php echo $row->mm_subcategoria_id ?>"><?php echo $row->mm_subcategoria_nombre ?></option><?php
    }
    ?><option value="nuevo">Nueva subcategoria...</option><?php
}else{
}    
?>