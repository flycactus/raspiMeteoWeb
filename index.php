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
			
			this.formatData = function() {
				
					this.response = JSON.parse(this.xhr.responseText);
					this.temperatureArray =[];
					this.HumiditeArray=[];
					this.EarthArray=[];
					var labeArray = [];				
					
					// find 23h59:59 UTC of the day
				    var isoDate = new Date((this.response[0][0]+7200)*1000); 					
						
					day = isoDate.getUTCDate();
					month = isoDate.getUTCMonth();
					year = isoDate.getUTCFullYear();
					this.dayEnd = new Date(year,month,day,23,59);					
					this.dayEnd = this.dayEnd.setUTCHours(this.dayEnd.getUTCHours()-this.dayEnd.getTimezoneOffset()/60);
															
					if (this.dataName=="today"){
						this.dataStr = Highcharts.dateFormat('%e-%b',isoDate);
					}else{
						this.dataStr = "Semaine";
					}
					
					for (elem in this.response) {
												
						var dataTime =  new Date(this.response[elem][0]*1000);
						// take local time into account
						dataTime = dataTime.setUTCHours(dataTime.getUTCHours()-dataTime.getTimezoneOffset()/60);
						
						// Temperature						
						var localTemperature = [dataTime,this.response[elem][1]];
						this.temperatureArray.push(localTemperature);
						
						// Humidity
						var localHumidite = [dataTime,this.response[elem][2]];
						this.HumiditeArray.push(localHumidite);

						//earth humidity
						var localEarth = [dataTime,this.response[elem][3]];
						this.EarthArray.push(localEarth);
					}
					// document.write(dayEndstr);
			};
		};
		
		function genPlotSerie(sensorData){			
			sensorData.plotSerie1 = [{
					yAxis:0,
					valueSuffix:'°C',
					name: 'Température '+sensorData.dataStr,
					data: sensorData.temperatureArray,
					color: '#cd5b45',
					visible:true
				}, {
					yAxis:1,
					valueSuffix:'%',
					name: 'Humidité '+ sensorData.dataStr, 
					data: sensorData.HumiditeArray,
					color: '#21c1f0'
				},
				]
			sensorData.plotSerie2 =[{
					valueSuffix:'°C',
					name: 'Humidité de la terre '+sensorData.dataStr,
					data: sensorData.EarthArray,
					color: "#00cc00"
				},
				]
			// alert(plotSerie[0].name)
			return sensorData
		
		}
		
		function addPlotSerie(sensorData,sensorData2){
			// find 23h59:59 UTC of the day
			var isoDate = new Date((sensorData.response[0][0])*1000); 					
				
			day = isoDate.getUTCDate();
			month = isoDate.getUTCMonth();
			year = isoDate.getUTCFullYear();
			dayStart = new Date(year,month,day,0,0);
			dayStart = dayStart.setUTCHours(dayStart.getUTCHours()-dayStart.getTimezoneOffset()/60);
			
			//adjust time to fit with current Data
			i=0;
			for (elem in sensorData2.response) {
						
						var dataTime =  new Date(sensorData2.response[elem][0]*1000);
						// take local time into account
						dataTime = dataTime.setUTCHours(dataTime.getUTCHours()-dataTime.getTimezoneOffset()/60);
						
						dataTime = dataTime+dayStart;						
						
						// Temperature						
						localTemperature = [dataTime,sensorData2.response[elem][1]];
						sensorData2.temperatureArray[i]=(localTemperature);
						
						// Humidity
						localHumidite = [dataTime,sensorData2.response[elem][2]];
						sensorData2.HumiditeArray[i]=(localHumidite);

						//earth humidity
						localEarth = [dataTime,sensorData2.response[elem][3]];
						sensorData2.EarthArray[i]=(localEarth);
						i=i+1;
					}
			// document.write(sensorData2.EarthArray[140]+'---  ');		
			newTemp = { 
					yAxis:0,
					valueSuffix:'°C',
					name: 'Température '+sensorData2.dataStr,
					data: sensorData2.temperatureArray,
					color: '#cd9f45',
					visible:true
					}
			newHum = {
					yAxis:1,
					valueSuffix:'%',
					name: 'Humidité '+ sensorData2.dataStr, 
					data: sensorData2.HumiditeArray,
					color : '#0ea4d0'
				}
				
			newEarth = {
					valueSuffix:'°C',
					name: 'Humidité de la terre '+sensorData2.dataStr,
					data: sensorData2.EarthArray,
					color: "#008e00"				
					}
			sensorData.plotSerie1.push(newTemp)
			sensorData.plotSerie1.push(newHum)
			sensorData.plotSerie2.push(newEarth)
			
			return sensorData
		}
		// temperature + humidité
		function plotData1(sensorData) { 
			chart1 = new Highcharts.Chart({
			// $('#container1').highcharts({
				chart: {
					type: 'line',
					renderTo: 'container1'
				},
				title: {
					text: 'Météo Gardanne'
				},
				yAxis: [{
					title: {
						text: 'Température (°C)'
							}
					},
				   {title: {
						text: 'Humidité (%)'
					},
					opposite: true
					
				}],
				xAxis: {
					max : sensorData.dayEnd,
					type: 'datetime',								
				},
				tooltip: {	
					formatter: function() {	
						dataStrTtip = Highcharts.dateFormat('%Hh%M',new Date(this.x));	
						if (this.series.name == 'Température '+sensorData.dataStr)									
							return dataStrTtip + '<br/>' + 'Température'+' : '+this.y.toFixed(1) +'°C';
						else
							return dataStrTtip + '<br/>' + 'Humidité'+' : '+this.y.toFixed(1) +'%';
					},							
				},
				series: sensorData.plotSerie1 
				// [{
					// yAxis:0,
					// valueSuffix:'°C',
					// name: 'Température '+sensorData.dataStr,
					// data: sensorData.temperatureArray,
					// color: '#cd5b45',
					// visible:true
				// }, {
					// yAxis:1,
					// valueSuffix:'%',
					// name: 'Humidité '+ sensorData.dataStr, 
					// data: sensorData.HumiditeArray,
					// color: '#21c1f0'
				// },
				// ]
			});
			return sensorData;
		}

					
		// humidité de la terre					
		function plotData2(sensorData) { 
			// $('#container2').highcharts({
			chart2 = new Highcharts.Chart({
				chart: {type: 'line',	
						renderTo:'container2'
				},
				title: {text: ''},
				yAxis: {
					title: {
						text: 'Humidité de la Terre'
							}
				},
				xAxis: {	
						max : sensorData.dayEnd,
						type: 'datetime',
						dateTimeLabelFormats: { 
						hours: '%H:00',
					},
				},
				tooltip: {	
					formatter: function() {				
						dataStrTtip = Highcharts.dateFormat('%Hh%M',new Date(this.x));						
						return dataStrTtip + '<br/>' + this.series.name+' : '+this.y.toFixed(1);							
					},
				},
				series: sensorData.plotSerie2 
				// [{
					// valueSuffix:'°C',
					// name: 'Humidité de la terre '+sensorData.dataStr,
					// data: sensorData.EarthArray,
					// color: "#00cc00"
				// },
				// ]
			});
			return sensorData;
		}
		



		var TodayData = new sensorData("today","meteo.json");
		var AvgWeekData = new sensorData("week","7DaysAvg_meteo.json");
		
		// On souhaite juste récupérer le contenu du fichier, la méthode GET suffit amplement :
		TodayData.xhr.open('GET', TodayData.fileName);
		
		req1=0;
		req2=0;
		TodayData.xhr.addEventListener('readystatechange', function() { // On gère ici une requête asynchrone			
				
				TodayData.formatData();
				AvgWeekData.xhr.open('GET', AvgWeekData.fileName);
				TodayData = genPlotSerie(TodayData);
				if (req1 == 0) {
					req1=0;
					AvgWeekData.xhr.addEventListener('readystatechange', function() {
						
						AvgWeekData.formatData();
						if (req2 == 0){
							PlotData = addPlotSerie(TodayData,AvgWeekData);
							req2=1;
						}
						PlotData = plotData1(PlotData);
						PlotData = plotData2(PlotData);
						
						
					// AvgWeekData.xhr.send(null); // La requête est prête, on envoie tout !
					}	
					, false);
					AvgWeekData.xhr.send(null);
				}
		}, false);
			
		// 
		// addSerie(chart1)
		TodayData.xhr.send(null); // La requête est prête, on envoie tout !
		
		
		

		
 
    </script>
	
	<a href="street_map.php">OSM</a>

</body>
</html>