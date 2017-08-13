<?php
namespace CatalogueBundle\Controller;

use CoreBundle\Controller\BaseController;
use CatalogueBundle\Form\SearchCategoryType;
use CatalogueBundle\Form\Model\Search;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use CatalogueBundle\Entity\Brand;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Catalogue controller.
 *
 * @Route("/")
 */
class CatalogueController extends BaseController
{
    const ITEMS_PER_PAGE = 20;

    /**
     * @Route("/{slug}", requirements={"slug" = "^((?!(\/|login|logout|profile|cities.json|bundles|css|js|images|optic|BingSiteAuth.xml|login_check|rellena-tu-cuenta-bancaria)).)*$" }, defaults={"page" = "1"})
     * @Route("/{slug}/page/{page}", name="cataloguebundle_catalogue_category_pager", requirements={"slug" = "^((?!login|logout|profile|cities.json|css|js|images).)*$" }, defaults={"page" = "1"})
     * @Template()
     */
    public function categoryAction(Request $request, $slug, $page)
    {
        $latitude = null;
        $longitude = null;
        $city = null;
        $cp = null;
        $categoryIds = array();
        $brandIds = array();
        $modelIds = array();
        $colors = array();
        $sizes = array();
        $materials = array();
        $mounts = array();
        $screens = array();
        $faces = array();
        $genders = array();
        $contactLenses = array();
        $sort = null;
        $priceFrom = '0';
        $priceTo = '500';
        
        $formConfig = array();
        $formConfig['features'] = array();

        $em = $this->getDoctrine()->getManager();
        $data = $request->query->get('search_category');
//        print_r($data);die;
        
        
        //hack for pagination
        $slugArr = explode('/', $slug);
        if(isset($slugArr[0])) $slug = $slugArr[0];
        if(isset($slugArr[1])&& $slugArr[1] == 'page'){
            if(isset($slugArr[2])) $page = $slugArr[2];
        }

        if(isset($data['category']) && count($data['category'])>0){
            $categoryIds = $data['category'];
            $subcategory = $em->getRepository('CatalogueBundle:Category')->findOneById($data['category'][0]);
            $category = $subcategory->getParentCategory();
        }else{
            $category = $em->getRepository('CatalogueBundle:Category')->findOneBySlug($slug);
            $categoryIds[] = $category->getId();
        }
        if(count($category->getFeatures())>0){
            $features = $em->getRepository('CatalogueBundle:Feature')->findBy(array('category' => $category),array('order' => 'ASC'));
            $formConfig['features'] = $features;
        }
        
    
        $formConfig['category'] = $category;
            
        if($request->query->get('brand')!=''){
            $brandIds[] = $request->query->get('brand');
            $brand = $em->getRepository('CatalogueBundle:Brand')->find($request->query->get('brand'));
            $formConfig['brand'] = $brand;
        }
        
        if($request->query->get('model')!=''){
            $modelIds[] = $request->query->get('model');
            $model = $em->getRepository('CatalogueBundle:BrandModel')->find($request->query->get('model'));
            $formConfig['model'] = $model;
        }
        
        foreach ($formConfig['features'] as $feature) {
            $feature = $feature->getSlug();
            if($request->query->get($feature)!=''){
                $formConfig[$feature] = $request->query->get($feature);
            }
        }

        //price        
        if($data['price_from'] != ''){
            $formConfig['price_from'] = $data['price_from'];
        }else{
            $formConfig['price_from'] = '0';
        }
        if($data['price_to'] != ''){
            $formConfig['price_to'] = $data['price_to'];
        }else{
            $formConfig['price_to'] = '500';
        }

        $entity = new Search();
//        if($this->get('session')->get('city') != '') {
//            $city = $this->get('session')->get('city');
//            $entity->setCity($city);
//        }
//        if($this->get('session')->get('cp') != '') {
//            $cp = $this->get('session')->get('cp');
//            $entity->setCp($cp);
//        }
//        if($this->get('session')->get('latitude') != '') {
//            $latitude = $this->get('session')->get('latitude');
//            $entity->setLatitude($latitude);
//        }
//        if($this->get('session')->get('longitude') != '') {
//            $longitude = $this->get('session')->get('longitude');
//            $entity->setLongitude($longitude);
//        } 
        $form = $this->createSearchCategoryForm($entity, $formConfig, $slug);
        
        if($data!=''){
            
            $form->handleRequest($request);
       
            if ($form->isValid()) {
//                $latitude = $form->getNormData()->getLatitude();
//                $longitude = $form->getNormData()->getLongitude();
//                $city = $form->getNormData()->getCity();
//                $cp = $form->getNormData()->getCp();
                $categoryIds = $this->getValueIds($form->getNormData()->getCategory());
                if(count($categoryIds)==0) $categoryIds[] = $formConfig['category']->getId();
                $brandIds = $this->getValueIds($form->getNormData()->getBrand());
                $modelIds = $this->getValueIds($form->getNormData()->getModel());
                $colors = $form->getNormData()->getColor();
                $sizes = $form->getNormData()->getTamano();
                $materials = $form->getNormData()->getMaterial();
                $mounts = $form->getNormData()->getMontura();
                $screens = $form->getNormData()->getPantalla();
                $faces = $form->getNormData()->getCara();
                $genders = $form->getNormData()->getGenero();
                $contactLenses = $form->getNormData()->getTipo();
                $sort = $form->getNormData()->getSort();
                $priceFrom = $form->getNormData()->getPriceFrom();
                $priceTo = $form->getNormData()->getPriceTo();
            }
        }

       
        list($entities, $paginator) = $this->getSearchResult(
//                        $latitude,
//                        $longitude,
//                        $city,
//                        $cp,
                        $categoryIds,
                        $brandIds,
                        $modelIds,
                        $colors,
                        $sizes,
                        $materials,
                        $mounts,
                        $screens,
                        $faces,
                        $genders,
                        $contactLenses,
                        $sort,
                        $priceFrom,
                        $priceTo,
                        $page
                        );
                          
                    
        return array(
            'form' => $form->createView(),
            'category' => $category,
            'products' => $entities, 
            'features' => $formConfig['features'],
            'paginator' => $paginator, 
            'page' => $page
        );
 
    }
    
    
        private function getSearchResult(
//            $latitude,
//            $longitude,
//            $city,
//            $cp,
            $categories, 
            $brands, 
            $models,
            $colors,
            $sizes,
            $materials,
            $mounts,
            $screens,
            $faces,
            $genders,
            $contactLenses,
            $sort,
            $priceFrom,
            $priceTo,
            $page
            )
    {
   
       
        $params = array();
        $where = null;
        $andWhere = '';
        $join = '';
        
        if(!is_null($categories) && count($categories) > 0){
            if(count($categories)>1){
                $params['category'] =  $this->getValueIds($categories);
            }else{
                if($categories instanceof Category){
                    $params['category'] = $categories->getId();
                }else{
                    $params['category'] = $categories; 
                }
            }
            
            $where = ' WHERE c.id IN (:category) ';
        }
        
        //Brands
        if(count($brands)>0){
            if(is_null($where) && $andWhere=='')
            $andWhere .= ' WHERE b.id IN (:brands) ';
            else
            $andWhere .= ' AND b.id IN (:brands) ';
            $params['brands'] = $brands;
        }
        
        //Model
        if(count($models)>0){
            if(is_null($where) && $andWhere=='')
            $andWhere .= ' WHERE m.id IN (:models) ';
            else
            $andWhere .= ' AND m.id IN (:models) ';
            $params['models'] = $models;
        }
        
        //Colors
        if(count($colors)>0){
            $joinArray = array();
            foreach ($colors as $color) {
                $joinArray[] = " fv_color.name='".$color."' ";
                
            }
            $joinValues = implode(' OR ', $joinArray);
            $join =  " JOIN CatalogueBundle:Feature feature_color WITH feature_color.name='Color' "
                    . " JOIN feature_color.values fv_color WITH ".$joinValues
                    . " JOIN p.featureValues pf_color WITH fv_color.id = pf_color.id ";
            
        }
        
        //Sizes
        if(count($sizes)>0){
            $joinArray = array();
            foreach ($sizes as $size) {
                $joinArray[] = " fv_size.name='".$size."' ";
                
            }
            $joinValues = implode(' OR ', $joinArray);
            $join .=  " JOIN CatalogueBundle:Feature feature_size WITH feature_size.name='Tamaño' "
                    . " JOIN feature_size.values fv_size WITH ".$joinValues
                    . " JOIN p.featureValues pf_size WITH fv_size.id = pf_size.id ";
            
        }
        
        //Materials
        if(count($materials)>0){
            $joinArray = array();
            foreach ($materials as $material) {
                $joinArray[] = " fv_material.name='".$material."' ";
            }
            $joinValues = implode(' OR ', $joinArray);
            $join .=  " JOIN CatalogueBundle:Feature feature_material WITH feature_material.name='Material' "
                    . " JOIN feature_material.values fv_material WITH ".$joinValues
                    . " JOIN p.featureValues pf_material WITH fv_material.id = pf_material.id ";
            
        }
        
        //Mounts
        if(count($mounts)>0){
            $joinArray = array();
            foreach ($mounts as $mount) {
                $joinArray[] = " fv_mount.name='".$mount."' ";
                
            }
            $joinValues = implode(' OR ', $joinArray);
            $join .=  " JOIN CatalogueBundle:Feature feature_mount WITH feature_mount.name='Montura' "
                    . " JOIN feature_mount.values fv_mount WITH ".$joinValues
                    . " JOIN p.featureValues pf_mount WITH fv_mount.id = pf_mount.id ";
            
        }
        
        //Screens
        if(count($screens)>0){
            $joinArray = array();
            foreach ($screens as $screen) {
                $joinArray[] = " fv_screen.name='".$screen."' ";
                
            }
            $joinValues = implode(' OR ', $joinArray);
            $join .=  " JOIN CatalogueBundle:Feature feature_screen WITH feature_screen.name='Pantalla' "
                    . " JOIN feature_screen.values fv_screen WITH ".$joinValues
                    . " JOIN p.featureValues pf_screen WITH fv_screen.id = pf_screen.id ";
            
        }
        
        //Faces
        if(count($faces)>0){
            $joinArray = array();
            foreach ($faces as $face) {
                $joinArray[] = " fv_face.name='".$face."' ";
                
            }
            $joinValues = implode(' OR ', $joinArray);
            $join .=  " JOIN CatalogueBundle:Feature feature_face WITH feature_face.name='Cara' "
                    . " JOIN feature_face.values fv_face WITH ".$joinValues
                    . " JOIN p.featureValues pf_face WITH fv_face.id = pf_face.id ";
            
        }
        
        //Genders
        if(count($genders)>0){
            $joinArray = array();
            foreach ($genders as $gender) {
                $joinArray[] = " fv_gender.name='".$gender."' ";
                
            }
            $joinValues = implode(' OR ', $joinArray);
            $join .=  " JOIN CatalogueBundle:Feature feature_gender WITH feature_gender.name='Género' "
                    . " JOIN feature_gender.values fv_gender WITH ".$joinValues
                    . " JOIN p.featureValues pf_gender WITH fv_gender.id = pf_gender.id ";
            
        }
        
        //ContactLenses
        if(count($contactLenses)>0){
            $joinArray = array();
            foreach ($contactLenses as $contactLense) {
                $joinArray[] = " fv_contactLense.name='".$contactLense."' ";
                
            }
            $joinValues = implode(' OR ', $joinArray);
            $join .=  " JOIN CatalogueBundle:Feature feature_contactLense WITH feature_contactLense.name='Tipo' "
                    . " JOIN feature_contactLense.values fv_contactLense WITH ".$joinValues
                    . " JOIN p.featureValues pf_contactLense WITH fv_contactLense.id = pf_contactLense.id ";
            
        }
        
        //sort
        if(!is_null($sort)){
            switch ($sort) {
                case 0:
                    $sort = ' ORDER BY p.price ASC ';
                    break;
                case 1:
                    $sort = ' ORDER BY p.price ASC ';
                    break;
                case 2:
                    $sort = ' ORDER BY p.price DESC ';
                    break;
                case 3:
                    $sort = ' ORDER BY p.highlighted  DESC ';
                    break;
                case 4:
                    $sort = ' ORDER BY p.name ASC ';
                    break;
                default:
                    $sort = ' ORDER BY p.price ASC ';
                    break;
            }
     
        }else{
            if($this->get('session')->get('geolocated')) $sort = ' ORDER BY distance ASC ';
            else $sort = ' ORDER BY p.price ASC ';
        }
 
        //Price
        if(is_numeric($priceFrom) && is_numeric($priceTo)){
            if(is_null($where) && $andWhere=='')
            $andWhere .= ' WHERE p.price >= :price_from AND p.price <= :price_to ';
            else
            $andWhere .= ' AND p.price >= :price_from AND p.price <= :price_to ';
            $params['price_from'] = $priceFrom;
            $params['price_to'] = $priceTo;
        }
        
//        $geoWhere = null;
//        if(($latitude != '' && $longitude != '') || $city != '' || $cp != '') {
//            if(is_null($where) && $andWhere=='') $geoWhere = " WHERE (";
//            else
//            $geoWhere = " AND (";
//        }
        //$query = " SELECT p.id, p.name, p.price , p.slug, p.priceType, GROUP_CONCAT(i.path SEPARATOR ', ') as imagePaths, o.id opticId, o.name opticName, b.id brandId, b.name brandName, b.slug brandSlug ";
        $query = " SELECT p.id, p.name, p.price , p.slug, p.priceType, GROUP_CONCAT(i.path SEPARATOR ', ') as imagePaths, b.id brandId, b.name brandName, b.slug brandSlug ";
//        $geoOrWhere = array();
//        if($latitude != '' && $longitude !='') {
//            $query .= ", distance(o.latitude, o.longitude, '$latitude', '$longitude') as distance ";
//            $geoOrWhere[] = " distance(o.latitude, o.longitude, '".$latitude."', '".$longitude."') <= 5  ";
////            $sort = ' ORDER BY distance  ASC  ';
//        }
//        if($city != '') {
//            $geoOrWhere[] = ' o.city = :city ';
//            $params['city'] = $city;
//        }
//        if($cp != '') {
//            $geoOrWhere[] = ' o.postalCode = :cp ';
//            $params['cp'] = $cp;
//        }
//        $geoWhere .= implode(' AND ', $geoOrWhere);
        
//        if(($latitude != '' && $longitude != '') || $city != '' || $cp != '') $geoWhere .= ')';

        $query .= ' FROM CatalogueBundle:Product p'
                . ' JOIN p.brand b  WITH p.brand = b.id '
                . ' JOIN CatalogueBundle:BrandModel  m  WITH b.id = m.id '
                . ' JOIN p.category c '
                . ' LEFT JOIN p.images i '
                . ' '.  $join .' '
                . ' '.  $where .' '
                . ' '.  $andWhere .' '
                //. ' '.  $geoWhere .' '
                . ' GROUP BY p.id '
                . ' '. $sort;

//        print_r($query); 
        return  $this->paginator($query, $page, $params, self::ITEMS_PER_PAGE);      

    }
    
    /**
     * Creates a form to create a Post entity.
     *
     * @param Post $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createSearchCategoryForm(Search $entity, $formConfig, $slug)
    {
        $form = $this->createForm(SearchCategoryType::class, $entity, array(
            'action' => $this->generateUrl('catalogue_catalogue_category', array('slug' => $slug)),
            'method' => 'GET',
            'attr' => array('id' => 'search'),
            'formConfig' => $formConfig
        ));

        //$form->add('submit', 'submit', array('label' => 'Buscar'));

        return $form;
    }
    
    /**
     * Returns a list of BrandModel entities in JSON format.
     *
     * @return JsonResponse
     *
     * @Route("/model/{id}", requirements={ "_format" = "json" }, defaults={ "_format" = "json", "id" = "" })
     * @Method("GET")
     */
    public function modelJsonAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $brand = $em->getRepository('CatalogueBundle:Brand')->find($id);

        if (!$brand instanceof Brand){
            throw $this->createNotFoundException('Unable to find Brand entity.');
        }
            
        $entities = $em->getRepository('CatalogueBundle:BrandModel')->findByBrand($brand);

        $returnValues = array();
        foreach ($entities as $entity) {
            $returnValues[$entity->getId()] =  $entity->getName() ;
        }
        return new JsonResponse($returnValues);
    }
 
    private function getValueIds($types)
    {
       
        $returnValues = array();
        if(count($types)==0) {
            return $returnValues;
        }
        foreach ($types as $type) {
            $returnValues[] = $type->getId();
        }
        
        return $returnValues;
    }
}
