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

		function sensorData (dataName,fileName) {
			this.dataName = dataName;
			this.fileName = fileName;
			this.xhr = new XMLHttpRequest();
			this.plotData = function() {
				var response = JSON.parse(this.xhr.responseText);
					
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
					dayEnd = new Date(year,month,day,23,59);
					dayEnd = dayEnd.setUTCHours(dayEnd.getUTCHours()-dayEnd.getTimezoneOffset()/60);
					
					if (this.dataName=="today"){
						var dataStr = Highcharts.dateFormat('%e-%b',isoDate);
					}
					
					for (elem in response) {
												
						var dataTime =  new Date(response[elem][0]*1000);
						// take local time into account
						dataTime = dataTime.setUTCHours(dataTime.getUTCHours()-dataTime.getTimezoneOffset()/60);
						
						// Temperature						
						var localTemperature = [dataTime,response[elem][1]];
						temperatureArray.push(localTemperature);
						
						// Humidity
						var localHumidite = [dataTime,response[elem][2]];
						HumiditeArray.push(localHumidite);

						//earth humidity
						var localEarth = [dataTime,response[elem][3]];
						EarthArray.push(localEarth);
					}
					// document.write(dayEndstr);
					
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
								max : dayEnd,
								type: 'datetime',								
							},
							tooltip: {	
								formatter: function() {				
									var dataStr = Highcharts.dateFormat('%Hh%M',new Date(this.x));
								if (this.series.name == 'Température')									
									return dataStr + '<br/>' + this.series.name+' : '+this.y.toFixed(1) +'°C';
								else
									return dataStr + '<br/>' + this.series.name+' : '+this.y.toFixed(1) +'%';
								},							
							},
							series: [{
								yAxis:0,
								valueSuffix:'°C',
								name: 'Température ' + dataStr ,
								data: temperatureArray,
								color: '#cd5b45',
								visible:true
							}, {
								yAxis:1,
								valueSuffix:'%',
								name: 'Humidité '+ dataStr, 
								data: HumiditeArray
							},
							]
						});
					});
					
					// humidité de la terre
					
					$(function () { 
						$('#container2').highcharts({
							chart: {type: 'line'},
							title: {text: ''},
							yAxis: {
								title: {
									text: 'Humidité de la Terre'
										}
							},
							xAxis: {	
									max : dayEnd,
									type: 'datetime',
									dateTimeLabelFormats: { 
									hours: '%H:00',
								},
							},
							tooltip: {	
								formatter: function() {				
									var dataStr = Highcharts.dateFormat('%Hh%M',new Date(this.x));						
									return dataStr + '<br/>' + this.series.name+' : '+this.y.toFixed(1);									
								},
							},
							series: [{
								valueSuffix:'°C',
								name: 'Humidité de la terre '+dataStr,
								data: EarthArray,
								color: "#00cc00"
							},
							]
						});
					});
			};
		}



		var TodayData = new sensorData("today","meteo.json");
		
		// On souhaite juste récupérer le contenu du fichier, la méthode GET suffit amplement :
		TodayData.xhr.open('GET', TodayData.fileName);
		TodayData.xhr.addEventListener('readystatechange', function() { // On gère ici une requête asynchrone			
			if (TodayData.xhr.readyState === 4 && TodayData.xhr.status === 200) { // Si le fichier est chargé sans erreur
				
				TodayData.plotData()					
				
				}
		}, false);
			
		
		TodayData.xhr.send(null); // La requête est prête, on envoie tout !
		
 
    </script>
	
	<a href="street_map.php">OSM</a>

</body>
</html>