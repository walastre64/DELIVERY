// JavaScript Document



function ventana_modal_2(valor,titulo,mensaje,cantidad_btn)
{		
	var vmodal;		
	vmodal = '';
		 <!-- ventana Modal -->
	vmodal +='<div id="myModal" data-backdrop="static" data-keyboard="false" style="margin-top: 200px;" class="modal">';
	
	  <!-- Modal content -->
	vmodal +='  <div id="div_modal_content" class="modal-content" >';
			
			<!-- Modal Header -->
	vmodal +='		<div id="14" class="modal-header" style="background-color:#b1d8eb;">';
	vmodal +='			<h4 id="titulo" class="modal-title">'+ titulo+'</h4>';
	vmodal +='				<button type="button" class="close" data-dismiss="modal">&times;</button>';
	vmodal +='		</div>';
			<!-- FIN Modal Header -->	
	
			<!-- Modal body -->	
	vmodal +='   	<div id="div_cuerpo_modal" class="modal-body">';
	vmodal +='			<p id="mensaje">'+mensaje+'</p>';
	vmodal +='		</div>';
			<!-- FIN Modal body -->	

<!-- Modal footer -->
	if(valor == 999991){  		
		vmodal +='	   <div id="15" class="modal-footer" style="background-color:#b1d8eb;">';
		vmodal +='   		<button id="btn_modal_aceptar" type="button" class="btn btn-info" data-dismiss="modal" data-backdrop="false" >Aceptar</button>';
		vmodal +='	   </div>';
	}else if(valor == 999992) {
		vmodal +='	   <div id="15" class="modal-footer" style="background-color:#b1d8eb;">';
		vmodal +='   		<button id="btn_modal_aceptar"  type="button" class="btn btn-primary"   data-dismiss="modal" data-backdrop="false" >Aceptar</button>';
		vmodal +='			<button id="btn_modal_cancelar" type="button" class="btn btn-secondary" data-dismiss="modal" data-backdrop="false" >Cancelar</button>';
		vmodal +='	   </div>';
	}else if(valor == 999993){
		vmodal +='	   <div id="15" class="modal-footer" style="background-color:#b1d8eb;">';
		vmodal +='   		<button id="btn_modal_aceptar"  type="button" class="btn btn-primary"   data-dismiss="modal" data-backdrop="false" >Aceptar</button>';
		vmodal +='			<button id="btn_modal_cancelar" type="button" class="btn btn-secondary" data-dismiss="modal" data-backdrop="false" >Cancelar</button>';
		vmodal +='			<button id="btn_modal_otro" 	type="button" class="btn btn-danger" data-dismiss="modal" data-backdrop="false" >Cancelar</button>';
		vmodal +='	   </div>';
	}
		
		
		   <!-- FIN Modal footer -->  
	vmodal +='  </div>';
		
		return vmodal;
}



var tiempo
function ini() {
  tiempo = setTimeout('location="../index.php"',500000); // <- 5 minutos --  ejemplo - 5000(MILISEGUNDOS) =  5 segundos
  //tiempo = setTimeout('location="../index.php"',30000000000); // <- 5 minutos --  ejemplo - 5000(MILISEGUNDOS) =  5 segundos
}

function parar() {
  clearTimeout(tiempo);
  tiempo = setTimeout('location="../index.php"',500000); // <- 5 minutos -- ejemplo - 5000(MILISEGUNDOS) =  5 segundos
  //tiempo = setTimeout('location="../index.php"',3000000000); // <- 5 minutos -- ejemplo - 5000(MILISEGUNDOS) =  5 segundos
}



function mensaje(tipo,titulo,mensaje)
{			
	setTimeout(function(){
	$.Notify({type: tipo  , caption: titulo, content: mensaje,width: 300, height: 100, top:'5px'});
	}, 750);		
	return false;	
}


function ventana_emergente(mensaje,icon)
{
	switch (icon)
	{
	case 1:
	  img = 'ui-icon-circle-close';
	  break;
	case 2:
	  img = 'ui-icon-circle-check';
	  break;
	default:
	  img = 'ui-icon-circle-triangle-e';
	}
	
	dial = $('<div id="div_error" title="Mensaje del Sistema"><p> <span class="ui-icon '+img+'" style="float:left; margin:0 7px 50px 0;"></span>'+mensaje+'</p></div>');
	
	dial.dialog({
			modal: true,
			width: 500,
			height: 300,
			show: "blind",
			hide: "shake",
			buttons:{
			Ok: function() {
				$( this ).dialog( "close" );
						}
			//,Cerrar: function(){}
			}
	});
}

function solonumeros(e)
{

// numeros y (/) 
 var key;

	 if(window.event) // IE
	 {
		 key = e.keyCode;
	 }
	 else if(e.which) // Netscape/Firefox/Opera
	 {
		 key = e.which;
	 }



	if(key != 8 )
	{
		 if (key <= 44 || key > 45 && key < 47  ||  key == 46  || key > 57 )
			{
			  return false;
			}
			
	}
 return true;
}




function solonumeros_y_negativos(e)
{

 var key;

 if(window.event) // IE
 {
 	 key = e.keyCode;
 }
 else if(e.which) // Netscape/Firefox/Opera
 {
 	 key = e.which;
 }


if(key != 8)
{
	 if (key <= 44 || key >= 46 && key < 48  ||  key == 46  || key > 57 )
		{
		  return false;
		}
}
 return true;
}

function vali_cont(svalor,blancos,nemail)
{
 	 var narr  = 0;
	 var nblanco = 0;
	 var especial = "@";
	 var npunto  = 0;
	 for (k=0; k < svalor.length; k++)  	 {
	 	 chr=svalor.charAt(k);	
		 if (chr == " ")		 {
		     if  (blancos == 0)  {return (false); }	
		     if ((k = 0) && (svalor.charAt(k + 1 ) == " "))  { return (false); }  
		     if ((k = svalor.length) && (svalor.charAt(k - 1 ) == " "))  {    return (false);   }  
             if ((svalor.charAt(k + 1 ) == " ")  || (svalor.charAt(k - 1 ) == " ")) {      return (false);    }   
	   	     nblanco++;
	   	     if (nblanco > blancos)  {  return (false);  }
	   	 }  	   
	   	if ((chr == "@") && (k == 0 || k == (svalor.length - 1)))  {  return (false);  }
	   	if (chr == "@")  	   {   
	   	     narr++; 
	   	     if ((narr > 1) || (narr <= 0))    {       return (false);	       }
	    }
	   	if (chr == ".") 	   	   {   	   	     npunto = 1;	   	   }  
     }	
     if (npunto == 0 && nemail == 1)   	  {    	   	return (false);	  }  
     if (narr == 0 && nemail == 1)   	  {    	   	return (false);	  }  
	 
	 //avoid sql conflict
	 if (svalor.indexOf("'") > 0) {return(false);}
	 
     return (true);   	 
}
         
         
function longitud(xvar) { return xvar.length;}

function valid_number(num) {
   var chr = "";
   var numeros = "0123456789";
   for (i=0; i < num.length; i++) {
      chr=num.charAt(i);	
      if (numeros.indexOf(chr) < 0 )  { return (false); } 
   }
   return (true);	
} 


function valid_monto(num){
   var chr = "";
   var numeros = "0123456789.";
   var npunto  = 0;
   for (i=0; i < num.length; i++) {
      chr=num.charAt(i);	
      if (numeros.indexOf(chr) < 0 ) {  return (false); } 
      if (chr == "."  || chr == ",") {  npunto = npunto  + 1; }    
      if (npunto  > 1) {  return (false); }       
   }
   return (true);	
} 


function valid_Tel(tel) {
   var chr = "";
   var numeros = "0123456789-";
   for (i=0; i < tel.length; i++) {
      chr=tel.charAt(i);	
      if (numeros.indexOf(chr) < 0 ) { return (false); }    
   }
   return (true);	
} 



function cdate(fecha,formato) {
   var sFecha
   if (formato==1) {
      sFecha=fecha.substring(6,10) +fecha.substring(3,5) +fecha.substring(0,2);	
   } else {
      sFecha=fecha.substring(6,10) +fecha.substring(0,2) +fecha.substring(3,5);
   }	
   return (sFecha);	
} 

 /*
function trim(svalor) {
   var sblanco = " ";
   for (k=0; k < svalor.length; k++) {
      chr=svalor.charAt(k);	
      if (chr == " ") { sblanco = svalor.substring((k+1),(svalor.length-1)); }
      else  {  break; }
   }	
   if (sblanco.length > 1)  { svalor = sblanco; }
   for (t=svalor.length ; t > 0;t--) { 
      if (svalor.substring((t - 1),t) != " ") { svalor = svalor.substring(0,t);	 break;	 }
   }
   return (svalor);
}
*/

function trim(value) {
   var temp = value;
   var obj = /^(\s*)([\W\w]*)(\b\s*$)/;
   if (obj.test(temp)) { temp = temp.replace(obj, '$2'); }
   var obj = / +/g;
   temp = temp.replace(obj, " ");
   if (temp == " ") { temp = ""; }
   return temp;
}

function valid_fecha(fecha,formato) {	 
   var template = "99/99/9999" ;
   if (fecha == "")  {  return (false);	 }
   if (fecha.length < 10) {  return (false); }
   for (j=0; j < fecha.length; j++) {
      chr=fecha.charAt(j);
      chr_tpl=template.charAt(j)	; 
      if (chr_tpl == "9" && !valid_number(chr))	 { return (false); }
      if (chr_tpl == "/"  && !(chr == chr_tpl)) { return (false); }
   }
   if (formato==1) {
      sMonth = parseInt(fecha.substring(3,5),10);
      sDay   = parseInt(fecha.substring(0,3),10);
   } else {
      sMonth = parseInt(fecha.substring(0,3),10);
      sDay   = parseInt(fecha.substring(3,5),10);
   }
   sYear = parseInt(fecha.substring(6,10),10)
   
   if ( sMonth > 12 )  { return (false); }
   if ( sDay > 31 ) { return (false); }
   if ( sYear < 1000 ) { return (false); }   
      
   if ((sMonth == "04" || sMonth == "06" || sMonth == "09" || sMonth == "11") && sDay > 30 )  {  return (false); }
   if (sMonth == "02")  { 
      nTemp = (sYear / 4);
      nTemp2 = Math.floor(nTemp);
      if ((nTemp - nTemp2) != 0) {  if (sDay > 28) {  return (false); } }           
      else { if (sDay > 29) { return (false);  }   }    
   }                
   return (true);
}
         

function valid_hora(hora){	 
   var template = "99:99" ;
   if (hora == "") { return (false); }
   if (hora.length < 5)  { return (false); }
   for (j=0; j < hora.length; j++) {
      chr=hora.charAt(j);
      chr_tpl=template.charAt(j)	; 
      if (chr_tpl == "9" && !valid_number(chr)) { return (false); }
      if (chr_tpl == ":"  && !(chr == chr_tpl)) { return (false); }
   }
   if ( parseInt(hora.substring(0,2),10) > 23 ) { return (false); }
   if ( parseInt(hora.substring(3,10),10) > 59 ) { return (false); }
   return (true);
}          




/**********************************
function valida(theForm)
{
if (!valid_fecha(theForm.ffecha.value)) 
    {
      alert("Fecha inválida. Incluya dd/mm/yyyy ");
      theForm.ffecha.focus();
      theForm.ffecha.select();
      return (false);
    }
if (!valid_hora(theForm.hora.value)) 
    {
      alert("Hora inválida. Incluya hh:mm en formato de 24 horas");
      theForm.hora.focus();
      theForm.hora.select();
      return (false);
    }
if (theForm.titulo.value == "" || longitud(theForm.titulo.value)  < 2  )
   {
      alert("El titulo suministrado es inválido");
      theForm.titulo.focus();
      theForm.titulo.select();
      return (false);
    }
if (!valid_number(theForm.sinopsis.value) )
   {
      alert("El campo sinopsis debe ser numerico");
      theForm.sinopsis.focus();
      theForm.sinopsis.select();
      return (false);
    } 

    theForm.submit();    
    return (true);   	   
}    
**********************************************/

/******************************************************************************************

Function: validateImageExt
Verifica que si se ingresa un nuevo archivo de imagen tenga extension gif o jpg
Si no se ingresa un nuevo archivo de imagen verifica que exista una imagen cargada
Permite indicar si la imagen es requerida o no

*******************************************************************************************/

function validateImageExt (currentImg, newImg, msgBadExt, msgMissingImg, bRequiredImg) {

	if (newImg.value.length > 0) {
		ext = newImg.value.substring(newImg.value.length -4,newImg.value.length);		
		if ((ext.toLowerCase() != '.gif' & ext.toLowerCase() != '.jpg') | (!(newImg.value.length > 4))) {
			alert(msgBadExt);
			newImg.focus();
			return false;
		}
	}
	else {
		if (!(currentImg.value.length > 0) & bRequiredImg) {
			alert(msgMissingImg);
			newImg.focus();
			return false;
		}		
	}
	return true;
}

/******************************************************************************************

Function: validateDocExt
Verifica que si se ingresa un nuevo archivo tenga extension valida dada por extSet
Si no se ingresa un nuevo archivo de imagen verifica que exista una imagen cargada
Permite indicar si la imagen es requerida o no

*******************************************************************************************/


function bValidFileExtension (extSet, currentFile, newFile, msgBadFile, msgMissingFile, bRequiredFile) {

	if (newFile.value == "") {
		if (bRequiredFile & (currentFile.value == "")) {alert(msgMissingFile);newFile.focus();return false;}
		if (currentFile.value != "") {			
				var fileExt = getFileExtension(currentFile.value);
				if (!bContainsFileExtension(extSet,fileExt)) {alert(msgBadFile);currentFile.focus();return false;}
		}
	}
	else {				
		var fileExt = getFileExtension(newFile.value);
		if (!bContainsFileExtension(extSet,fileExt)) {alert(msgBadFile);newFile.focus();return false;}
	}
	
	return true;
}


function bValidFileExtensionR (extSet, currentFile, newFile, msgBadFile, msgMissingFile, bRequiredFile) {

	if (newFile.value == "") {
		if (bRequiredFile) {
			if (currentFile.value == "") {alert(msgMissingFile);newFile.focus();return false;}
			else {					
				var fileExt = getFileExtension(currentFile.value);
				if (bContainsFileExtension(extSet,fileExt)) {alert(msgBadFile);currentFile.focus();return false;}				
			}
		}
	}
	else {
		var fileExt = getFileExtension(newFile.value);
		if (bContainsFileExtension(extSet,fileExt)) {alert(msgBadFile);newFile.focus();return false;}
	}
	return true;
}

function getFileExtension(fileName) {
	splitFileName = fileName.split(".");
	if (splitFileName.length > 1) {	//en caso de no tener extension retorna vacio
		return "." + splitFileName[splitFileName.length - 1];
	} else {
	
		return "";
	}	
}

function bContainsFileExtension (extSet, fileExt)  {
			
	var extArray = extSet.split(";");
	for (var i=0;i<extArray.length;i++) { if (fileExt.toLowerCase() == extArray[i] ) {return true} }

	return false;
}


function selproducto(pid,nombre)
{
pagina="selProducto.asp?pid="+pid+"&c="+nombre;
win2=window.open(pagina,"","width=400,height=350,scrollbars");
win2.creator=self;
}
 
function modopenwindow(url,name)
{ 
  msgWindow=window.open(url,name,"dependent=yes,alwaysraised=no,toolbar=no,resizable=no,width=220,height=255");
  msgWindow.creator=self;
  msgWindow.focus();
}


function validar_email(valor)
{
	// creamos nuestra regla con expresiones regulares.
	var filter = /[\w-\.]{3,}@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}/;
	// utilizamos test para comprobar si el parametro valor cumple la regla
	if(filter.test(valor))
		return true;
	else
		return false;
}


function validar_tlf(valor)
{
	// creamos nuestra regla con expresiones regulares.
	var filter = /^[0-9]+$/;
	// utilizamos test para comprobar si el parametro valor cumple la regla
	if(filter.test(valor))
		return true;
	else
		return false;
}



function validacorreo(caja)
{
	var datos
	datos = $("#txt_correo").val();
	
	if (datos != "")
	{	
		//var s = document.frm_operador.txt_correooperador.value
		//var filter=/^[A-Za-z][A-Za-z0-9_]*@[A-Za-z0-9_]+\.[A-Za-z0-9_.]+[A-za-z]$/;
		var filter=/^[\w-\.]{3,}@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}$/;
		if (datos.length == 0 )
		{		
			return true;
		}
		if (filter.test(datos))
		{
				return true;
		}
		else
		{
				//alert("Ingrese una direcci\u00f3n de correo v\u00e1lida");		
				mensaje = "Ingrese una direcci\u00f3n de correo v\u00e1lida ...";
				setTimeout(function(){
				$.Notify({style: {background: 'red', color: 'white'}, caption: '<font size="6"><b><u>Error!!!</u></b></font>', width: 200, height: 100, timeout: 8000, content: "<font size='6'>"+mensaje+"</font>"});
				}, 700);						
				
				
				$("#txt_correo").focus();
				return false;
		}
	}
}


function validar_email(valor)
{
	// creamos nuestra regla con expresiones regulares.
	var filter = /[\w-\.]{3,}@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}/;
	// utilizamos test para comprobar si el parametro valor cumple la regla
	if(filter.test(valor))
		return true;
	else
		return false;
}


function validateEmail(emailaddress)
{ 
	var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/; 
	if(!emailReg.test(emailaddress)) 
	{ 
		alert("Please enter valid email id"); 
	} 
} 

/*****************************************************************************
Código para colocar los indicadores de miles  y decimales mientras se escribe
Script creado por Tunait!
Si quieres usar este script en tu sitio eres libre de hacerlo con la condición de que permanezcan intactas estas líneas, osea, los créditos.
http://javascript.tunait.com
tunait@yahoo.com  27/Julio/03
--------------------
MODIFICADO POR WILMAR ALASTRE 
FECHA 02/04/2013
******************************************************************************/
function puntitos(donde,caracter,campo,e)
{

var whichCode = (window.Event) ? e.which : e.keyCode; 
if (whichCode == 13) return true; // Enter
if (whichCode == 8) return true; // Return
if (whichCode == 127) return true; // Suprimir

var decimales = true
dec = campo
pat = /[\*,\+,\(,\),\?,\\,\$,\[,\],\^]/
valor = donde.value
largo = valor.length
crtr = true
if(isNaN(caracter) || pat.test(caracter) == true)
	{
	if (pat.test(caracter)==true) 
		{caracter = "\\" + caracter}
	carcter = new RegExp(caracter,"g")
	valor = valor.replace(carcter,"")
	donde.value = valor
	crtr = false
	}
else
	{
	var nums = new Array()
	cont = 0
	for(m=0;m<largo;m++)
		{
		if(valor.charAt(m) == "." || valor.charAt(m) == " " || valor.charAt(m) == ",")
			{continue;}
		else{
			nums[cont] = valor.charAt(m)
			cont++
			}
		
		}
	}

if(decimales == true) {
	ctdd = eval(1 + dec);
	nmrs = 1
	}
else {
	ctdd = 1; nmrs = 3
	}
var cad1="",cad2="",cad3="",tres=0
if(largo > nmrs && crtr == true)
	{
	for (k=nums.length-ctdd;k>=0;k--){
		cad1 = nums[k]
		cad2 = cad1 + cad2
		tres++
		if((tres%3) == 0){
			if(k!=0){
				cad2 = "." + cad2
				}
			}
		}
		
	for (dd = dec; dd > 0; dd--)	
	{cad3 += nums[nums.length-dd] }
	if(decimales == true)
	{cad2 += "," + cad3}
	 donde.value = cad2
	}
donde.focus()
}

	//phoneNumber = /^[0-9-()+]{3,20}/;	
	function ventana_emergente(mensaje,icon)
	{
		switch (icon)
		{
		case 1:
		  img = 'ui-icon-circle-close';
		  break;
		case 2:
		  img = 'ui-icon-circle-check';
		  break;
		default:
		  img = 'ui-icon-circle-triangle-e';
		}
		
		dial = $('<div id="div_error" title="Mensaje del Sistema"><p> <span class="ui-icon '+img+'" style="float:left; margin:0 7px 50px 0;"></span>'+mensaje+'</p></div>');
		
		dial.dialog({
				modal: true,
				width: 500,
				height: 300,
				show: "blind",
				hide: "shake",
				buttons:{
				Ok: function() {
					$( this ).dialog( "close" );
							}
				//,Cerrar: function(){}
				}
		});
	}


function formato_numero(numero, decimales, separador_decimal, separador_miles){ // v2007-08-06
    numero=parseFloat(numero);
    if(isNaN(numero)){
        return "";
    }

    if(decimales!==undefined){
        // Redondeamos
        numero=numero.toFixed(decimales);
    }

    // Convertimos el punto en separador_decimal
    numero=numero.toString().replace(".", separador_decimal!==undefined ? separador_decimal : ",");

    if(separador_miles){
        // Añadimos los separadores de miles
        var miles=new RegExp("(-?[0-9]+)([0-9]{3})");
        while(miles.test(numero)) {
            numero=numero.replace(miles, "$1" + separador_miles + "$2");
        }
    }

    return numero;
}

// On keypress event call the following method
    function AlphaNumCheck(e) {
        var charCode = (e.which) ? e.which : e.keyCode;
        if (charCode == 8) return true;

        var keynum;
        var keychar;
        var charcheck = /[a-zA-Z0-9]/;
        if (window.event) // IE
        {
            keynum = e.keyCode;
        }
        else {
            if (e.which) // Netscape/Firefox/Opera
            {
                keynum = e.which;
            }
            else return true;
        }

        keychar = String.fromCharCode(keynum);
        return charcheck.test(keychar);
    }



    function isNumber(evt) {
        var iKeyCode = (evt.which) ? evt.which : evt.keyCode
        if (iKeyCode != 46 && iKeyCode > 31 && (iKeyCode < 48 || iKeyCode > 57))
            return false;

        return true;
    }  