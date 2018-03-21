/**
* Сценарий для Majordomo
* Озвучивание прогноза погоды
* @author Alex Sokolov <admin@gelezako.com>
* @copyright Alex Sokolov http://blog.gelezako.com (c)
**/    

//Код будет корректно работать при следующий настройках в модуле app_openweather
//	Прогноз погоды: на 3 дня
//	Метод API: 5 дневный/3 часовой прогноз

include_once(DIR_MODULES . 'app_openweather/app_openweather.class.php');
    $openweather = new app_openweather();
    $openweather->get_weather(gg('ow_city.id'));
	
	    //вычисление окончание слова "градус" для влажности
	function degree($tempw){
      if($tempw >= 11 and $tempw <= 14){
      $degree=" градусов ";
      }
      else{

      while ($tempw > 9){
      $tempw=$tempw%10;
      }

      if($tempw == 0 or $tempw >= 5 and $tempw <= 9){
      $degree= градусов ; }
      if($tempw == 1){
      $degree= градус ; }
      if($tempw >= 2 and $tempw <= 4){
      $degree= градуса ; }
      }
     return $degree;
    }
    //$tNew = abs((float)getGlobal('ow_fact.temperature'));

	//вычисление окончание слова "процент" для влажности
	function humidity($tempw2){
          if($tempw2 >= 11 and $tempw2 <= 14){
          $tempcels=" процентов ";
          }
          else{

          while ($tempw2 > 9){
          $tempw2=$tempw2%10;
          }

         if($tempw2 == 0 or $tempw2 >= 5 and $tempw2 <= 9){
         $tempcels= процентов ; }
         if($tempw2 == 1){
         $tempcels= процент ; }
         if($tempw2 >= 2 and $tempw2 <= 4){
         $tempcels= процента ; }
         }
     return $tempcels;
    }
	
	//давление
    $pressure=(float)gg("ow_fact.pressure_mmhg");
    if ($pressure<728) {
     $stp=" Атмосферное давление пониженное. ";
    } elseif ($pressure>768) {
     $stp=" Атмосферное давление повышенное. ";
    } else {
     $stp=" Атмосферное давление нормальное. ";
    }
	
	//ветер
    $WindSpeed=(float)gg("ow_fact.wind_speed");
    if ($WindSpeed<1) {
     $stw=" Ветра нет.";
    } elseif ($WindSpeed<5) {
     $stw.=" Ветер слабый.";
    } elseif ($WindSpeed<10) {
     $stw=" Ветер сильный.";
    } elseif ($WindSpeed<15) {
     $stw=" Ветер очень сильный.";
    } else {
     $stw=" Ветер капец какой сильный ";
    }
	
//определяем на когда спрашиваем погоду
if((string)$params['weather']===(string)"погода" and $params['WeatherType']==""){
	if((string)$params['my-date-period']===(string)"" or (string)$params['my-date-period']===(string)"сейчас"){
		$temp=gg("ow_fact.temperature");
		$humidity=gg("ow_fact.humidity");
		$wind=gg("ow_fact.wind_speed");
		$weather_type=gg("ow_fact.weather_type");
		}
	if((string)$params['my-date-period']===(string)"сегодня" or (string)$params['my-date-period']==="Сегодня"){
		$temp=gg("ow_fact.temperature");
		$humidity=gg("ow_day0.humidity");
		$wind=gg("ow_day0.wind_speed");
		$weather_type=gg("ow_day0.weather_type");		
		}
    if((string)$params['my-date-period']===(string)"завтра"){
		$temp=gg("ow_day8.temperature");
		$humidity=gg("ow_day8.humidity");
		$wind=gg("ow_day8.wind_speed");
		$weather_type=gg("ow_day8.weather_type");}
    if((string)$params['my-date-period']==="послезавтра" or (string)$params['my-date-period']==="после завтра"){
		$temp=gg("ow_day16.temperature");
		$humidity=gg("ow_day16.humidity");
		$wind=gg("ow_day16.wind_speed");
		$weather_type=gg("ow_day16.weather_type");
		}
$status.="Погода в ".gg("ow_city.name")." на данный момент: ".$weather_type.". "." Температура: ".round($temp)."  ".degree($temp)." цельсия. "." Относительная влажность: ".round($humidity)." ".humidity($humidity).". ".$stp.$stw;
say($status,2);
}

	//если удалось определить время, то выдать результат
if(!empty($temp) and !empty($humidity) and !empty($humidity)){
	
		if((string)$params['WeatherType']===(string)"тепло" and $temp>24){
        say("Да, ".$params['my-date-period']." на улице тепло. Можно идти в шортах и футболке. Не забудь взять солнечные очки.",2);
        if((string)$weather_type===(string)"пасмурно" and $humidity>85) //влажность
      		say("И скорей всего будет дождь если уже не идёт.",2);
     	if($wind>10 and $wind<15)say(" И ещё меного дует ветер.",2);
        elseif($wind>16 and $wind<20)say(" И ещё поднялся сильный ветер.",2);
        elseif($wind>21)say(" Фигачит сильный ветер.",2);
    }
    

	else if((string)$params['WeatherType']===(string)"тепло" and $temp>0 and $temp<25){
         say($params['my-date-period']." не очень тепло, на улице достаточно прохладно.",2);
         if((string)$weather_type===(string)"пасмурно" and $humidity>85) //влажность
      		say("И скорей всего будет дождь если уже не идёт.",2);
     	if($wind>10 and $wind<15)say(" И ещё меного дует ветер.",2);
        elseif($wind>16 and $wind<20)say(" И ещё поднялся сильный ветер.",2);
        else if($wind>21)say(" Фигачит сильный ветер.",2);
    }

	elseif((string)$params['WeatherType']===(string)"холодно" and $temp>25){
     	say("Нет, ".$params['my-date-period']." на улице жара, не забудь взять солнечные очки.",2);
     	if((string)$weather_type===(string)"пасмурно" and $humidity>85) //влажность
      		say("Но скорей всего будет дождь если уже не идёт. Возьми зонтик.",2);
        if($wind>10 and $wind<15)say(" И ещё меного дует ветер.",2);
        else if($wind>16 and $wind<20)say(" И ещё поднялся сильный ветер.",2);
        else if($wind>21)say(" Фигачит сильный ветер.",2);
    }

	elseif((string)$params['WeatherType']===(string)"холодно" and $temp>0 and $temp<25){
        say("Да, ".$params['my-date-period']." на улице достаточно прохладно и ".$weather_type.". Накинь что-нибуть сверху.",2);
     	if((string)$weather_type===(string)"пасмурно" and $humidity>85) //влажность
      		say("И скорей всего будет дождь если уже не идёт. Возьми зонтик.",2);
        if($wind>10 and $wind<15)say(" И ещё меного дует ветер.",2);
        else if($wind>16 and $wind<20)say(" И ещё поднялся сильный ветер.",2);
        else if($wind>21)say(" Фигачит сильный ветер.",2);
    }

	elseif((string)$params['WeatherType']==(string)"холодно" and $temp>-10 and $temp<1){
        say("Да, ".$params['my-date-period']." на улице небольшой мороз и ".$weather_type. ". Одевайся теплее.",2);
        if((string)$weather_type==(string)"пасмурно" and $humidity>85) //влажность
      		say("И скорей всего будет снег если уже не идёт.",2);
        if($wind>10 and $wind<15)say(" И ещё меного дует ветер.",2);
        else if($wind>16 and $wind<20)say(" И ещё поднялся сильный ветер.",2);
        else if($wind>21)say(" Фигачит сильный ветер.",2);
    }

	elseif((string)$params['WeatherType']===(string)"холодно" and $temp<-11){
        say("Да, ".$params['my-date-period']." на улице мороз и ".$weather_type." Тёплая шуба не помешает.",2);
        if($weather_type=="пасмурно" and $humidity>85) //влажность
      		say("И скорей всего будет снег если уже не идёт.",2);
        if($wind>10 and $wind<15)say(" И ещё меного дует ветер.",2);
        else if($wind>16 and $wind<20)say(" И ещё поднялся сильный ветер.",2);
        else if($wind>21)say(" Фигачит сильный ветер.",2);
    }

	if((string)$params['WeatherType']===(string)"ветрено"){
        if($wind<10)say("Нет, ".$params['my-date-period']." ветра почти нет.",2);
     	if($wind>=10 and $wind<15)say("Да, ".$params['my-date-period']." меного дует ветер.",2);
        else if($wind>16 and $wind<20)say("Да, ".$params['my-date-period']." поднялся сильный ветер.",2);
        else if($wind>21)say("Да, ".$params['my-date-period']." фигачит сильный ветер.",2);
    }

	if((string)$params['WeatherType']===(string)"пасмурно"){
        if((string)$weather_type===(string)"пасмурно")say("Да, ".$params['my-date-period']." на улице пасмурно.",2);
        else say("Нет, на улице ".$weather_type,2);     
    }

	if((string)$params['WeatherType']===(string)"ясно"){
        if((string)$weather_type===(string)"ясно")say("Да, ".$params['my-date-period']." на улице ясно.",2);
        say("Нет, на улице ".$weather_type,2);
    }

	if((string)$params['WeatherType']===(string)"влажно")say($params['my-date-period']." влажность на улице ".$humidity." ".humidity($humidity),2);

	
	if((string)$params['WeatherType']===(string)"гроза" or (string)$params['WeatherType']===(string)"дождь" or (string)$params['WeatherType']===(string)"осадки" or (string)$params['WeatherType']===(string)"легкий дождь"){
     	if($weather_type===(string)"гроза" or $weather_type===(string)"дождь" or $weather_type===(string)"легкий дождь")
     	say("Да, на улице ".$params['my-date-period']." ".$weather_type,2);
     	elseif($weather_type===(string)"пасмурно")say("На улице пасмурно, возможно пойдёт дождь.",2);
     	else say("Нет, на улице не ожидаются осадки. ".$params['my-date-period']." будет ".$weather_type,2);
    }
     
}//конец глобальной проверки на определение времени
	

