// JavaScript Document

var componentselect = null ;
var criteriaselect = null ;
var selectorselect = null ;

window.addEvent('domready', function() {
	
	componentselect = new Array();
	criteriaselect = new Array();
	selectorselect = new Array();
	
	componentselect[1] = $('jform_params_component');
	criteriaselect[1] = $('jform_params_criteria');
	selectorselect[1] = $('jform_params_selector');
	
	componentselect[2] = $('jform_params_component2');
	criteriaselect[2] = $('jform_params_criteria2');
	selectorselect[2] = $('jform_params_selector2');
	
	componentselect[3] = $('jform_params_component3');
	criteriaselect[3] = $('jform_params_criteria3');
	selectorselect[3] = $('jform_params_selector3');
	
	componentselect[4] = $('jform_params_component4');
	criteriaselect[4] = $('jform_params_criteria4');
	selectorselect[4] = $('jform_params_selector4');

	componentselect[1].addEvent('change', function(e) {
		
		changecriteriaselect(1) ;
		changeselectorselect(1) ;
		
	});
	
	if(componentselect[1].options[componentselect[1].selectedIndex].value != ""){
		
		changecriteriaselect(1) ;
		changeselectorselect(1) ;
		
		if(modulecriteria != "0" && modulecriteria) criteriaselect[1].getElement("option[value=" + modulecriteria + "]").selected = true ;
		if(moduleselector != "0" && moduleselector) selectorselect[1].getElement("option[value=" + moduleselector + "]").selected = true ;
	}
	
	if(componentselect[2]){
		componentselect[2].addEvent('change', function(e) {
			
			changecriteriaselect(2) ;
			changeselectorselect(2) ;
			
		});
		
		if(componentselect[2].options[componentselect[2].selectedIndex].value != ""){
			
			changecriteriaselect(2) ;
			changeselectorselect(2) ;
			
			if(modulecriteria2 != "0" && modulecriteria2) criteriaselect[2].getElement("option[value=" + modulecriteria2 + "]").selected = true ;
			if(moduleselector2 != "0" && moduleselector2) selectorselect[2].getElement("option[value=" + moduleselector2 + "]").selected = true ;
		}
	}
	
	if(componentselect[3]){
		componentselect[3].addEvent('change', function(e) {
			
			changecriteriaselect(3) ;
			changeselectorselect(3) ;
			
		});
		
		if(componentselect[3].options[componentselect[3].selectedIndex].value != ""){
			
			changecriteriaselect(3) ;
			changeselectorselect(3) ;
			
			if(modulecriteria3 != "0" && modulecriteria3) criteriaselect[3].getElement("option[value=" + modulecriteria3 + "]").selected = true ;
			if(moduleselector3 != "0" && moduleselector3) selectorselect[3].getElement("option[value=" + moduleselector3 + "]").selected = true ;
		}
	}
	
	if(componentselect[4]){
		componentselect[4].addEvent('change', function(e) {
			
			changecriteriaselect(4) ;
			changeselectorselect(4) ;
			
		});
		
		if(componentselect[4].options[componentselect[4].selectedIndex].value != ""){
			
			changecriteriaselect(4) ;
			changeselectorselect(4) ;
			
			if(modulecriteria4 != "0" && modulecriteria4) criteriaselect[4].getElement("option[value=" + modulecriteria4 + "]").selected = true ;
			if(moduleselector4 != "0" && moduleselector4) selectorselect[4].getElement("option[value=" + moduleselector4 + "]").selected = true ;
		}
	}
	
});

function changecriteriaselect(who){
	var component = componentselect[who].options[componentselect[who].selectedIndex].value ;
	
	criteriaselect[who].empty();
	
	if(component != ""){
	
		var n = criteriaoptions[component].length ;
		
		for(var i = 0; i < n; i++){
		
			var newoption = new Option(criteriaoptions[component][i]['name'], criteriaoptions[component][i]['value']);
			criteriaselect[who].add(newoption, null);
		
		}
	}
	else{
			var newoption = new Option('Please select a content provider first', 0);
			criteriaselect[who].add(newoption, null);
	
	}
}

function changeselectorselect(who){
	var component = componentselect[who].options[componentselect[who].selectedIndex].value ;
		
	selectorselect[who].empty();
	
	if(component != ""){
	
		var n = selectoroptions[component].length ;
		
		for(var i = 0; i < n; i++){
		
			var newoption = new Option(selectoroptions[component][i]['name'], selectoroptions[component][i]['value']);
			selectorselect[who].add(newoption, null);
		
		}
	}
	else{
			var newoption = new Option('Please select a content provider first', 0);
			selectorselect[who].add(newoption, null);
	
	}
}
