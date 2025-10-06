/**
 * ---------------------------------------
 * amCharts 4.
 * ---------------------------------------
 */


function launchMapChart(url){


	$.ajax({
		url: url,
	    type: 'GET',
	    // headers: { 'X-XSRF-TOKEN' : csrfToken },
        beforeSend: function (xhr) {
            // xhr.setRequestHeader('X-CSRF-Token', csrfToken);
        },
	    data: {
           
		},
	    success: function (response) {
        	// alert('ok')
			var regionsColors = response;
			// console.log(regionsColors);

			var moyenne = regionsColors.totatlBudget / 5;
            console.log(regionsColors)
			regionsColors.localitesChart.forEach((region, index) => {
				if (region.budget <= moyenne) {
					region.color = "#FFCDD2";
				}
				if (region.budget > moyenne && region.budget <= (moyenne*2)) {
					region.color = "#E57373";
				}
				if (region.budget > moyenne*2 && region.budget <= (moyenne*3)) {
					region.color = "#E53935";
				}
				if (region.budget > (moyenne*3)) {
					region.color = "#B71C1C";
				}
			});

	        // alert(JSON.stringify(regionsColors));
	        launchMap(regionsColors.localitesChart);
	    }
	});

	// launchMap(regionsColors);
}
function getMapData(url,csrfToken=null,groupeAgeId,groupeModaliteId,modaliteId,sectionId,sousSectionId,sourceId,periodeId,indicateurId){
	$.ajax({
		//url: $('#url_get_regions').val(),
		url: url,
	    type: 'GET',
	    headers: { 'X-XSRF-TOKEN' : csrfToken },
        beforeSend: function (xhr) {
            xhr.setRequestHeader('X-CSRF-Token', csrfToken);
        },
	    data: {
            'groupeAgeId':groupeAgeId,
            'groupeModaliteId':groupeModaliteId,
            'modaliteId':modaliteId,
            'sectionId':sectionId,
            'sousSectionId':sousSectionId,
            'sourceId':sourceId,
            'periodeId':periodeId,
            'indicateurId':indicateurId,
		},
	    success: function (response) {
        	// alert('ok')
			var dbRegions = response.data;
			// alert(dbRegions)

	        var regionsColors = [];

	        for (var i = 0; i < dbRegions.length; i++) {
				var dbRegion = dbRegions[i];
				burkinaRegionsData.features.forEach((region, index) => {
					var regionCopy = region;
					if(region.properties.name == dbRegion.libelle){
						// alert(region)
						// console.log("===========================");
						region.properties.dbRegionId = dbRegion.id;

						var formattedRessources = jsFormatDevise(dbRegion.sum_valeur.toFixed(), "");
						region.properties.valeur = formattedRessources;

						regionsColors.push({id: region.properties.id, color: dbRegion.map_color});

					}

				});
	        }
	        // alert(JSON.stringify(regionsColors));
	        launchMap(regionsColors);
	    }
	});
}


var chart;
function launchMap(regionsColors){
	// alert(regionsColors)
	//Map
	chart = am4core.create("amcharts-burkina-map", am4maps.MapChart);

	// Set map definition
	chart.geodata = burkinaRegionsData;
	//chart.geodata = am4geodata_waSchools;
	//chart.geodata = am4geodata_burkinaFasoHigh;
	//chart.geodata = am4geodata_burkinaFasoLow;

	// Set projection
	chart.projection = new am4maps.projections.Miller();

	// Create map polygon series
	var polygonSeries = chart.series.push(new am4maps.MapPolygonSeries());

	// Make map load polygon (like country names) data from GeoJSON
	polygonSeries.useGeodata = true;

	
	// Configure series
	var polygonTemplate = polygonSeries.mapPolygons.template;
	//polygonTemplate.tooltipText = "{name}";
	// html='';
	// html+='<h1 style="color:#000000;"><strong>Région : {name}</strong></h1>';
	// html+='<p style="color:#000000;"><strong>Projets</strong> : Améliorer l’accès à l’eau,<br/> la gestion des déchets et la protection côtière,<br/> Développer les réseaux d’électrification,<br/> Favoriser l’éducation de base et l’insertion professionnelle,<br/> Renforcer les systèmes de protection sociale,<br/> Renforcer les réseaux de transport rural,<br/> Renforcer les réseaux de transport rural, Soutenir le secteur productif </p>';
	// html+='<h6 style="color:#000000;">Budget global : {budget}</h6>';
	// html+='<h6 style="color:#fff; background-color:{taux_color};padding:0px 5px 0px 5px;">Exécution financière : {taux}</h6>';
	// html+='<h6 style="color:#fff; background-color:{taux_color};padding:0px 5px 0px 5px;">Exécution physique : {taux_physique}</h6>';
	// polygonTemplate.tooltipHTML = html;

	polygonTemplate.propertyFields.fill = "color";
	polygonSeries.data = regionsColors;

	// Create hover state and set alternative fill color
	var hs = polygonTemplate.states.create("hover");
	hs.properties.fill = am4core.color("#F3E5F5");

	// Add zoom control
	chart.zoomControl = new am4maps.ZoomControl();
	// Disable zoom and pan
	chart.maxZoomLevel = 2;
	chart.seriesContainer.draggable = true;
	chart.seriesContainer.resizable = true;
	//chart.reverseGeodata = true;
	// $('#amcharts_burkina_map_dialog').css('display','none');

	// Create a new series for the points (cities or locations)
    var imageSeries = chart.series.push(new am4maps.MapImageSeries());

    // Configure the image series for points
    var imageTemplate = imageSeries.mapImages.template;
    var circle = imageTemplate.createChild(am4core.Circle);
    circle.radius = 20;
    circle.fill = am4core.color("#FF0000"); // Red color for points
    circle.stroke = am4core.color("#FFFFFF"); // White outline
    circle.strokeWidth = 2;
    circle.nonScaling = true; // Keep points from resizing during zoom

    // Tooltip for points
    imageTemplate.tooltipText = "{name}";

    // Create label for each point
    var label = imageTemplate.createChild(am4core.Label);
    label.text = "{count}"; // This will display the number
    label.horizontalCenter = "middle";
    label.verticalCenter = "middle";
    label.fontSize = 16;
    label.fill = am4core.color("#FFFFFF"); // Black color for the number

	// Auto resize circle based on label content
    label.events.on("validated", function(event) {
        var bbox = label.bbox;  // get the bounding box of the label
        var circleRadius = Math.max(bbox.width, bbox.height) / 2 + 10;  // calculate radius (with padding)
        circle.radius = circleRadius;
        imageTemplate.width = circleRadius * 2;
        imageTemplate.height = circleRadius * 2;
    });

	chart.events.on("zoomlevelchanged", function () {
		imageSeries.mapImages.each(function(image) {
			var textLength = image.children.getIndex(1).text.length; // Get label text length
			var zoomFactor = chart.zoomLevel;  // Get zoom level
			image.children.getIndex(0).radius = (textLength*3)*zoomFactor;  // Resize circle
		});
	});

    

    // Add points to the map
    imageSeries.data = regionsColors;

    imageTemplate.propertyFields.latitude = "latitude";
    imageTemplate.propertyFields.longitude = "longitude";

	imageTemplate.events.on("hit", function(ev) {
		// zoom to an object
		ev.target.series.chart.zoomToMapObject(ev.target);

		// get object info
		// console.log(ev.target.dataItem.dataContext);

		refreshTable(ev.target);
	});

	polygonTemplate.events.on("hit", function(ev) {
		// zoom to an object
		ev.target.series.chart.zoomToMapObject(ev.target);

		// get object info
		// console.log(ev.target.dataItem.dataContext);

		refreshTable(ev.target);
	});

	// Set the click event for the region list
	document.getElementById('regionList').addEventListener('change', function(e) {
		var selectedRegion = e.target.value; // Get the selected region

		if (selectedRegion) {
		  // Find the matching region in the imageSeries data
		  var regionData = imageSeries.data.find(region => region.name === selectedRegion);

		  if (regionData) {
			// Find the corresponding imageTemplate on the map
			var mapImage = imageSeries.mapImages.values.find(image => image.dataItem.dataContext.name === selectedRegion);

			if (mapImage) {
			  // Zoom to the image template
			  chart.zoomToMapObject(mapImage);
			  refreshTable(mapImage);
			}
		  }
		}
	});

	// Listen for the year select change
    document.getElementById('anneeList').addEventListener('change', function () {
		var selectedYear = parseInt(this.value, 10); // Parse the selected year as an integer
		var selectedregion = $('#regionList').val();
		

		//alert(selectedYear)
        // Filter the JSON to find relevant regions based on year selection
        const filteredRegion = imageSeries.data.find(item => {

			if (item.projects && Array.isArray(item.projects) && item.projects.length > 0) {
				return item.projects.some(function (project) {
					if(selectedregion){
						// console.log(selectedregion+' '+item.name);
						if(selectedregion == item.name){
							var startYear = parseInt(project.start_date.substring(0, 4), 10);
							var endYear = parseInt(project.end_date.substring(0, 4), 10);
	
							//alert(startYear)
							//alert(endYear)
							// Check if the selected year is between the start_date and end_date
							return selectedYear >= startYear && selectedYear <= endYear;
						}
					}else{
						var startYear = parseInt(project.start_date.substring(0, 4), 10);
						var endYear = parseInt(project.end_date.substring(0, 4), 10);
	
						// Check if the selected year is between the start_date and end_date
						return selectedYear >= startYear && selectedYear <= endYear;
					}
				});
			} else {
				console.log('Array is either undefined, null, or empty');
			}
        });

		//console.log(filteredRegion);
        // Zoom to the region
        if (filteredRegion) {
			// Zoom to the image template
			var mapImage = imageSeries.mapImages.values.find(image => image.dataItem.dataContext.id === filteredRegion.id);

			if (mapImage) {
			  refreshTable(mapImage);
			}
        }else{
			$('#mapTableBody').html('');
			$('#mapRegion').html('');
		}
    });


}

function refreshTable(mapImage){
	html = '';
	mapImage.dataItem.dataContext.projects.forEach((value, index) => {
		html += '<tr>';
			html += '<td>'+value.type+'</td>';
			html += '<td style="font-weight:bold;">'+value.name+'</td>';
			html += '<td>'+value.taux+'</td>';
			html += '<td>'+value.taux_physique+'</td>';
			html += '<td>'+value.updated_at+'</td>';
		html += '</tr>';
	});
	html += '<tr style="background-color:#024181;">';
		html += '<td class="text-white" colspan="2">Total</td>';
		html += '<td class="text-white">'+mapImage.dataItem.dataContext.taux+'</td>';
		html += '<td class="text-white">'+mapImage.dataItem.dataContext.taux_physique+'</td>';
		html += '<td class="text-white"></td>';
	html += '</tr>';
	$('#mapTableBody').html(html);
	$('#mapRegion').html(mapImage.dataItem.dataContext.name);
}

// Add export menu
function savePDF(){
	chart.exporting.export("pdf");
}

function saveIMAGE(){
	chart.exporting.export("png");
}

function jsFormatDevise(prix, devise){

	if (prix == null || prix == "" || !prix) {
		//return "Non précisé";
		return prix+" "+devise;
	}
	else {

		var signe = "";

		if (prix.toString().startsWith("-")) {
			signe = "-";
			prix = prix.toString().substr(1);
		}

		var res = prix;

		if(prix > 0 && prix <= 999) // 1 à 999
		{
			res = prix+" "+devise;
		}
		else if(prix > 999 && prix <= 9999) // 1.000 à 9.999
		{
			res = prix.toString().substr(0, 1)+" "+prix.toString().substr(1)+" "+devise;
		}
		else if(prix > 9999 && prix <= 99999) // 10.000 à 99.999
		{
			res = prix.toString().substr(0, 2)+" "+prix.toString().substr(2)+" "+devise;
		}
		else if(prix > 99999 && prix <= 999999) // 100.000 à 999.999
		{
			res = prix.toString().substr(0, 3)+" "+prix.toString().substr(3)+" "+devise;
		}
		else if(prix > 999999 && prix <= 9999999) // 1.000.000 à 9.999.999
		{
			res = prix.toString().substr(0, 1)+" "+prix.toString().substr(1, 3)+" "+prix.toString().substr(4)+" "+devise;
		}
		else if(prix > 9999999 && prix <= 99999999) // 10.000.000 à 99.999.999
		{
			res = prix.toString().substr(0, 2)+" "+prix.toString().substr(2, 3)+" "+prix.toString().substr(5)+" "+devise;
		}
		else if(prix > 99999999 && prix <= 999999999) // 100.000.000 à 999.999.999
		{
			res = prix.toString().substr(0, 3)+" "+prix.toString().substr(3, 3)+" "+prix.toString().substr(6)+" "+devise;
		}
		else if(prix > 999999999 && prix <= 9999999999) // 1.000.000.000 à 9.999.999.999
		{
			res = prix.toString().substr(0, 1)+" "+prix.toString().substr(1, 3)+" "+prix.toString().substr(4, 3)+" "+prix.toString().substr(7)+" "+devise;
		}
		else if(prix > 9999999999 && prix <= 99999999999) // 10.000.000.000 à 99.999.999.999
		{
			res = prix.toString().substr(0, 2)+" "+prix.toString().substr(2, 3)+" "+prix.toString().substr(5, 3)+" "+prix.toString().substr(8)+" "+devise;
		}
		else if(prix > 99999999999 && prix <= 999999999999) // 100.000.000.000 à 999.999.999.999
		{
			res = prix.toString().substr(0, 3)+" "+prix.toString().substr(3, 3)+" "+prix.toString().substr(6, 3)+" "+prix.toString().substr(9)+" "+devise;
		}
		else
		{
			res = prix+" "+devise;
		}

		return signe+""+res;
	}
}
/**********************************************************************************/
