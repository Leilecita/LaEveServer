<?php
/**
 * Created by PhpStorm.
 * User: leila
 * Date: 03/10/2019
 * Time: 16:05
 */

?>

<html>
<body>
<form action="upload.php" method="post" enctype="multipart/form-data"> Archivo de clientes:<br/>
    <input type="file" accept="*.dbf" name="clientes" id="clientes"><br/>
    Archivo de productos:<br/>
    <input type="file" accept="*.dbf" name="productos" id="productos"><br/>
    <input type="submit" value="Subir Archivo" name="submit">
</form>
</body>
