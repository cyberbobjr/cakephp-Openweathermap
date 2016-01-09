<?php
namespace Openweathermap\Controller\Component;


use Cake\Controller\Component;
use Cake\Core\Exception\Exception;
use Cake\Network\Http\Client;
use Cake\ORM\TableRegistry;
use Openweathermap\Model\Table\WeatherdatasTable;
use Openweathermap\Model\Table\WeathersitesTable;

/**
 * Openweathermap component
 * Component for getting weather forecast from Openweathermap.org
 * @property WeatherdatasTable $Weatherdatas
 * @property WeathersitesTable $Weathersites
 */
class OpenweathermapComponent extends Component
{
    /**
     * api.openweathermap.org/data/2.5/forecast?lat=48.900552&lon=2.25929&APPID=1ac998025e1b44ea56a8af2ee5e965dd&units=metric&lang=fr
     */

    /**
     * Exemple of API response :
     * {"city":{"id":1851632,"name":"Shuzenji",
     * "coord":{"lon":138.933334,"lat":34.966671},
     * "country":"JP",
     * "cod":"200",
     * "message":0.0045,
     * "cnt":38,
     * "list":[{
     * "dt":1406106000,
     * "main":{
     * "temp":298.77,
     * "temp_min":298.77,
     * "temp_max":298.774,
     * "pressure":1005.93,
     * "sea_level":1018.18,
     * "grnd_level":1005.93,
     * "humidity":87
     * "temp_kf":0.26},
     * "weather":[{"id":804,"main":"Clouds","description":"overcast clouds","icon":"04d"}],
     * "clouds":{"all":88},
     * "wind":{"speed":5.71,"deg":229.501},
     * "sys":{"pod":"d"},
     * "dt_txt":"2014-07-23 09:00:00"}
     * ]}
     */

    /**
     * Possible values for lang :
     * English - en
     * Russian - ru
     * Italian - it
     * Spanish - es (or sp)
     * Ukrainian - uk (or ua)
     * German - de
     * Portuguese - pt
     * Romanian - ro
     * Polish - pl
     * Finnish - fi
     * Dutch - nl
     * French - fr
     * Bulgarian - bg
     * Swedish - sv (or se)
     * Chinese Traditional - zh_tw
     * Chinese Simplified - zh (or zh_cn)
     * Turkish - tr
     * Croatian - hr
     * Catalan - ca
     */

    /**
     * Default configuration.
     * define the default configuration for the Openweathermap API, configuration can be modified during the plugin
     * loading inside bootstrap.php core of the main App only the API key is not provided
     * @var array
     */
    protected $_defaultConfig = [
        'url'   => [
            'forecast' => 'http://api.openweathermap.org/data/2.5/forecast',
        ],
        'mode'  => 'json',
        'units' => 'metric',
        'lang'  => 'fr',
    ];

    /**
     * Initialisation of the component
     * @param array $config Array of configuration
     * @throws Exception If API Key is not provided
     */
    public function initialize(array $config)
    {
        if (isset($config['key'])) {
            $this->_defaultConfig['key'] = $config['key'];
        } else {
            throw new Exception(__('API Key must be provided'));
        }
    }

    /**
     * get weather data with geospatial coordinates of a city
     * @param int         $city_id Reference ID of the city (http://bulk.openweathermap.org/sample/ for the full list
     *                             of ID)
     * @param null|string $mode    Format output expected, optionnal, the default was fixed by config (json)
     * @param null|string $units   Units of temperature, optionnal, the default was fixed by config (celsius)
     * @param null|string $lang    Language for the output, optionnal, the default was fixed by config (french)
     * @return array Array of result, the key 'success' indicate the result (TRUE or FALSE), if error, the key 'error'
     *                             contain the detail of the error, if success the key 'data' contain the weather data
     */
    public function getWeatherByCityId($city_id, $mode = NULL, $units = NULL, $lang = NULL)
    {
        if (is_null($city_id)) {
            return ['success' => FALSE,
                    'error'   => __('Invalid coordinates')];
        }
        // Build an array of parameters
        $params = $this->_buildParams('cityId', ['units' => $units,
                                                 'lang'  => $lang,
                                                 'city'  => $city_id]);
        return $this->_fetchData($params, $mode);
    }

    /**
     * get weather data with geospatial coordinates of a city
     * @param string      $city_name    Name of the city
     * @param string      $country_code Name of the country, optionnal, better use for a more accurate result
     * @param null|string $mode         Format output expected, optionnal, the default was fixed by config (json)
     * @param null|string $units        Units of temperature, optionnal, the default was fixed by config (celsius)
     * @param null|string $lang         Language for the output, optionnal, the default was fixed by config (french)
     * @return array Array of result, the key 'success' indicate the result (TRUE or FALSE), if error, the key 'error'
     *                                  contain the detail of the error, if success the key 'data' contain the weather
     *                                  data
     */
    public function getWeatherByCityName($city_name, $country_code = NULL, $mode = NULL, $units = NULL, $lang = NULL)
    {
        if (empty($city_name)) {
            return ['success' => FALSE,
                    'error'   => __('Invalid coordinates')];
        }
        // Build an array of parameters
        $params = $this->_buildParams('cityName', ['units' => $units,
                                                   'lang'  => $lang,
                                                   'city'  => join(',', [$city_name,
                                                                         $country_code])]);
        return $this->_fetchData($params, $mode);
    }

    /**
     * get weather data with geospatial coordinates of a city
     * @param float       $lat   Latitude coordinate of the POI
     * @param float       $lon   Longitude coordinate of the POI
     * @param null|string $mode  Format output expected, optionnal, the default was fixed by config (json)
     * @param null|string $units Units of temperature, optionnal, the default was fixed by config (celsius)
     * @param null|string $lang  Language for the output, optionnal, the default was fixed by config (french)
     * @return array Array of result, the key 'success' indicate the result (TRUE or FALSE), if error, the key 'error'
     *                           contain the detail of the error, if success the key 'data' contain the weather data
     */
    public function getWeatherByGeoloc($lat, $lon, $mode = NULL, $units = NULL, $lang = NULL)
    {
        if (is_null($lat) || is_null($lon)) {
            return ['success' => FALSE,
                    'error'   => __('Coordinates not valid')];
        }
        // Build an array of parameters
        $params = $this->_buildParams('geoloc', ['units' => $units,
                                                 'lang'  => $lang,
                                                 'lat'   => $lat,
                                                 'lon'   => $lon]);
        return $this->_fetchData($params, $mode);
    }

    /**
     * Build internal array for paramater of urls
     * @param string $type Type of search 'geoloc', 'city' or 'id'
     * @param array  $vars Array of vars for the url which will be called
     * @return mixed
     */
    private function _buildParams($type, $vars)
    {
        $params['APPID'] = $this->_defaultConfig['key'];

        // Units parameter (C° of F°)
        if (is_null($vars['units'])) {
            $params['units'] = $this->_defaultConfig['units'];
        } else {
            $params['units'] = $vars['units'];
        }
        
        // Language parameter
        if (is_null($vars['lang'])) {
            $params['lang'] = $this->_defaultConfig['lang'];
        }

        /**
         * made custom query switch from the request type (geoloc, cityId, cityName)
         */
        switch ($type) {
            case 'geoloc' :
                $params['lat'] = $vars['lat'];
                $params['lon'] = $vars['lon'];
                break;
            case 'cityId':
                $params['id'] = $vars['city'];
                break;
            case 'cityName':
                $params['q'] = $vars['city'];
                break;
        }
        return $params;
    }

    /**
     * Fetch data from Openweathermap website
     * @param array  $params Array of parameters for building the url
     * @param string $mode   Output format expected (json, xml, html)
     * @param string $type   Type of request (forecast, current)
     * @return array|mixed
     */
    private function _fetchData($params, $mode = NULL, $type = 'forecast')
    {
        try {
            // if $mode is null, we get the default config mode
            if (is_null($mode)) {
                $mode = $this->_defaultConfig['mode'];
            }
            // create HTTP Client
            $client         = new Client();
            $params['mode'] = $mode;
            // grab the info
            $data = $client->get($this->_defaultConfig['url'][$type], $params);

            if (!$data->isOk()) {
                // if the grabing is a failure, we return an error
                return ['success' => FALSE,
                        'error'   => __('Fetching error from Openweathermap')];
            } else {
                // if grabing is a success, we parse the result
                switch ($mode) {
                    case 'json' :
                        $response = ['success' => TRUE,
                                     'data'    => $data->json];
                        break;
                    case 'xml' :
                        $response = ['success' => TRUE,
                                     'data'    => $data->xml];
                        break;
                    case 'html':
                        $response = ['success' => TRUE,
                                     'data'    => $data->body()];
                        break;
                }

                // we save weather informations into database
                $this->_saveData($data->json);
            }
        } catch (\Exception $ex) {
            $response = ['success' => FALSE,
                         'error'   => $ex->getMessage()];
        }

        return $response;
    }

    /**
     * Save the weather response from the Openweathermap API
     * @param array $weatherdatas Array with weather datas in JSON format
     * @return bool TRUE if success, FALSE if error
     */
    private function _saveData($weatherdatas)
    {
        $this->Weathersites = TableRegistry::get('Weathersites');
        $this->Weatherdatas = TableRegistry::get('Weatherdatas');
        // we get the site
        $site = $weatherdatas['city'];
        // we check if site is already in the base
        if ($this->Weathersites->exists(['id' => $site['id']])) {
            $site = $this->Weathersites->get($site['id']);
        } else {
            // if not, we create it
            $site = $this->Weathersites->newEntity(['id'        => $site['id'],
                                                    'name'      => $site['name'],
                                                    'longitude' => $site['coord']['lat'],
                                                    'latitude'  => $site['coord']['lon'],
                                                    'country'   => $site['country'],
                                                   ]);
            $site = $this->Weathersites->save($site);
        }
        // then we parse all the weather data
        foreach ($weatherdatas['list'] as $list) {
            // we check if the weatherdata is already in the base

            $weatherdata = $this->Weatherdatas->find()
                                              ->where(['weathersite_id'     => $site->id,
                                                       'UNIX_TIMESTAMP(dt)' => $list['dt']])
                                              ->first();
            if (($weatherdata && ($weatherdata->created < (new \DateTime("now"))->sub(new \DateInterval('PT3H')))) || (!$weatherdata)) {
                $data = ['temp'               => $list['main']['temp'],
                         'temp_min'           => $list['main']['temp_min'],
                         'temp_max'           => $list['main']['temp_max'],
                         'pressure'           => $list['main']['pressure'],
                         'sea_level'          => $list['main']['sea_level'],
                         'grnd_level'         => $list['main']['grnd_level'],
                         'humidity'           => $list['main']['humidity'],
                         'temp_kf'            => $list['main']['temp_kf'],
                         'weatherid'          => $list['weather'][0]['id'],
                         'weathermain'        => $list['weather'][0]['main'],
                         'weatherdescription' => $list['weather'][0]['description'],
                         'weathericon'        => $list['weather'][0]['icon'],
                         'clouds'             => $list['clouds']['all'],
                         'windspeed'          => $list['wind']['speed'],
                         'winddeg'            => $list['wind']['deg'],
                         'rain3'              => isset($list['rain']['3h']) ? $list['rain']['3h'] : NULL,
                         'snow3'              => isset($list['snow']['3h']) ? $list['snow']['3h'] : NULL,
                ];
                // if the weatherdata is already in the base AND the last query is more than 6 hours, we update the data with the latest grab OR if the weatherdata not exist
                if ($weatherdata) {
                    // we update weatherdata
                    $weatherdata = $this->Weatherdatas->patchEntity($weatherdata, $data);
                } else {
                    $data = array_merge($data, ['dt'             => date_create_from_format('U', $list['dt']),
                                                'weathersite_id' => $site->id]);
                    // we create weatherdata
                    $weatherdata = $this->Weatherdatas->newEntity($data);
                }
                $this->Weatherdatas->save($weatherdata);
            }
        }
        return TRUE;
    }
}
