# Openweathermap plugin for CakePHP v3

## Installation (in progress)

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```
composer require cyberbobjr/Openweathermap
```

## Installation (now)
Put the plugin into the 'plugins' directory of your app.
Check your composer.json and add this line :
```
    "autoload": {
        "psr-4": {
            "Openweathermap\\": "./plugins/Openweathermap/src"
        },
```
Launch autodump :
```
composer composer.phar dumpautoload
```
Create 2 tables : weatherdatas & weathersites with the help of initial migration file into 'Migrations' directory of the plugin (/Openweathermap/configs/Migrations)

## Configuration
Check that the plugin was loaded into the bootstrap.php :
```
Plugin::load('Openweathermap', ['bootstrap' => FALSE,
                                'routes'    => FALSE]);
```
Load the component into your AppController.php or any Controller you want :
```
$this->loadComponent('Openweathermap.Openweathermap', ['key' => YOURAPIKEY]);
```
The API Key is mandatory, if you want an API go to the openweathermap.org and follow instructions.

## Using
You can fetch weather forecast for any cities with theses 3 functions :
getWeatherByCityId
getWeatherByCityName
getWeatherByGeoloc

For example into a shell code :
For this example, a $sites table contain every information about the city (name, long/lat or weathersite_id)
```
public function main()
    {
        // récupération de tous les sites
        $sites = $this->Sites->find('all');
        foreach ($sites as $site) {
            $this->out('site : ' . $site->libelle);
            if ($site->has('weathersite_id')) {
                $data = $this->Openweathermap->getWeatherByCityId($site->weathersite_id);
            } elseif ($site->has('latitude') || !$site->has('longitude')) {
                $data = $this->Openweathermap->getWeatherByGeoloc($site->latitude, $site->longitude);
                if ($data['success']) {
                    $this->Sites->associateOpenweatherSite($site->id, $data['data']['city']['id']);
                }
            } else {
                $data = $this->Openweathermap->getWeatherByCityName($site->ville, 'FR');
                if ($data['success']) {
                    $this->Sites->associateOpenweatherSite($site->id, $data['data']['city']['id']);
                }
            }
            sleep(2);
        }
    }
```

Everytime you fetch weatherdata, the component check if the request is more than 6 hours old, otherwise the component will return weatherdata from database instead of updating it.

<b>Don't use XML or HTML format, it's not implemented for the moment</b>

## Future
I'm coding a Weather Helper which can display icon (css) or image from weather information.

## End
Sorry for my very bad english (i'm a french guy), if you see some errors, please do a pull request. If you want more informations, you can ask your questions into the issues section of this page.

you can send me a mail at cyberbobjr(at)yahoo(dot)com

Regards
