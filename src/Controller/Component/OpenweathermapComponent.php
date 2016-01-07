<?php
namespace Openweathermap\Controller\Component;


use Cake\Controller\Component;
use Cake\Core\Exception\Exception;
use Cake\Network\Http\Client;

/**
 * Openweathermap component
 * Component for getting weather forecast from Openweathermap.org
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
     *
     * @var array
     */
    protected $_defaultConfig = [
        'url'   => [
            'forecast' => 'http://api.openweathermap.org/data/2.5/forecast',
        ],
        'mode'  => 'json',
        'units' => 'metric',
        'lang'  => 'fr',
        //
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

        if (is_null($vars['units'])) {
            $params['units'] = $this->_defaultConfig['units'];
        }
        $params['units'] = $vars['units'];

        if (is_null($vars['lang'])) {
            $params['lang'] = $this->_defaultConfig['lang'];
        }

        $params['units'] = $vars['units'];

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
    private function _fetchData($params, $mode, $type = 'forecast')
    {
        if (is_null($mode)) {
            $mode = $this->_defaultConfig['mode'];
        }
        $client         = new Client();
        $params['mode'] = $mode;
        $data           = $client->get($this->_defaultConfig['url'][$type], $params);

        if (!$data->isOk()) {
            return ['success' => FALSE,
                    'error'   => __('Fetching error from Openweathermap')];
        }
        switch ($mode) {
            case 'json' :
                return ['success' => TRUE,
                        'data'    => $data->json];
            case 'xml' :
                return ['success' => TRUE,
                        'data'    => $data->xml];
            case 'html':
                return ['success' => TRUE,
                        'data'    => $data->body()];
        }
    }

    public function parseDatas($data)
    {

    }
}