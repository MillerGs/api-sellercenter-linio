<?php
/**
 * Created by PhpStorm.
 * User: millergs
 * web: http://millergomez.com/
 * Date: 28/12/15
 * Time: 09:25 PM
 */


if ( !empty($_POST['action']) && !empty($_POST['output_format']) ) {

    $url = '';

    // No prestes atención a este comunicado.
    // Es sólo necesario si la zona horaria en php.ini no están correctamente ajustadas.
    //date_default_timezone_set("UTC");
    date_default_timezone_set("America/Lima");

    // La hora actual. Necesaria para crear el parámetro de marca de hora a continuación.
    $now = new DateTime();

    // Los parámetros para nuestra petición GET. Estos se consiguen firmado.
    $parameters = array(
        // El ID de usuario para el que estamos haciendo la llamada.
        'UserID' => '',

        // La versión de la API. Actualmente debe ser 1.0
        'Version' => '1.0',

        // El método API para llamar.
        'Action' => $_POST['action'],

        // El formato del resultado.
        'Format' => $_POST['output_format'],

        // La hora actual formato de ISO8601
        'Timestamp' => $now->format(DateTime::ISO8601)
    );

    // Ordenar parámetros por nombre.
    ksort($parameters);

    // URL codificar los parámetros.
    $encoded = array();
    foreach ( $parameters as $name => $value ) {
        $encoded[] = rawurlencode($name) . '=' . rawurlencode($value);
    }

    // Concatenar los parámetros ordenados y URL codificadas en una cadena.
    $concatenated = implode('&', $encoded);

    // La clave API para el usuario como genera en el Centro GUI vendedor.
    // Debe ser una clave de API asociado con el parámetro ID de usuario.
    $api_key = '';

    // Calcular firma y añadirlo a los parámetros.
    $parameters['Signature'] = rawurlencode(hash_hmac('sha256', $concatenated, $api_key, false));

    // Armando consulta
    $api_url = 'https://sellercenter-api.linio.com.pe?';
    $url = $api_url.$concatenated.'&Signature='.$parameters['Signature'];


    // Open Curl connection
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    // Save response to the variable $data
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $data = curl_exec($ch);
    // Close Curl connection
    curl_close($ch);

    echo $data;

} else {
    echo '<div style="color: red"> No ha seleccionado una de las acciones en el formulario</div>';
}