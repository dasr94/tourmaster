<?php

get_header();
global $wpdb;
$id_tour = "30734";
$tabla = $wpdb->base_prefix."posts";
$query = "SELECT post_author FROM $tabla where  ID = '$id_tour' ";
$res = $wpdb->get_row($query);
$guia = $res->post_author;

$array = [

    "hola" => "hi",
    "guia" => $guia
];

echo json_encode($array);

?>