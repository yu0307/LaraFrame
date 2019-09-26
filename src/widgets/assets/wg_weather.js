/**** WEATHER WIDGET ****/
$(document).ready(function () {
    var weatherControl = widgetWeather();
    weatherControl.getForecast();
    weatherControl.get5days();
    var weatherTimer;
    /* We get city from input on change */
    $(".widget-weather input.wg_weather_city_search").on('keyup', function (e) {
        e.preventDefault;
        city = $(this).val();
        if (city.length > 0) {
            clearTimeout(weatherTimer);
            weatherTimer = setTimeout(function () {
                weatherControl.setLocation(city);
                weatherControl.getForecast();
                weatherControl.get5days();
            }, 2000);
        }
    });

    $(window).resize(function () {
        setTimeout(function () {
            $('.widget-weather').height($('.widget-weather .panel-header').height() + $('.weather').height() + 12);
        }, 100);
    });
});

function widgetWeather() {

    var weatherWidget = '<div class="panel-header background-primary p-0"><h3 class="m-0"><i class="icon-30"></i> <strong>Weather</strong> Widget</h3></div><div class="weather panel-content" class="widget-container widget-weather boxed"><div class="weather-highlighted">';
    weatherWidget += '<div class="day-0 weather-item clearfix active"><canvas id="day-0-icon" class="m-t-15" width="64" height="64"></canvas><div class="inner"><strong class="today-temp-low"></strong><span class="weather-currently"></span><span class="today-temp"></span></div><div class="c-white today-desc f-12 animated fadeOut"></div></div>';
    weatherWidget += '<div class="day-1 weather-item clearfix"><canvas id="day-1-icon" class="m-t-15" width="64" height="64"></canvas><div class="inner"><strong class="1-days-temp-low"></strong><span class="1-days-text"></span><span class="1-days-temp"></span></div></div>';
    weatherWidget += '<div class="day-2 weather-item clearfix"><canvas id="day-2-icon" class="m-t-15" width="64" height="64"></canvas><div class="inner"><strong class="2-days-temp-low"></strong><span class="2-days-text"></span><span class="2-days-temp"></span></div></div>';
    weatherWidget += '<div class="day-3 weather-item clearfix"><canvas id="day-3-icon" class="m-t-15" width="64" height="64"></canvas><div class="inner"><strong class="3-days-temp-low"></strong><span class="3-days-text"></span><span class="3-days-temp"></span></div></div>';
    weatherWidget += '<div class="day-4 weather-item clearfix"><canvas id="day-4-icon" class="m-t-15" width="64" height="64"></canvas><div class="inner"><strong class="4-days-temp-low"></strong><span class="4-days-text"></span><span class="4-days-temp"></span></div></div>';
    weatherWidget += '</div><div class="weather-location clearfix p-10"><strong></strong>';
    weatherWidget += '<div class="weather-search-form"><input type="text" name="search2" value="" id="wg_weather_city" class="wg_weather_city_search weather-search-field" placeholder="[City,Country],[lat,lon]"><input type="submit" value="" class="btn weather-search-submit" name="search-send2"></div></div><ul class="weather-forecast clearfix">';
    weatherWidget += '<li class="first"><a id="day-0" class="today-day active" href="javascript:;"><strong></strong><span class="today-img"></span><span class="today-temp-low"></span></a></li>';
    weatherWidget += '<li><a id="day-1" class="1-days-day" href="javascript:;"><strong></strong><span class="1-days-image"></span><span class="1-days-temp-low"></span></a></li>';
    weatherWidget += '<li><a id="day-2" class="2-days-day" href="javascript:;"><strong></strong><span class="2-days-image"></span><span class="2-days-temp-low"></span></a></li>';
    weatherWidget += '<li><a id="day-3" href="javascript:;" class="3-days-day"><strong></strong><span class="3-days-image"></span><span class="3-days-temp-low"></span></a></li>';
    weatherWidget += '<li class="last"><a id="day-4" href="javascript:;" class="4-days-day"><strong></strong><span class="4-days-image"></span><span class="4-days-temp-low"></span></a></li></ul></div>';

    $('.widget-weather').html('');
    $('.widget-weather').append(weatherWidget);



    //************************* WEATHER WIDGET *************************//
    /* We initiate widget with a city (can be changed) */
    var city = 'Mountain View, US';
    var icon_type_today = "partly-cloudy-day";
    var weekdays = new Array(
        "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"
    );
    var weatherControl = $.FeiWeather({
        location: 'Mountain View, US',
        proxyURL: '/WidgetsAjax/wg_weather/.wg_weather',
        success: function (weather) {
            var icon_type = {};
            var icons = new Skycons(), list = ["clear-day", "cloudy", "rain", "sleet", "snow", "wind", "fog"], i;
            for (i = list.length; i--;) { icons.set(list[i], list[i]); }
            switch (weather.actionPerformed) {
                case 'getForecast'://one day forecast
                    city = weather.city;
                    region = weather.country;
                    weather_icon = '<i class="icon-' + decodeWeather(weather.code) + '"></i>';
                    $(".weather-location strong").html(city);
                    $(".weather-currently").html(weather.currently);
                    $(".today-img").html('<canvas id="fc-day-0-icon" class="m-t-15 fc-day-0-icon" width="40" height="40"></canvas></i>');
                    $(".today-temp-low").html(weather.low + ' ' + weather.units.temp + '°');
                    $(".today-temp").html(weather.temp + ' ' + weather.units.temp + '° / ' + weather.high + ' ' + weather.units.temp + '°');
                    $(".today-desc").text(weather.description);
                    $(".weather-region").html(region);
                    $(".weather-day").html(weather.day);
                    $(".weather-icon").html(weather_icon);
                    $(".today-day strong").html(weekdays[weather.day]);
                    icons.set("day-0-icon", decodeWeather(weather.code));
                    icons.set("fc-day-0-icon", decodeWeather(weather.code));
                    break;
                case 'get5days'://5 days forecast
                    if (undefined !== weather.forecastData && weather.forecastData.length > 0) {
                        var index = 0;
                        $(weather.forecastData).each(function (idx, ForecastData) {
                            if (idx <= 4 && idx > 0) {
                                index++;
                                $("." + index + "-days-day strong").html(weekdays[ForecastData.day]);
                                $("." + index + "-days-text").html(ForecastData.currently);
                                $("." + index + "-days-image").html('<canvas id="fc-day-' + idx + '-icon" class="m-t-15 fc-day-' + idx + '-icon" width="40" height="40"></canvas></i>');
                                $("." + index + "-days-temp-low").html(ForecastData.low + ' ' + ForecastData.units.temp + '°');
                                $("." + index + "-days-temp").html(ForecastData.low + ' ' + ForecastData.units.temp + '° / ' + ForecastData.high + ' ' + ForecastData.units.temp + '°');
                                icons.set("day-" + idx + "-icon", decodeWeather(ForecastData.code));
                                icons.set("fc-day-" + idx + "-icon", decodeWeather(ForecastData.code));
                            }
                        });
                    }
                    break;
            }

            function decodeWeather(code) {
                switch (true) {
                    case (/^800$/.test(code))://clear
                        return 'clear-day';
                    case (/^611$/.test(code))://sleet
                        return 'sleet';
                    case (/^741$/.test(code))://fog
                        return 'fog';
                    case (/^2[0-9]{2}$/.test(code))://Thunderstorm
                        return 'storm';
                    case (/^3[0-9]{2}$/.test(code))://Drizzle
                        return 'drizzle';
                    case (/^5[0-9]{2}$/.test(code))://Rain
                        return 'rain';
                    case (/^6[0-9]{2}$/.test(code))://snow
                        return 'snow';
                    case (/^7[0-9]{2}$/.test(code))://Atmosphere
                        return 'wind';
                    case (/^80[0-9]{1}$/.test(code))://Clouds
                        return 'cloudy';
                    default:
                        return '';
                }
                return '';
            }
            icons.play();
            // tomorrow_date = weather.forecast[0].date;
        },
        error: function (error) { }
    });

    $('.widget-weather').each(function () {
        $(this).html('');
        weatherControl.setLocation($(this).attr('city'));
        $(this).append(weatherWidget);
    });

    // Weather
    $('.weather-forecast li a').on('click', function () {
        var day = $(this).attr('id');
        $('.weather-forecast li a, .weather-item').removeClass('active');
        $(this).addClass('active');
        $('.weather-item.' + day).addClass('active');
    });

    return weatherControl;
}


