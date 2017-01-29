<!DOCTYPE html>
<html>
<head>
    <title>Raspi - Météo Gardanne</title> 
	<meta charset="utf-8" />
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
	<script src="http://code.highcharts.com/highcharts.js"></script>
	<style>
	  body {
		text-align: center;
	  }
	</style>
</head>

<body>


	<div id="container1" style="width:100%; height:300px;"></div>	
	<div id="container2" style="width:100%; height:300px;"></div>	
    <script>
	
		var file = "meteo.json"
		var fileHier = "meteo_hier.json"
		
		var xhr = new XMLHttpRequest();
		var xhr_hier = new XMLHttpRequest();

		// On souhaite juste récupérer le contenu du fichier, la méthode GET suffit amplement :
		xhr.open('GET', file);
		xhr.addEventListener('readystatechange', function() { // On gère ici une requête asynchrone			
			if (xhr.readyState === 4 && xhr.status === 200) { // Si le fichier est chargé sans erreur

					var response = JSON.parse(xhr.responseText);
					
					var temperatureArray =[];
					var HumiditeArray=[];
					var EarthArray=[];
					var labeArray = [];				
					
					// find 23h59:59 UTC of the day
				    var isoDate = new Date((response[0][0]+7200)*1000); 					
					// var isoDate = new Date((response[0][0])*1000); 					
					day = isoDate.getUTCDate();
					month = isoDate.getUTCMonth();
					year = isoDate.getUTCFullYear();
					dayEndstr = new Date(year,month,day,23,59);
					dayEnd = (dayEndstr.getTime())/1000+7200;
					dayStartStr = new Date(year,month,day,0,0);
					dayStart = (dayStartStr.getTime())/1000+7200;
					
					// dayEnd = (dayEndstr.getTime()+)/1000;
					
					for (elem in response) {
						// var timeWOffset = response[elem][0]+7200;
						// var timeWOffset = response[elem][0];
						
						// Temperature						
						var tempTime =  new Date(response[elem][1]*1000);
						// take local time into account
						tempTime = tempTime.setUTCHours(tempTime.getUTCHours()-tempTime.getTimezoneOffset()/60);
						var localTemperature = [tempTime,response[elem][1]];
						temperatureArray.push(localTemperature);
						
						// Humidity
						var humTime =  new Date(response[elem][2]*1000);
						// take local time into account
						humTime = humTime.setUTCHours(humTime.getUTCHours()-humTime.getTimezoneOffset()/60);
						var localHumidite = [humTime,response[elem][2]];
						HumiditeArray.push(localHumidite);
						
						var earthTime =  new Date(response[elem][0]*1000);
						// take local time into account
						earthTime = earthTime.setUTCHours(earthTime.getUTCHours()-earthTime.getTimezoneOffset()/60);
						var localEarth = [earthTime,response[elem][3]];
						EarthArray.push(localEarth);
					}
					// document.write(EarthArray+" "+dayEnd+" -- "+1474847999);
					// document.write(earthTime);
					
					// temperature + humidité
					$(function () { 
						$('#container1').highcharts({
							chart: {
								type: 'line'
							},
							title: {
								text: 'Météo Gardanne'
							},
							yAxis: [{
								title: {
									text: 'Température (°C)'
										}
								},
							{   title: {
									text: 'Humidité (%)'
								},
								opposite: true
								
							}],
							xAxis: {
								,
							},
							tooltip: {
								formatter: function() {
											var myDate =  new Date(this.x*1000);
											data = this.y.toFixed(1) 
											minute = myDate.getUTCMinutes()
											hour = myDate.getUTCHours()+myDate.getTimezoneOffset()/60
											if (hour < 10)
												hourStr = '0'+hour
											else
												hourStr = hour
												 
											minute = minute.toFixed(0)
											if (minute < 10)
												minStr = '0'+minute
											else
												minStr = minute
											if (this.series.name == 'Température' || this.series.name == 'Température Hier')
												return hourStr+'h'+minStr+' : <br/>'+this.series.name+' : '+data+ '°C'
											else
												return hourStr+'h'+minStr+' : <br/>'+this.series.name+' : '+data+ '%'
											
									},
							},
							series: [{
								yAxis:0,
								valueSuffix:'°C',
								name: 'Température',
								data: temperatureArray,
								color: '#cd5b45',
								visible:true
							}, {
								yAxis:1,
								valueSuffix:'%',
								name: 'Humidité',
								data: HumiditeArray
							},
							]
						});
					});
					
					// humidité de la terre
					
					$(function () { 
						$('#container2').highcharts({
							chart: {
								type: 'line'
							},
							title: {
								text: ''
							},
							yAxis: {
								title: {
									text: 'Humidité de la Terre'
										}
							},
							xAxis: {	
								// max : dayEnd,
								// min: dayStart,
								// labels: {
									// formatter: function() {
											// var myDate =  new Date(this.value*1000);
											// minute = myDate.getUTCMinutes()
											// hour = myDate.getUTCHours()+myDate.getTimezoneOffset()/60
											// if (hour < 10)
												// hourStr = '0'+hour
											// else
												// hourStr = hour
												 
											// minute = minute.toFixed(0)
											// if (minute < 10)
												// minStr = '0'+minute
											// else
												// minStr = minute
											// return hourStr+'h'+minStr
									// }
									type: 'datetime',
									dateTimeLabelFormats: { 
									hours: '%H:00',
								},
							},
							tooltip: {
								type: 'datetime',
									dateTimeLabelFormats: { 
									hours: '%H:00',
								},
								// formatter: function() {
											// var myDate =  new Date(this.x*1000);
											
											// data = this.y.toFixed(1) 
											// minute = myDate.getUTCMinutes()
											// hour = myDate.getUTCHours()+myDate.getTimezoneOffset()/60
											// if (hour < 10)
												// hourStr = '0'+hour
											// else
												// hourStr = hour
												 
											// minute = minute.toFixed(0)
											// if (minute < 10)
												// minStr = '0'+minute
											// else
												// minStr = minute

											// return hourStr+'h'+minStr+' : <br/>'+this.series.name+' : '+data
											
									// },
							},
							series: [{
								valueSuffix:'°C',
								name: 'Humidité de la terre',
								data: EarthArray,
								color: "#00cc00"
							},
							]
						});
					});
					
					

				}
		}, false);
			
		
		xhr.send(null); // La requête est prête, on envoie tout !
		
 
    </script>
	
	<a href="street_map.php">OSM</a>

</body>
</html>