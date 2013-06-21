<?php

	namespace Controller;
	
	use \Config\Config;
	
	class Chart {
		
		const _HIGHCHARTS_JS = "/highcharts.js"; //percorso per js highcharts
		
		const _EXPOSRTING_JS = "/modules/exporting.js"; //percorso per exporting
		
		private $strTitle = ""; //titolo
		private $strSubTitle = ""; //subtitle
		
		private $strXLabels = ""; //etichette asse x
		
		private $arYAxises = array(); //array contenente assi y
		
		private $strTooltip = ""; //stringa contenente tooltip
		
		private $arSeries = array(); //array contenente le serie
		
		private $strLegend = ""; //legenda
		
		
		public function __construct() { //costruttore
			
		}
		
		
		//FUNZIONI PER IMPOSTARE IL GRAFICO
		
		public function addChartTitle($strTitle) { //aggiungo titolo
			$this->strTitle = $strTitle;
		}
		
		public function addChartSubTitle($strSubTitle) { //sottotitolo
			$this->strSubTitle = $strSubTitle;
		}
		
		public function addXLabels($arXLabels) { //aggiungo etichette asse x
			$this->strXLabels = "";
			foreach($arXLabels as $value) {
				
				$this->strXLabels .= "'";
				$this->strXLabels .= $value;
				$this->strXLabels .= "',";
				
			}
			
			$this->strXLabels = substr($this->strXLabels, 0, strlen($this->strXLabels) -1);
		}
		
		public function addYAxis($strName, $strSuffix, $strColor, $strOpposite) {//aggiungo un asse
			
			//stringa in notazione heredoc
			$strAxis = <<<STRING
			
			{
                title: {
                    text: '$strName',
                    style: {
                        color: '$strColor'
                    }
                },
                labels: {
                    formatter: function() {
                        return this.value +'$strSuffix';
                    },
                    style: {
                        color: '$strColor'
                    }
                },
				opposite: $strOpposite
    
            }
STRING;
			
			$this->arYAxises[] = $strAxis;
			
			
		}
		
		public function addTooltip($strShared = "true") { //addtooltip
			
			$this->strTooltip = <<<STRING
			
			tooltip: {
                shared: $strShared
            }
STRING;
			
		}
		
		public function addSerie($arValue, $strName, $strColor, $intAxis, $strTooltipSuffix) {//aggiungo una serie
			
			//stringa in notazione heredoc
			$strSerie = <<<STRING
			
			{
                name: '$strName',
                color: '$strColor',
                type: 'line',
                yAxis: $intAxis,
				data: [
STRING;

			foreach($arValue as $value) {
				$strSerie .= $value . ", ";
			}
			
			$strSerie = substr($strSerie, 0, strlen($strSerie) -2);
			
			$strSerie .= <<<STRING
			
                ],
                tooltip: {
                    valueSuffix: '$strTooltipSuffix'
                }
			}
STRING;
			
			$this->arSeries[] = $strSerie;
			
			
		}
		
		
		public function addLegend($strLayout, $strAlign, $intX, $intY, $strFloating, $strBgColor) {
		
		
		$this->strLegend = <<<STRING
		legend: {
                layout: '$strLayout',
                align: '$strAlign',
                x: $intX,
                verticalAlign: 'top',
                y: $intY,
                floating: $strFloating,
                backgroundColor: '$strBgColor'
            }
STRING;

		}
		
		
		
		
		//FUNCZIONE PER OTTENERE IL GRAFICO
		
		public function getJSChart($strElementId) { //ottengo il grafico come coidice js da eseguire sul browser
		
			$strChart = <<<STRING
			
		$('$strElementId').highcharts({
            chart: {
                zoomType: 'line'
            },
            title: {
                text: '$this->strTitle',
				style: {
					color: '#1D1D1D',
					fontSize: '3em',
					fontFamily: 'Open Sans Condensed, sans-serif'
				}
				
            },
            subtitle: {
                text: '$this->strSubTitle',
				style: {
					color: '#1D1D1D',
					fontSize: '2em',
					fontFamily: 'Open Sans Condensed, sans-serif'
				}
            },
            xAxis: [{
                categories: [$this->strXLabels]
			}],
            yAxis: [
			
STRING;
			foreach($this->arYAxises as $strAxis) {
				$strChart .= $strAxis . ", ";
			}
		
		$strChart = substr($strChart, 0, strlen($strChart) -2);
		
		$strChart .= <<<STRING
			],
            
			$this->strTooltip/*,
			$this->strLegend*/,
		
			series: [
STRING;
		
			foreach($this->arSeries as $strSerie) {
				$strChart .= $strSerie . ", ";
			}
		
		$strChart = substr($strChart, 0, strlen($strChart) -2);
		
		$strChart .= <<<STRING
			
			]
        });
STRING;

			return $strChart;
			
		}
		
		public function getScriptTags() { //ottengo codice script da invludere nella pagina per avere il codice js di HighCharts
		
			$strScriptTags = "<script src=\"" . Config::_PATH_JS_FOLDER . Chart::_HIGHCHARTS_JS . "\"></script>";
			$strScriptTags .= "<script src=\"" . Config::_PATH_JS_FOLDER . Chart::_EXPOSRTING_JS . "\"></script>";
			
			return $strScriptTags;
			
		}
		
		
	}