<?php
/**
 * Created by PhpStorm.
 * User: Danish Mir
 * Date: 23-10-2017
 * Time: 07:51 PM
 */

namespace Algoclogic\PALL;
use InvalidArgumentException;
use Algoclogic\PALL\Helpers\APIHelper;

class SimilarityLookup
{
    use APIHelper;
    private $locales = [
        'co.uk', 'com', 'ca', 'com.br', 'de', 'es', 'fr', 'in', 'it', 'co.jp', 'com.mx'
    ];
    public  function __construct(){
        $this->validConfig();
    }
    public function similarityLookup($rawParams){
        $params = $this->buildParams($rawParams);

        $validParams = array('ItemId','ResponseGroup','Condition','MerchantId','SimilarityType');

        $validResponseGroup = array('Accessories','BrowseNodes','EditorialReview','Images','ItemAttributes','ItemIds','Large','Medium',
            'Offers','OfferSummary','PromotionSummary','Reviews','SalesRank','Similarities','Small',
            'Tracks','Variations','VariationSummary');

        $validConditions = array('New','Used','Collectible','Refurbished','All');

        $validMerchantIds = array('Amazon');

        $validSimilarityTypes = array('Intersection','Random');

        if(!isset($params['ItemId'])){
            throw new InvalidArgumentException(
                sprintf(
                    'You have not provided the required parameter: %s', 'ItemId'
                )
            );
            return false;
        }

        foreach ($params as $paramsKey => $paramsValue) {
            if (!in_array($paramsKey, $validParams)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'You have configured one or more invalid Request Parameters. Possible Request Parameters are: %s',
                        implode(', ', $validParams)
                    )
                );
                return false;
            }
        }

        if(isset($params['ResponseGroup'])) {
            foreach (explode(",", $params['ResponseGroup']) as $responseGroup) {
                if (!in_array($responseGroup, $validResponseGroup)) {
                    throw new InvalidArgumentException(
                        sprintf(
                            'You have configured one or more invalid Response Group. Possible Response Groups are: %s',
                            implode(', ', $validResponseGroup)
                        )
                    );
                    return false;
                }
            }
        }

        if (isset($params['Condition'])? !in_array($params['Condition'], $validConditions): false) {
            throw new InvalidArgumentException(
                sprintf(
                    'You have configured a wrong Condition. Possible Conditions are: %s',
                    implode(', ', $validConditions)
                )
            );
            return false;
        }

        if (isset($params['MerchantId'])? !in_array($params['MerchantId'], $validMerchantIds) : false) {
            throw new InvalidArgumentException(
                sprintf(
                    'You have configured a wrong Merchant Id. Possible Merchant Ids are: %s',
                    implode(', ', $validMerchantIds)
                )
            );
            return false;
        }

        if (isset($params['SimilarityType'])? !in_array($params['SimilarityType'], $validSimilarityTypes) : false) {
            throw new InvalidArgumentException(
                sprintf(
                    'You have configured a wrong SimilarityType. Possible SimilarityTypes are: %s',
                    implode(', ', $validSimilarityTypes)
                )
            );
            return false;
        }

        // handle OK
        $signedURL = $this->buildURL('SimilarityLookup',$params);

        // Get cURL resource
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 'TRUE',
            CURLOPT_URL => $signedURL,
        ));
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);
        // return Response::make($resp, '200')->header('Content-Type', 'text/xml');
        $xml = new \SimpleXMLElement($resp);
        return $xml;

    }
    private function validConfig()
    {
        if(empty(config('amazoncredentials.access_key_id')) || empty(config('amazoncredentials.secret_access_key')) || empty(config('amazoncredentials.associate_tag')))
        {
            throw new InvalidArgumentException('No Access Key, Secret Key or Associate Tag has been set.');
        }

        if(!in_array(config('amazoncredentials.locale'), $this->locales))
        {
            throw new InvalidArgumentException(
                sprintf(
                    'You have configured an invalid locale "%s". Possible locales are: %s',
                    config('amazoncredentials.locale'),
                    implode(', ', $this->locales)
                )
            );
        }
    }
}