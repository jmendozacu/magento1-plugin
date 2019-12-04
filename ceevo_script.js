$(document).on("click",".btn-checkout",function(event)
{
	
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

	 var ceevoPayment = new CeevoPayment(apikey, formId, config);
	 var widget = ceevoPayment.widget();
     var amount = Math.round(price/100);
     var amountTotal =  String(amount);
     var currecny  = String(curr); 
    
        if(document.querySelector('tr.last .price')){
            var totalAmount = document.querySelector('tr.last .price').innerHTML.replace(/[^0-9.-]+/g,"");
            amount = Math.round(totalAmount/100);
        } 
    
		ceevoPayment.setPrice(String(amount));
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
