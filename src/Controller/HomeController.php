<?php

namespace App\Controller;

use App\Entity\Towns;
use App\Entity\TownSearch;
use App\Form\TownSearchType;
use App\Repository\ShopCategoriesRepository;
use App\Repository\ShopsRepository;
use App\Repository\TownsRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     *
     * @var Towns[] $towns
     * @var Towns $town
     * @param Request $request
     * @param TownsRepository $townsRepository
     * @param ShopsRepository $shopsRepository
     * @param ShopCategoriesRepository $shopCategoriesRepository
     * @return Response
     */
    public function index(PaginatorInterface $paginator ,Request $request, TownsRepository $townsRepository, ShopsRepository $shopsRepository, ShopCategoriesRepository $shopCategoriesRepository): Response
    {
        $search = new TownSearch();
        $message = false;
        $form = $this->createForm(TownSearchType::class, $search);
        $form->handleRequest($request);

        if ($search->getZipCodeSearch() || $search->getNameTownSearch()) {
            $shopList = $this->findShopBySearch($search, $townsRepository, $shopsRepository,$request,$paginator); 
        } else {
            $shopList = $this->randomShop($shopsRepository,$request,$paginator);
        }
        dump(count($shopList));
        if (count($shopList) == 0) {
            $message = true;
        }
        return $this->render('home/index.html.twig', [
            'categories' => $shopCategoriesRepository->findAll(),
            'current_menu' => 'home',
            'shopList' => $shopList,
            'formSearchTown' => $form->createView(),
            'message' => $message,
            'search' => $search->getZipCodeSearch() || $search->getNameTownSearch()
        ]);
    }

    /**
     * recherche une liste de 12 magasins aléatoirement
     *
     * @param ShopsRepository $shopsRepository
     * @return void
     */
    public function randomShop(ShopsRepository $shopsRepository,Request $request,PaginatorInterface $paginator)
    {
        // recupération de la liste des magasins
        $shopList = $shopsRepository->findAll();
        // récupération de 12 clés aléatoire des données de $shopList
        $tabRandomShopList = array_rand($shopList, 3);
        // Initialisation d'un tableau pour récupérer les objets Shops
        $shopRandomList = [];
        foreach ($tabRandomShopList as $idRandom) {
            //Insertion des objets Shops à l'aide des clés aléatoire récupérer au préalable
            array_push($shopRandomList, $shopList[$idRandom]);
        }
        $shopRandomListPaginate = $paginator->paginate(
            $shopRandomList,
            $request->query->getInt('page',1),
            6
        );

        return $shopRandomListPaginate;
    }

    /**
     * Undocumented function
     *
     * @param [type] $search
     * @param [type] $townsRepository
     * @param [type] $shopsRepository
     * @return Towns[] $shopList
     */
    public function findShopBySearch($search, $townsRepository, $shopsRepository,Request $request,PaginatorInterface $paginator)
    {
        $tabShopList = [];
        $shopList = [];
        // on récupére les id de la ville si elle a plusieurs code postal
        $towns = $townsRepository->getTownBySearch($search);
        // on récupére les magasins si il existe par code postal
        foreach ($towns as $town) {
            $shop = $shopsRepository->getShopByTown($town);
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
        $shopListPaginate = $paginator->paginate(
            $shopList,
            $request->query->getInt('page',1),
            6
        );
        return $shopListPaginate;
        
    }
}
