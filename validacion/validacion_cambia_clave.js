// JavaScript Document
function val_clave()
{

	var txt_anterior = $("#txt_anterior").val();
		if (txt_anterior == ''){
		
				setTimeout(function(){
				$.Notify({style: {background: 'red', color: 'white'}, caption: '<font size="6"><b><u>Error!!!</u></b></font>', width: 200, height: 100, timeout: 8000, content: "<font size='6'>Debe Colocar la clave anterior.</font>"});
				}, 700);						
			
				$("#txt_anterior").focus();
				return 
		}
		
		var txt_nueva = $("#txt_nueva").val();
		if (txt_nueva == '' || txt_nueva.length < 6){
		
				setTimeout(function(){
				$.Notify({style: {background: 'red', color: 'white'}, caption: '<font size="6"><b><u>Error!!!</u></b></font>', width: 200, height: 100, timeout: 8000, content: "<font size='6'>Debe Colocar la clave nueva con al menos 6 car\u00e1cteres.</font>"});
				}, 700);						
			
				$("#txt_nueva").focus();
				return 
		}
		
		var txt_confirmar = $("#txt_confirmar").val();
		if (txt_confirmar == ''){
		
				setTimeout(function(){
				$.Notify({style: {background: 'red', color: 'white'}, caption: '<font size="6"><b><u>Error!!!</u></b></font>', width: 200, height: 100, timeout: 8000, content: "<font size='6'>Debe confirmar la clave nueva con al menos 6 car\u00e1cteres.</font>"});
				}, 700);						
			
				$("#txt_confirmar").focus();
				return 
		}
		
		var texto_anterior = $("#hd_anterior").val();
		if(texto_anterior != txt_anterior){
		
			setTimeout(function(){
				$.Notify({style: {background: 'red', color: 'white'}, caption: '<font size="6"><b><u>Error!!!</u></b></font>', width: 200, height: 100, timeout: 8000, content: "<font size='6'>Clave anterior no coincide.</font>"});
				}, 700);						
			
				$("#txt_anterior").focus();
				return 
		
		}
		
		if(txt_anterior == txt_nueva){
			setTimeout(function(){
				$.Notify({style: {background: 'red', color: 'white'}, caption: '<font size="6"><b><u>Error!!!</u></b></font>', width: 200, height: 100, timeout: 8000, content: "<font size='6'>Clave anterior y nueva no deben coincidir.</font>"});
				}, 700);						
			
				$("#txt_nueva").focus();
				return 
		}
		
		if(txt_confirmar != txt_nueva){
			setTimeout(function(){
				$.Notify({style: {background: 'red', color: 'white'}, caption: '<font size="6"><b><u>Error!!!</u></b></font>', width: 200, height: 100, timeout: 8000, content: "<font size='6'>Confirmaci\u00f3n de clave no coincide.</font>"});
				}, 700);						
			
				$("#txt_confirmar").focus();
				return 
		}
		
		$('#btn_guardar').attr('disabled','-1');
		
		$.ajax({
		type: "POST",
		dataType:"html",
		url: "datos/guarda_clave.php",	
		data: "hd_cedula="+$('#hd_cedula').val()+
			  "&txt_nueva="+$('#txt_nueva').val()
		  ,
		cache: false,			
		success: function(result) {	
			//alert(result);
			resultado=result.split("/");
			if(resultado[1] == 1)
			{
				setTimeout(function(){
				$.Notify({style: {background: 'blue', color: 'white'}, caption: '<font size="6"><b><u>Informaci\u00f3n!!!</u></b></font>', width: 200, height: 100, timeout: 4000, content: "<font size='6'>"+resultado[0]+"</font>"});
				}, 700);
				
				setTimeout(function(){location.href='index.php'}, 2000);
			}
			else
			{

				setTimeout(function(){
				$.Notify({style: {background: 'red', color: 'white'}, caption: '<font size="6"><b><u>Informaci\u00f3n!!!</u></b></font>', width: 200, height: 100, timeout: 8000, content: "<font size='6'>"+resultado[0]+"</font>"});
				}, 700);
				
				setTimeout(function(){location.href='index.php'}, 3000);

			}
	
			
		},			
		error: function(error) {				
			alert("	jquery - Algunos problemas han ocurrido. Por favor, inténtelo de nuevo más tarde: " + error);			
		}
	
		});


	
}