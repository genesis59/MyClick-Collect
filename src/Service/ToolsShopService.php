<?php

namespace App\Service;

use App\Entity\TownSearch;
use App\Repository\ProductsRepository;
use App\Repository\ShopsRepository;
use App\Repository\TownsRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;


class ToolsShopService
{
    /**
     * @var PaginatorInterface
     */
    private $paginator;

    /**
     * @var RequestStack
     */
    private $request;

    /**
     * @var Shops
     */
    private $shop;

    /**
     * @var ProductsRepository
     */
    private $productRepo;

    /**
     * @var ShopsRepository
     */
    private $shopsRepo;

    /**
     * @var TownsRepository
     */
    private $townsRepo;
    

    public function __construct(PaginatorInterface $paginator, RequestStack $request, ProductsRepository $productRepo,ShopsRepository $shopsRepo,TownsRepository $townsRepo)
    {
        $this->paginator = $paginator;
        $this->request = $request->getCurrentRequest();
        $this->shop = $this->recupCurrentShop();
        $this->productRepo = $productRepo;
        $this->shopsRepo = $shopsRepo;
        $this->townsRepo = $townsRepo;  

    }

    /**
     * Creation of pagination compared with status of categories
     *
     * @return void
     */
    public function createPaginationList(){
        
        // if there are not subCategories we collect all the products from $shop
        if ($this->getNumberOfCategories() == 0) {
            return $this->pagination($this->productWithoutSubCategory(), 5);
        } else {
            // list of product paginate by category
            return $this->paginationProductWithOrWithoutCategory();
            
        }
    }

    /**
     * create a pagination for $listToPaginate
     *
     * @param [type] $listToPaginate
     * @param Int $nbElementByPage
     * @param String $newValueGet
     * @return void
     */
    private function pagination($listToPaginate, Int $nbElementByPage = 5, String $newValueGet = null)
    {
        if ($newValueGet) {
            return $this->paginator->paginate(
                $listToPaginate,
                $this->request->query->getInt($newValueGet, 1),
                $nbElementByPage,
                ['pageParameterName' => $newValueGet]
            );
        } else {
            return $this->paginator->paginate(
                $listToPaginate,
                $this->request->query->getInt('page', 1),
                $nbElementByPage,
            );
        }
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    private function paginationProductWithOrWithoutCategory()
    {
        // pagination of products by category
        $productsBySubCatList = [];
        foreach ($this->shop->getShopSubCategories() as $subCategory) {
            $current = 
            $pagination = $this->pagination($this->productRepo->getProductsByShopBySubCat($this->recupCurrentShop(), $subCategory), 5);
            array_push($productsBySubCatList, $pagination);
        }
        // if product without category
        if ($this->productWithoutSubCategory()) {
            $pagination = $this->pagination($this->productWithoutSubCategory(), 5);
            array_push($productsBySubCatList, $pagination);
        }
        // pagination by category of previous pagination products
        $products = $this->pagination($productsBySubCatList, 1, 'cat');
        return $products;
    }

    private function recupCurrentShop(){
        foreach($this->request->attributes as $key => $value){
            if($key == 'shop'){
                return $value;
            }
            
        }
    }
   
    /**
     * check if search or not
     *
     * @param TownSearch $search
     * @return Shops[]
     */
    public function searchOrNotToSearch(TownSearch $search){
        if ($search->getZipCodeSearch() || $search->getNameTownSearch()) {
            $shopList = $this->findShopBySearch($search); 
        } else {
            $shopList = $this->randomShop(6);
        }
        return $shopList;
    }

    /**
     * recherche une liste de 6 magasins aléatoirement
     *
     * @param ShopsRepository $shopsRepository
     * @return void
     */
    public function randomShop(Int $numberOfRandomShop)
    {
        // recupération de la liste des magasins
        $shopList = $this->shopsRepo->findAll();
        // récupération des clés aléatoire des données de $shopList
        $tabRandomShopList = array_rand($shopList, $numberOfRandomShop);
        // Initialisation d'un tableau pour récupérer les objets Shops
        $shopRandomList = [];
        foreach ($tabRandomShopList as $idRandom) {
            //Insertion des objets Shops à l'aide des clés aléatoire récupérer au préalable
            array_push($shopRandomList, $shopList[$idRandom]);
        }
        return $this->pagination($shopRandomList,6);
    }

    

    /**
     * search shops by nameTown or zipCode
     *
     * @param TownSearch $search
     * @return void
     */
    public function findShopBySearch(TownSearch $search)
    {
        $tabShopList = [];
        $shopList = [];
        // on récupère les id de la ville si elle a plusieurs code postal
        $towns = $this->townsRepo->getTownBySearch($search);
        // on récupére les magasins si il existe par code postal
        foreach ($towns as $town) {
            $shop = $this->shopsRepo->getShopByTown($town);
            if ($shop) {
                array_push($tabShopList, $shop);
            }
        }
        // on récupére les magasins dont la ville et le code postal sont identiques
        foreach ($tabShopList as $shops) {
            // et on les ajoute à $shopList indépendamment
            foreach ($shops as $shop) {
                array_push($shopList, $shop);
            }
        }
        return $this->pagination($shopList,6);
        
    }

    public function getCurrentShop(){
        return $this->shop;
    }
    public function productWithoutSubCategory(){
        return $this->productRepo->getProductsByShopBySubCat($this->recupCurrentShop());
    }
    public function getSubCategories(){
        return $this->shop->getShopSubCategories();
        
    }
    public function getNumberOfCategories(){
        return count($this->getSubCategories());
    }
}
