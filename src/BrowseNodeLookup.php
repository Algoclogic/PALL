<?php
namespace Algoclogic\PALL;
use Algoclogic\PALL\Helpers\APIHelper;
use InvalidArgumentException;

class BrowseNodeLookup{
    use APIHelper;
        private $locales = [
            'co.uk', 'com', 'ca', 'com.br', 'de', 'es', 'fr', 'in', 'it', 'co.jp', 'com.mx'
        ];
        private $locale;
        private $associateTag;
        private $accessKeyId;
        private $secretAccessKey;
    public  function __construct(){
            $this->validConfig();
            $this->locale = config('amazoncredentials.locale');
            $this->associateTag = config('amazoncredentials.associate_tag');
            $this->accessKeyId = config('amazoncredentials.access_key_id');
            $this->secretAccessKey = config('amazoncredentials.secret_access_key');
    }
    public function browseNodeLookup($rawParams){
        $params = $this->buildParams($rawParams);

        $validParams = array('BrowseNodeId','ResponseGroup');
        $validResponseGroup = array('BrowseNodeInfo','MostGifted','NewReleases','MostWishedFor','TopSellers');


        if(!isset($params['BrowseNodeId'])){
            throw new InvalidArgumentException(
                sprintf(
                    'You have not provided the required parameter: %s', 'BrowseNodeId'
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

        foreach (explode(",",$params['ResponseGroup']) as $responseGroup) {
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

        // handle OK
        $signedURL = $this->buildURL('BrowseNodeLookup',$params);

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