    <h2>Nueva Cuenta</h2>
<?php
$empresa = $_GET["empresa"];
SeleccionarEmpresa($empresa);
?>
    <label for="nombre">Nombre:</label>
    <input type="text" name="nombre" id="nombre" class="form-control">
    
    <label for="banco">Banco:</label>
    <input type="text" name="banco" id="banco" class="form-control">
    
    <label for="moneda">Moneda:</label>
    <select name="moneda" id="moneda" class="form-control" required>
        <option>Elegir una moneda</option>
        <option value="PEN">SOLES (PEN)</option>
        <option value="USD">DÓLARES (USD)</option>
        <option value="EUR">EUROS (EUR)</option>
    </select>
    
    <label for="numero">Número:</label>
    <input type="text" name="numero" id="numero" class="form-control">
        
    <label for="cci">CCI:</label>
    <input type="text" name="cci" id="cci" class="form-control">
    
    <label for="notas">Notas:</label>
    <textarea name="notas" id="notas" class="form-control"></textarea>
    
    <label for="foto">Foto:</label>
    <input type="file" name="fileToUpload" id="fileToUpload" value="null" class="btn btn-info form-control" ><br>