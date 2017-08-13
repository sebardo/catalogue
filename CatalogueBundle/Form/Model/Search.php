<?php

namespace CatalogueBundle\Form\Model;

class Search
{
    
    static function color(){
        return array(
            'amarillo' => 'Amarillo',
            'azul' => 'Azul' ,
            'beige' => 'Beige',
            'blanco' => 'Blanco',
            'bronce' => 'Bronce',
            'dorado' => 'Dorado',
            'granate' => 'Granate',
            'gris' => 'Gris',
            'lila' => 'Lila',
            'marron' => 'Marrón',
            'naranja' => 'Naranja',
            'negro' => 'Negro',
            'plateado' => 'Plateado',
            'rojo' => 'Rojo',
            'rosa' => 'Rosa',
            'transparente' => 'Transparente',
            'verde' => 'Verde'
       );
    }
           
        
    static function tamano(){
        return  array(
            'estrecha' => 'Cabeza estrecha',
            'ancha' => 'Cabeza ancha'
            );
    }
            
        
    static function material(){
        return array(
            'aluminio' => 'Aluminio',
            'carbon' => 'Carbón',
            'carton' =>  'Cartón',
            'cuero' => 'Cuero',
            'madera' => 'Madera',
            'metal' => 'Metal',
            'pasta' => 'Pasta',
            'piedra' => 'Piedra',
            'titanio' => 'Titanio'
            );
    }
            
    static function montura(){
        return array(
            'montura_aire' => 'Montura al aire',
            'montura_completa' => 'Montura completa',
            'montura_mediana' => 'Montura mediana'
        );
    }
    
    static function pantalla(){
        return array(
            'piloto' => 'Piloto',
            'mariposa' => 'Mariposa',
            'ovalada' => 'Ovalada',
            'cuadrada' => 'Cuadrada', 
            'redonda' => 'Redonda',
            'wayfarer' => 'Wayfarer',
            'uniforme' => 'Pantalla uniforme'
        );
    }
    
    static function cara(){
        return array(
            'cara_cuadrada' => 'Cara cuadrada',
            'cara_triangular' => 'Cara triangular',
            'cara_ovalada' => 'Cara ovalada',
            'cara_redonda' => 'Cara redonda',
        );
    }
    
    static function genero(){
        
        return array(
            'mujer' => 'Mujer',
            'hombre' => 'Hombre'
        );
    }
        
    static function tipo(){
        return array(
           'diarias' => 'Lentillas diarias',
           'mensuales' => 'Lentillas mensuales',
           'semanales' => 'Lentillas semanales',
           'toricas' => 'Lentilas tóricas',
           'progresivas' => 'Lentilas progresivas',
           'colores' => 'Lentillas de colores'
        );
    }
    
    public $latitude;
    
    public $longitude;
    
    public $city;

    public $cp; 
    
    public $category;
    
    public $brand;
    
    public $model;

    public $color;
    
    public $tamano;

    public $material;
    
    public $montura;
    
    public $pantalla;
    
    public $cara;
    
    public $genero;
    
    public $tipo;

    public $sort;
    
    public $priceFrom;
    
    public $priceTo;
    
    public $featureValue;

    public function setCategory($category) {
        $this->category = $category;
        return $this;
    }
    
    public function getCategory() {
        return $this->category;
    }
    
    public function setBrand($brand) {
        $this->brand = $brand;
        return $this;
    }
    
    public function getBrand() {
        return $this->brand;
    }
    
    public function setModel($model) {
        $this->model = $model;
        return $this;
    }
    
    public function getModel() {
        return $this->model;
    }
    
    public function setColor($color) {
        $this->color = $color;
        return $this;
    }

    public function getColor() {
        return $this->color;
    }

   
    public function setTamano($tamano) {
        $this->tamano = $tamano;
        return $this;
    }
    
    public function getTamano() {
        return $this->tamano;
    }

    public function setMaterial($material) {
        $this->material = $material;
        return $this;
    }

    public function getMaterial() {
        return $this->material;
    }
    
    public function setMontura($montura) {
        $this->montura = $montura;
        return $this;
    }

    public function getMontura() {
        return $this->montura;
    }
    
    public function setPantalla($pantalla) {
        $this->pantalla = $pantalla;
        return $this;
    }

    public function getPantalla() {
        return $this->pantalla;
    }
    
    public function setCara($cara) {
        $this->cara = $cara;
        return $this;
    }

    public function getCara() {
        return $this->cara;
    }
    
    public function setGenero($genero) {
        $this->genero = $genero;
        return $this;
    }

    public function getGenero() {
        return $this->genero;
    }
    
    public function setTipo($tipo) {
        $this->tipo = $tipo;
        return $this;
    }

    public function getTipo() {
        return $this->tipo;
    }
    
    public function setSort($sort) {
        $this->sort = $sort;
        return $this;
    }

    public function getSort() {
        return $this->sort;
    }
    
    public function setPriceFrom($priceFrom) {
        $this->priceFrom = $priceFrom;
        return $this;
    }

    public function getPriceFrom() {
        return $this->priceFrom;
    }
    
    public function setPriceTo($priceTo) {
        $this->priceTo = $priceTo;
        return $this;
    }

    public function getPriceTo() {
        return $this->priceTo;
    }
    
//    public function getLatitude() {
//        return $this->latitude;
//    }
//
//    public function getLongitude() {
//        return $this->longitude;
//    }
//
//    public function getCity() {
//        return $this->city;
//    }
//
//    public function getCp() {
//        return $this->cp;
//    }
//
//    public function setLatitude($latitude) {
//        $this->latitude = $latitude;
//        return $this;
//    }
//
//    public function setLongitude($longitude) {
//        $this->longitude = $longitude;
//        return $this;
//    }
//
//    public function setCity($city) {
//        $this->city = $city;
//        return $this;
//    }
//
//    public function setCp($cp) {
//        $this->cp = $cp;
//        return $this;
//    }

    public function setFeatureValue($featureValue) {
        $this->featureValue = $featureValue;
        return $this;
    }
    
    public function getFeatureValue() {
        return $this->featureValue;
    }

}
