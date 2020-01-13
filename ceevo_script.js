$(document).on("click",".btn-checkout",function(event)
{
	if (getRadioBoxValue("payment[method]") != "ceevopayment"){
		return;
	}
	window.stop();
	event.preventDefault();
	var apikey =  document.getElementById('apikey').value;
	var modde  = document.getElementById('paymode').value;
	var price  = document.getElementById('idprice').value;
	var curr  = document.getElementById('idcurr').value;
	
    var formId = '#co-payment-form';
	  var config = {
		envMode: modde, // LIVE
		receiveTokensEvent: true // support custom event listener 'receiveTokens' to send tokenise data to merchant. default false.
	  };
	  console.log("price="+price);
	 var ceevoPayment = new CeevoPayment(apikey, formId, config);
	//  var widget = ceevoPayment.widget();
    //  var amount = Math.round(price);
    //  var amountTotal =  String(amount);
     var currecny  = String(curr); 
	//  console.log("amount="+amount);
        // if(document.querySelector('tr.last .price')){
		// 	var totalAmount = document.querySelector('tr.last .price').innerHTML;
		// 	totalAmount = totalAmount.replace(',', ".");
		// 	// alert(totalAmount);
        //     amount = totalAmount.replace(/[^0-9.-]+/g,"");
        // } 
        var total = parseFloat(price).toFixed(2);
		console.log("total="+total);
		ceevoPayment.setPrice(String(total));
		ceevoPayment.setCurrency(currecny);
		ceevoPayment.open_widget();

		 // listen custom event when receiveTokensEvent is true
	    document.getElementById('co-payment-form').addEventListener('receiveTokens', function ({ detail }) {
				//console.log('form.eventListener', detail);
				//console.log("test--", detail.card_token)
				document.getElementById("token_hidden_input").value = detail.card_token;
				document.getElementById('session_hidden_input').value = detail.session_id;
				document.getElementById('method_code').value = detail.method_code;
				
				review.save();		
	    });       
 });

 
function  getRadioBoxValue(radioName) 
{ 
      var obj = document.getElementsByName(radioName);
         for(i=0; i<obj.length;i++)  {
         if(obj[i].checked)  { 
             return  obj[i].value; 
         } 
       }     
       return "undefined";    
}