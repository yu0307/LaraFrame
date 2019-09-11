/*! FeiWeather v1.0.0
Author: Lucas F. Lu
URL: Pending
License: MIT
Based on SimpleWeather -- https://github.com/monkeecreate/jquery.simpleWeather
*/

(function ( $ ) {
    'use strict';
    
    $.extend({
        FeiWeather: function (options) {
            var settings = $.extend({
                location: '',
                unit: 'Imperial',
                APIKey:'',
                weatherUrl: 'https://api.openweathermap.org/data/2.5/weather',
                requestParameters: '?callback=?',
                success: function (weather) { },
                error: function (message) { }
            }, options);
            if (settings.location !== '') {
                if (/^(\-?\d+(\.\d+)?),\s*(\-?\d+(\.\d+)?)$/.test(settings.location)) {
                    settings.location =settings.location.split(',');
                    settings.location = 'lat=' + settings.location[0] + '&lon=' + settings.location[1];
                } else {
                    settings.location = ('q='+settings.location.replace(", ",','));
                }
                settings.requestParameters += ('&' + settings.location) + ('&units=' + (settings.unit === 'Imperial' ? 'imperial' :'metric'));
            }else {
                settings.error('Could not retrieve weather due to an invalid location.');
                console.log('Could not retrieve weather due to an invalid location.');
                return false;
            }
            if (settings.APIKey.length <= 0) {
                settings.error('API key is needed.');
                console.log('API key is needed.');
                return false;
            }else{
                settings.requestParameters +=('&appid='+settings.APIKey);
            }
            var compass = ['N', 'NNE', 'NE', 'ENE', 'E', 'ESE', 'SE', 'SSE', 'S', 'SSW', 'SW', 'WSW', 'W', 'WNW', 'NW', 'NNW', 'N'];

            function GetWeather(callback){
                $.getJSON(
                    encodeURI(settings.weatherUrl + settings.requestParameters),
                    function (data) {
                        if (data !== null) {
                            callback(data);
                        } else {
                            settings.error('There was a problem retrieving the latest weather information.');
                        }
                        return false;
                    }
                );
            }

            function ProcessDailyCast(DailyCast,returnValue=false){
                if(false!==DailyCast){
                    var weather={};
                    var time = new Date(DailyCast.dt*1000);
                    var fc = DailyCast.weather[0];
                    weather.title = fc.main;
                    weather.temp = DailyCast.main.temp;
                    weather.code = fc.id;
                    weather.todayCode = fc.id;
                    weather.currently = fc.description;
                    weather.text = fc.description;
                    weather.high = DailyCast.main.temp_max;
                    weather.low = DailyCast.main.temp_min;
                    weather.humidity = DailyCast.main.humidity;
                    weather.pressure = (undefined !== DailyCast.main.grnd_level) ? DailyCast.main.grnd_level : ((undefined !== DailyCast.main.sea_level) ? DailyCast.main.sea_level : DailyCast.main.pressure) ;
                    weather.forecastDate = time.toLocaleDateString();
                    weather.visibility = DailyCast.visibility;
                    time = new Date(DailyCast.sys.sunrise * 1000);
                    weather.sunrise = time.toLocaleTimeString();
                    time = new Date(DailyCast.sys.sunset * 1000);
                    weather.sunset = time.toLocaleTimeString();
                    weather.city = DailyCast.name;
                    weather.country = DailyCast.sys.country;
                    // weather.region = DailyCast.location.region;
                    // weather.updated = DailyCast.item.pubDate;
                    // weather.link = DailyCast.item.link;
                    weather.units = { temp: (settings.unit === 'Imperial' ? 'F' : 'C'), pressure: 'hPa', speed: (settings.unit === 'Imperial' ? 'mph' : 'm/s') };
                    weather.wind = { direction: compass[Math.round(DailyCast.wind.deg / 22.5)], speed: DailyCast.wind.speed };

                    weather.description = 'Weather forecast at ' + weather.forecastDate+' ' + (undefined !== weather.city ? ('for ' + weather.city):'')+' is ' + fc.description +
                        (undefined !== DailyCast[fc.main] ? ((', ' + ConditionDetail(fc.main, DailyCast[fc.main]))) : '') + '. Tempreture is' +
                        (undefined !== weather.high ? (' as high as ' + weather.high + ' ' + weather.units.temp) : '')  +
                        (undefined !== weather.low ? ((undefined !== weather.high ?' while ':'') + weather.low + ' ' + weather.units.temp+' being the lowest') : '') +
                        (undefined !== weather.temp ? (((undefined !== weather.high || undefined !== weather.low )? ' with an average of ' : '') + weather.temp + ' ' + weather.units.temp) : '')+'.' +
                        (undefined !== weather.humidity ? (' Humidity is ' + weather.humidity) : '')+
                        (undefined !== DailyCast.wind.speed ? ((undefined !== weather.humidity ? ' and w' : 'W') + 'ind is going towards ' + weather.wind.direction + ' at ' + weather.wind.speed + ' ' + weather.units.speed): '') +'.'+
                        (undefined !== weather.sunrise ? ('Sunrise is at ' + weather.sunrise): '') +
                        (undefined !== weather.sunset ? ((undefined !== weather.sunrise ? ', and s' : 'S') + 'unset is at ' + weather.sunset) : '')+'.';

                    if (returnValue === false) {
                        settings.success(weather);
                    }else{
                        return weather;
                    }
                }
                return false;
            }

            function ConditionDetail(key, condition){
                if('all' in condition){
                    return ('with ' + val + '%' + ' ' + key.toLowerCase());
                }
                if ('1h' in condition) {
                    return ('with ' + condition['1h'] + (key == 'rain' ? ' rainfall' :' volume of snow') + ' for the past hour.');
                }
                if ('3h' in condition) {
                    return ('with ' + condition['1h'] + (key == 'rain' ? ' rainfall' : ' volume of snow') + ' for the past 3 hours.');
                }
                return '';
            }

            function Process5DaysForecast(DaysForecast) {
                if (false !== DaysForecast) {
                    if (DaysForecast.list !== undefined && DaysForecast.list.length>0){
                        var forecast=[];
                        var counter=0;
                        $.each(DaysForecast.list,function(idx,elm){
                            if(counter%8==0){
                                elm.name = DaysForecast.city.name;
                                elm.sys.country = DaysForecast.city.country;
                                elm.sys.sunrise = DaysForecast.city.sunrise;
                                elm.sys.sunset = DaysForecast.city.sunset;
                                forecast.push(ProcessDailyCast(elm, true));
                            }
                            counter++;
                        });
                        settings.success(forecast);
                    }
                }else{
                    settings.error('no data returned.');
                }
                return false;
            }

            return {
                get5days:   function () {
                    settings.weatherUrl ='https://api.openweathermap.org/data/2.5/forecast';
                    GetWeather(Process5DaysForecast);
                },
                getForecast: function () { 
                    settings.weatherUrl = 'https://api.openweathermap.org/data/2.5/weather';
                    return GetWeather(ProcessDailyCast);
                },
            };
        }
    });
}( jQuery ));