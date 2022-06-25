<?php

namespace App\Controller;

use App\service\callApiservice;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Length;

class CsvController extends AbstractController

{   

    //page  et route à laquelle le redirigie le user aprés son authentification pour télécharger orders

    /**
     * @Route("/", name="app_csv")
     */
    public function index(): Response
    {
        
        return $this->render('csv/vue.html.twig',);
    }
    

     //route qui permet le télechargement du csv 

    /**
     * @Route("/orders-to-csv", name="download")
     */
    //passer du service callApiservice avec linjection de dépendes au fonction download pour récupérer les données reçu du callApiService
    public function download(callApiservice $callApiservice){
        //définition entete fichier excel le ; sépare les données en colonnes
        $myVariableCSV = "Champ; Description;\n";
        //récupération des fonctions du service 
        $orders=$callApiservice->getOrders();
        $contacts=$callApiservice->getContacts();

        // boucle for sur le tableau  mutidimentionnel $orders pour le parcourir
        for($i=0;$i<count($orders['results']);$i++){
            // récupération du champ order number 
            $ordernumber=json_encode($orders['results'][$i]['OrderNumber']);
            //définition  dans une ligne pour pouvoir ajouter ensuite dans un fichier excel
            $myVariableCSV .= " order;$ordernumber;  \n";
            //boucle for sur le tableau contacts 
            for($j=0;$j<count($contacts['results']);$j++){
                // test sur l'égalité entre le champ du deliver to du orders[i] et le champ id du contacts j
                if($orders['results'][$i]['DeliverTo'] ==  $contacts['results'][$j]['ID'])
                {
                     $name=json_encode($contacts['results'][$j]['ContactName']);
                     $adress=json_encode($contacts['results'][$j]['AddressLine1']);
                     $country=json_encode($contacts['results'][$j]['Country']);
                     $zipcode=json_encode($contacts['results'][$j]['ZipCode']);
                     $city=json_encode($contacts['results'][$j]['City']);
                     $myVariableCSV .= " delivery_name;$name;  \n";
                     $myVariableCSV .= " delivery_address;$adress;  \n";
                     $myVariableCSV .= " delivery_country;$country;  \n";
                     $myVariableCSV .= " delivery_zipcode;$zipcode;  \n";
                     $myVariableCSV .= " delivery_city;$city;  \n";                    
                }            
            }
            //nombre d'items dans un order
           $items=json_encode(count($orders['results'][$i]['SalesOrderLines']['results']));
             // parcourir le tableau SalesOrdersLines pour récupérer les données du chaque item du order
            for($t=0;$t<$items;$t++){
                $myVariableCSV .= " ; ; ; \n";
             $itemindex=$t+1;
             $itemId=json_encode($orders['results'][$i]['SalesOrderLines']['results'][$t]['Item']);
            $itemquantity=json_encode($orders['results'][$i]['SalesOrderLines']['results'][$t]['Quantity']);
             $myVariableCSV .= " item_quantity;$itemquantity;  \n";
             $myVariableCSV .= " item_id;$itemId;  \n";
             $myVariableCSV .= " Item_index;$itemindex;  \n";
             $myVariableCSV .= " items_count;$items;  \n";
             $punitaire=$orders['results'][$i]['SalesOrderLines']['results'][$t]['UnitPrice'];
             $tva=$orders['results'][$i]['SalesOrderLines']['results'][$t]['VATPercentage'];
             $price_incl_vat=json_encode($orders['results'][$i]['SalesOrderLines']['results'][$t]['Amount']);
             $priceht=$price_incl_vat-($price_incl_vat*$tva);
             $price_excl_vat=json_encode($priceht);
             $myVariableCSV .= " price_incl_vat;$price_incl_vat;  \n";
             $myVariableCSV .= " price_excl_vat;$price_excl_vat;  \n";

           }
           // le \n à la fin permets de faire un saut de ligne  super important en CSV
           $myVariableCSV .= " ; ; ; \n";

        }
   
    $myVariableCSV .= " ; ; ; \n";
    //On donne la variable en string à la response, nous définissons le code HTTP à 200
    return new Response(
           $myVariableCSV,
           200,
           [
         //Définit le contenu de la requête en tant que fichier Excel
             'Content-Type' => 'application/vnd.ms-excel',
         //On indique que le fichier sera en attachment donc ouverture de boite de téléchargement ainsi que le nom du fichier
             "Content-disposition" => "attachment; filename=Orders.csv"
          ]
    );
    }
}
